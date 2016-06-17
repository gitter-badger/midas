<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use Google2FA;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use App\Helpers\Totp;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => ['logout', 'getLogout', 'postAuthenticate', 'getTotp', 'postTotp', 'postTotpSettings']]);
    }

    public function postAuthenticate(Request $request)
    {
        if (Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password')])) {
            if (Totp::verify($request)) {
                return redirect('/')->with('success', 'Successfully logged in!');
            } else {
                Auth::logout();
                return Totp::error($request);
            }
        }
    }

    public function getTotp()
    {
        if (Auth::check()) {
            if (empty(Auth::user()->get('totp_secret'))) {
                Session::set('2fasecret', Google2FA::generateSecretKey());
                return view('auth.totp', ['secret' => Session::get('2fasecret')]);
            } else {
                return view('auth.totp_configured');
            }
        } else {
            return redirect('/');
        }
    }

    public function postTotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'totp' => 'required',
        ]);

        if (!Google2FA::verifyKey(Session::get('2fasecret'), Input::get('totp'))) {
            $validator->errors()->add('totp', 'Wrong One-Time password!');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            Auth::user()->set('totp_secret', Session::get('2fasecret'));
            Session::set('2fasecret', false);
            return redirect('/')->with('success', 'Successfully configured!');
        }
    }

    public function postTotpSettings(Request $request)
    {
        if (Totp::verify($request, false)) {
            if (!empty(Input::get('reset'))) {
                return redirect('/auth/totp')->with('success', 'Successfully reseted!');
            }
            Auth::user()->set('totp_enable', Input::get('totp_enable'));
            return redirect('/auth/totp')->with('success', 'Successfully saved!');
        } else {
            return Totp::error($request);
        }
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect('/');
    }
}

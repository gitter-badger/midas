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
        $this->middleware($this->guestMiddleware(), ['except' => ['logout', 'getLogout', 'postAuthenticate', 'getTotp', 'postTotp']]);
    }

    public function postAuthenticate()
    {
        if (Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password')])) {
            if (empty(Auth::user()->totp_secret)) {
                return redirect('/auth/totp');
            } else {
                if (!Google2FA::verifyKey(Auth::user()->totp_secret, Input::get('totp'))) {
                    Auth::logout();
                    Session::set('2fasecret', false);
                    return redirect('/auth/login')->withErrors('Wrong One-Time password');
                } else {
                    return redirect('/')->with('success', 'Successfully logged in!');
                }
            }
        }
    }

    public function getTotp()
    {
        if (Auth::check()) {
            if (empty(Auth::user()->totp_secret)) {
                Session::set('2fasecret', Google2FA::generateSecretKey());
                return view('auth.totp', ['secret' => Session::get('2fasecret')]);
            } else {
                return redirect('/');
            }
        } else {
            return redirect('/');
        }
    }

    public function postTotp()
    {
        if (!Google2FA::verifyKey(Session::get('2fasecret'), Input::get('totp'))) {
            return redirect()->back()->withErrors('Wrong One-Time password');
        } else {
            Auth::user()->totp_secret = Session::get('2fasecret');
            Auth::user()->save();
            Session::set('2fasecret', false);
            return redirect('/')->with('success', 'Successfully configured!');
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

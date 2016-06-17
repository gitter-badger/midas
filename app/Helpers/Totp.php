<?php

namespace App\Helpers;

use Validator;
use Auth;
use Google2FA;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class Totp
{
    /**
       * Verify 2FA key.
       * @var Request $request
       * @var bool $check_user_settings
       *
       * @return bool|redirect
       */
      public static function verify(Request $request, $check_user_settings = true)
      {
          if (Auth::user()) {
              $required = true;
              if ($check_user_settings && Auth::user()->get('totp_enable') != 1) {
                  $required = false;
              }

              if ($required) {
                  if (!Google2FA::verifyKey(Auth::user()->get('totp_secret'), Input::get('totp'))) {
                      return false;
                  } else {
                      return true;
                  }
              } else {
                  return true;
              }
          } else {
              return false;
          }
      }

      /**
       * Redirect back with 2fa error
       *
       * @return redirect
       */
      public static function error(Request $request)
      {
          $validator = Validator::make($request->all(), [
              'totp' => 'required',
          ]);

          $validator->errors()->add('totp', 'Wrong One-Time password!');
          return redirect()->back()->withErrors($validator)->withInput();
      }
}

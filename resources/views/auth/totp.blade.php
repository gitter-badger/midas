@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Configuring One-Time password</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/totp') }}">
                        {{ csrf_field() }}

                        <?php
                        $google2fa_url = Google2FA::getQRCodeGoogleUrl(
                                            env('APP_URL', 'MidasMarket'),
                                            Auth::user()->email,
                                            $secret
                                          );

                        ?>

                          <div class="alert alert-info">
                            <p>1. Scan this QR code or enter secret key with your 2FA app(Google Authenticator, Authy, etc..)</p>
                          </div>

                          <div class="form-group">
                              <label for="totp" class="col-md-4 control-label">Your secret key</label>

                              <div class="col-md-6">
                                  <input id="secret" readonly type="text" class="form-control" name="secret" value="{{$secret}}">
                              </div>
                          </div>
                        </div>
                        <p class="alert text-center"><img src="{{$google2fa_url}}"></p>

                        <p class="alert alert-info">2. Input one-time password</p>
                        <div class="form-group{{ $errors->has('totp') ? ' has-error' : '' }}">
                            <label for="totp" class="col-md-4 control-label">One-Time password</label>

                            <div class="col-md-6">
                                <input id="totp" type="text" class="form-control" name="totp">

                                @if ($errors->has('totp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('totp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i> Confirm login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

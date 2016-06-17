@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">One-Time password settings</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/totp-settings') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('totp_enable') ? ' has-error' : '' }}">
                            <div class="col-md-6 col-md-offset-3 text-center">
                              <label>
                                <input type="hidden" name="totp_enable" value="0">
                                <input type="checkbox" name="totp_enable" value="1" @if (Auth::user()->get('totp_enable')) checked @endif> Enable One-Time passwords
                              </label>
                            </div>
                        </div>

                        @include('helpers.totp_confirm')

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i> Save settings
                                </button>

                                <button type="submit" name="reset" value="1" class="btn btn-danger">
                                    <i class="fa fa-btn fa-sign-in"></i> Reset One-Time secret
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

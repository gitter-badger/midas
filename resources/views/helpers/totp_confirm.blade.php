<div class="alert alert-warning form-group{{ $errors->has('totp') ? ' has-error' : '' }}">
    <p class="text-center">Confirm action with One-Time password
      @if (!empty($optional))
        (optional)
      @else
        (required)
      @endif
    </p>
    <div class="col-md-4 col-md-offset-4">
        <input id="totp" type="text" class="form-control" name="totp" @if (empty($optional)) required @endif>

        @if ($errors->has('totp'))
            <span class="help-block">
                <strong>{{ $errors->first('totp') }}</strong>
            </span>
        @endif
    </div>
</div>

@extends('layouts.app')
@section('content')

@php
$CountryCodesJson = file_get_contents(base_path('uploads/CountryCodes.json'));
$CountryCodes = json_decode($CountryCodesJson);
@endphp

<div class="signup-form signin lg-inn">
  <div class="text-center">
    @include('layouts.flash-message')
    <h2>{{ trans('global.sign_in') }}</h2>
    <p>{{ trans('global.login_welcome_msg') }}</p>
  </div>
  <div class="form">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="is_user" value="1">
        <input type="hidden" name="loginUserRole" value="" id="loginUserRole">

          <div class="row g-3 mx-auto mt-2">
            <div class="col">
               <label class="form-label">{{ trans('global.email_or_phone') }}</label>
                <div class="phn-number">
                    <select style="border: 0px solid transparent;" class="form-select" name="isd_code" id="isd_code">
                        @foreach($CountryCodes as $k => $country)
                          <option value="{{$country->dial_code}}" {{ old('isd_code') == $country->dial_code ? 'selected="selected"' : '' }}>{{$country->dial_code}}</option>
                        @endforeach
                        @error('isd_code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </select>
                      <input  type="text" class="form-control" name="email" placeholder="Enter email address/ Phone no" value="{{ old('email') }}" id="email">
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <!-- <div class="col">
               <label class="form-label">Email Address/Phone Number</label>
              <input type="text" class="form-control" name="email" placeholder="Enter email address/ Phone number" value="{{ old('email') }}" aria-label="email">
              @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            </div> -->
            <div class="col password">
                <label class="form-label">{{ trans('global.login_password') }}</label>
              <div class="password">
                <input type="password" class="form-control" name="password" placeholder="Password" aria-label="password">
                <div class="password-lock">
                    <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.4545 6.03428H10.2955V2.19429C10.2955 0.982286 9.31875 0 8.11364 0H3.88636C2.68125 0 1.70455 0.982286 1.70455 2.19429V6.03428H0.545455C0.24375 6.03428 0 6.27943 0 6.58286V13.1657C0 13.4691 0.24375 13.7143 0.545455 13.7143H11.4545C11.7563 13.7143 12 13.4691 12 13.1657V6.58286C12 6.27943 11.7563 6.03428 11.4545 6.03428ZM6.47727 10.0971V11.0057C6.47727 11.0811 6.41591 11.1429 6.34091 11.1429H5.65909C5.58409 11.1429 5.52273 11.0811 5.52273 11.0057V10.0971C5.38202 9.99554 5.277 9.85167 5.22278 9.68622C5.16856 9.52077 5.16793 9.34229 5.22099 9.17646C5.27405 9.01063 5.37806 8.86601 5.51805 8.76342C5.65804 8.66083 5.82679 8.60555 6 8.60555C6.17321 8.60555 6.34196 8.66083 6.48195 8.76342C6.62194 8.86601 6.72595 9.01063 6.77901 9.17646C6.83207 9.34229 6.83145 9.52077 6.77722 9.68622C6.723 9.85167 6.61798 9.99554 6.47727 10.0971ZM9.06818 6.03428H2.93182V2.19429C2.93182 1.66457 3.35966 1.23429 3.88636 1.23429H8.11364C8.64034 1.23429 9.06818 1.66457 9.06818 2.19429V6.03428Z" fill="white"/>
                    </svg>
                        
                </div>
              </div>

              @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
              
            </div>
            
          </div>
         
          <div class="mb-3 form-check agree d-flex">
           <div class="d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-2" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} >
            <label class="form-check-label" for="remember">{{ trans('global.remember_me') }}</label>
           </div>
            @if (Route::has('password.request'))
           <div class="forgt-psd">
               
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ trans('global.forgot_password') }}
                </a>
           
           </div>
            @endif
          </div>
          <div class="button px-2">
            <button type="submit" class="btn btn-light w-100">{{ trans('global.sign_in') }}</button>
          </div>
      </form>

      <div class="google px-2">
        <div class="goole-icon">
            <img src="{{ asset('images/google.svg') }}" alt=""/>
        </div>
        <button type="button" class="btn btn-outline-light w-100" onclick="location.href='{{ url('auth/google') }}'">{{ trans('global.sign_in_google') }}</button>
        <div class="next-arrow">
            <img src="{{ asset('images/next-arrow.svg') }}" alt=""/>
        </div>
      </div>

      <div class="bottom-text text-center">
        <span >{{ trans('global.dont_have_account') }} <a href="{{url('register')}}">{{ trans('global.sign_up') }}</a></span>
      </div>
      
  </div>
</div>

@section('scripts')
@parent

<script>
$(document).ready(function(){
    var loginUserRole = localStorage.getItem('loginUserRole') ? localStorage.getItem('loginUserRole') : 'client';
    $("#loginUserRole").val(loginUserRole);

    $('#email').on('keypress', function(event) {
        //var re = /([A-Z0-9a-z_-][^@])+?@[^$#<>?]+?\.[\w]{2,4}/.test(this.value);
        if(event.which > 47 && event.which < 58) {
            $("#isd_code").show();
            $(this).css("width", "66%");
        } else {
            $("#isd_code").hide();
            $(this).css("width", "100%");
        }
    })
});
</script>
@endsection

@endsection

@extends($activeTemplate.'layouts.frontend')
@section('content')

    
@php
    $signup = getContent('signup.content',true)->data_values;
    $elements = getContent('signup.element',false,'',1);
@endphp
    <section class="pt-100 pb-100">
        <div class="container">
          <div class="row justify-content-center">
            <div class=" col-xl-12">
              <div class="account-wrapper">
                <div class="left bg_img" style="background-image: url({{getImage('assets/images/frontend/signup/'.$signup->background_image,'1080x700')}});">
                  <div>
                    <h3 class="mb-3 text-white">{{__($signup->instruction_title)}}</h3>
                    <p class="text-white">{{__($signup->instruction_details)}}</p>
  
                    <ul class="square-list mt-4 text-white">
                        @foreach ($elements as $el)
                        <li>
                          <p>{{__($el->data_values->instruction)}}</p>
                        </li>
                            
                        @endforeach
                    </ul>
                  </div>
                </div>
                <div class="right">
                  <h3 class="title">Registration as a legal entity</h3>
                  <form action="{{ route('user.register_legal') }}" method="POST" onsubmit="return submitUserForm();">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="businessname">Business name</label>
                                <input id="businessname" type="text" class="form--control" name="businessname" placeholder="Business name" value="{{ old('businessname') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                 <input id="description" type="text" class="form--control" name="description" placeholder="Description" value="{{ old('description') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="VATnumber">VAT number</label>
                                <input id="VATnumber" type="number" class="form--control" name="VATnumber" placeholder="IT00.." value="{{ old('VATnumber') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name of the legal representative</label>
                                <input id="firstName" type="text" class="form--control" placeholder="Name of the legal representative" name="firstName" value="{{ old('firstName') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Surname of the legal representative</label>
                                <input id="lastName" type="text" class="form--control" name="lastName" placeholder="Surname of the legal representative" value="{{ old('lastName') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nationality of the legal representative</label>
                                <input id="nationalityOfLegal" type="text" class="form--control" placeholder="Italia" name="Lcountry" value="{{ old('Lcountry') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Country of residence of the legal representative</label>
                                <input id="RLcountry" type="text" class="form--control" name="RLcountry" placeholder="Italia" value="{{ old('RLcountry') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of birth of the legal representative</label>
                                <input id="RLbirthday" type="date" class="form--control" placeholder="" name="RLbirthday" value="{{ old('RLbirthday') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Company phone number</label>
                                <input id="Cphone" type="number" class="form--control" name="Cphone" placeholder="Company phone number" value="{{ old('Cphone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Legal telephone number</label>
                                <input id="Lphone" type="number" class="form--control" placeholder="Legal telephone number" name="Lphone" value="{{ old('Lphone') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email - Login</label>
                                <input id="Lemail" type="email" class="form--control" placeholder="Email for Login" name="Lemail" value="{{ old('Lemail') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email of the legal representative</label>
                                <input id="RLemail" type="email" class="form--control" name="RLemail" placeholder="Email of the legal representative" value="{{ old('RLemail') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="password">@lang('Password')</label>
                            <input id="password" type="password" class="form--control" placeholder="@lang('Password')" name="password" required autocomplete="new-password">
                            @if($general->secure_password)
                            <div class="progress mt-2">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-danger my-1 capital">@lang('Minimum 1 capital letter is required')</p>
                            <p class="text-danger my-1 number">@lang('Minimum 1 number is required')</p>
                            <p class="text-danger my-1 special">@lang('Minimum 1 special character is required')</p>
                            @endif
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="password-confirm">@lang('Confirm Password')</label>
                            <input id="password-confirm" type="password" class="form--control" placeholder="@lang('Confirm password')" name="password_confirmation" required autocomplete="new-password">
                        </div>
                        </div>
                    </div>

                    @include($activeTemplate.'partials.custom-captcha')
                    <div class="form-group mt-4">
                         @php echo loadReCaptcha() @endphp
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn--base btn-md w-100">@lang('Sign Up')</button>
                    </div>
                    <p class="font-size--14px text-center">@lang('Have an account?') <a class="text--base" href="{{route('user.login')}}">@lang('Login here').</a></p>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

    
@endsection
@push('style')
<style type="text/css">
    .country-code .input-group-prepend .input-group-text{
        background: #fff !important;
    }
    .country-code select{
        border: none;
    }
    .country-code select:focus{
        border: none;
        outline: none;
    }
</style>
@endpush
@push('script')
    <script>
      "use strict";
      
        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span style="color:red;">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }

        function verifyCaptcha() {
            document.getElementById('g-recaptcha-error').innerHTML = '';
        }
        @if($general->secure_password)
            $('input[name=password]').on('input',function(){
                var password = $(this).val();
                var width = 25;
                var capital = /[ABCDEFGHIJKLMNOPQRSTUVWXYZ]/;
                var capital = capital.test(password);
                if (!capital){
                    $('.capital').removeClass('d-none');
                }else{
                    width += 25;
                    $('.capital').addClass('d-none');
                }
                var number = /[123456790]/;
                var number = number.test(password);
                if (!number){
                    $('.number').removeClass('d-none');
                }else{
                    width += 25;
                    $('.number').addClass('d-none');
                }
                var special = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
                var special = special.test(password);
                if (!special){
                    $('.special').removeClass('d-none');
                }else{
                    width += 25;
                    $('.special').addClass('d-none');
                }

                if (width == 25) {
                    var bg = 'red';
                    var msg = 'Too Week'
                }else if(width == 50){
                    var msg = 'Week'
                    var bg = '#D7A612';
                }else if(width == 75){
                    var msg = 'Medium'
                    var bg = '#5210BF';
                }else if(width == 100){
                    var msg = 'Strong'
                    var bg = 'green';
                }
                $('.progress-bar').attr('style',`width:${width}%;background:${bg};`);
                $('.progress-bar').text(msg);
            });
        @endif

    </script>
@endpush

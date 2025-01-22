@extends('frontend.layouts.auth')
@php
    $defualt = get_default_language_code()??'en';
    $register_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::REGISTER_SECTION);
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $register = App\Models\Admin\SiteSections::getData($register_slug)->first();
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();

@endphp
@section('content')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Account
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="account-section bg-overlay-base bg_img" data-background="{{ get_image(@$register->value->images->register_image,'site-section') }}">
    <div class="account-shape">
        <img src="{{ asset('public/frontend/') }}/images/banner/bottom-shape.png" alt="shape">
    </div>
    <div class="right float-end">
        <div class="account-header text-center">
            <a class="site-logo" href="{{ setRoute('index') }}">
                <img src="{{ get_logo($basic_settings) }}"  data-white_img="{{ get_logo($basic_settings,'white') }}"
                data-dark_img="{{ get_logo($basic_settings,'dark') }}"
                    alt="site-logo">
            </a>
        </div>
        <div class="account-middle">
            <div class="account-form-area">
                <h3 class="title">{{ @$register->value->language->$defualt->heading }}</h3>
                <p>{{ @$register->value->language->$defualt->sub_heading }}</p>
                <form class="account-form" action="{{ setRoute('user.register.submit') }}" method="POST">
                    @csrf
                    <div class="row ml-b-20">
                        <div class="col-lg-6 form-group">
                            <input type="text" placeholder="{{ __("first Name") }}" name="firstname" class="form--control" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="col-lg-6 form-group">
                            <input type="text" placeholder="{{ __("last Name") }}" name="lastname" class="form--control" value="{{ old('lastname') }}" required>
                        </div>
                        <div class="col-lg-12 form-group">
                            <input type="email" placeholder="{{ __("Email") }}" name="email" class="form--control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-lg-12 form-group" id="show_hide_password">
                            <input type="password" class="form--control" name="password" placeholder="{{ __("password") }}" required>
                            <a href="" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                        @if (@$basic_settings->agree_policy == 1)
                            <div class="col-lg-12 form-group">
                                <div class="form-group custom-check-group mb-0">
                                    <input type="checkbox" id="level-1" name="agree">
                                    <label for="level-1" class="mb-0">{{ __("I have read agreed with the") }} <a href="{{ url(@$register->value->agree_policy_link) }}" class="fw-bold">{{ @$register->value->language->$defualt->agree_policy_title }}</a></label>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-12 form-group text-center">
                            <button type="submit" class="btn--base w-100">{{ __('Register Now') }}</button>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="account-item mt-10">
                                <label>{{ __('Already Have An Account') }}? <a href="{{ route('user.login') }}" class="fw-bold">{{ __('Login Now') }} </a></label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="account-footer text-center">
            <p>{{ __(@$footer->value->language->$defualt->footer_text) }} <a href="{{route('index')}}" class="fw-bold">{{ $basic_settings->site_name }}</a></p>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Account
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection

@push('script')
    <script>
        getAllCountries("{{ setRoute('global.countries') }}",$(".country-select"));
        $(document).ready(function(){
            $("select[name=country]").change(function(){
                var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCode);
            });

            setTimeout(() => {
                var phoneCodeOnload = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCodeOnload);
            }, 400);
        });
    </script>

@endpush

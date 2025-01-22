
@extends('frontend.layouts.auth')

@php
    $defualt = get_default_language_code()??'en';
    $login_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::LOGIN_SECTION);
    $login = App\Models\Admin\SiteSections::getData($login_slug)->first();
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
@endphp
@section('content')
   <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Account
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="account-section bg-overlay-base bg_img" data-background="{{ get_image(@$login->value->images->login_image,'site-section') }}">
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
                <h3 class="title">{{ @$login->value->language->$defualt->heading }}</h3>
                <p>{{ @$login->value->language->$defualt->sub_heading }}</p>
                <form action="{{ setRoute('user.login.submit') }}" method="POST">
                    @csrf
                    <div class="row ml-b-20">
                        <div class="col-lg-12 form-group">
                            <input type="text" placeholder="{{ __('email Address') }}" name="credentials" class="form--control" value="" required>
                        </div>
                        <div class="col-lg-12 form-group" id="show_hide_password">
                            <input type="password" class="form--control" name="password" placeholder="{{ __('password') }}">
                            <a href="" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-lg-12 form-group">
                            <div class="forgot-item">
                                <label><a href="{{ setRoute('user.password.forgot') }}" class="fw-bold">{{ __('Forgot Password') }}?</a></label>
                            </div>
                        </div>
                        <div class="col-lg-12 form-group text-center">
                            <button type="submit" class="btn--base w-100">{{ __('Login Now') }}</button>
                        </div>
                        <div class="or-area">
                            <span class="or-line"></span>
                            <span class="or-title">{{ __('Or') }}</span>
                            <span class="or-line"></span>
                        </div>
                        @if (@$basic_settings->user_registration == 1)
                            <div class="col-lg-12 text-center">
                                <div class="account-item mt-10">
                                    <label>
                                        {{ __("don THave Account") }}  <a href="{{ setRoute('user.register') }}" class="fw-bold">{{ __("Register Now") }}</a>
                                        </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <div class="account-footer text-center">
            <p>{{ __(@$footer->value->language->$defualt->footer_text) }} <a href="{{route('index')}}" class="fw-bold">{{ $basic_settings->site_name }}.</a></p>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Account
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection

@push('script')

@endpush

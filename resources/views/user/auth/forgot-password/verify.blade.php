@extends('layouts.master')


@php
    $defualt = get_default_language_code()??'en';
    $auth_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::LOGIN_SECTION);
    $auth = App\Models\Admin\SiteSections::getData( $auth_slug)->first();
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
@endphp
@section('content')
    <section class="account-section bg_img" data-background="{{ get_image(@$auth->value->images->login_image,'site-section') }}">
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
                    <h3 class="title">{{ __("OTP Verification") }}</h3>
                    <p>{{ __("Please check your email address to get the OTP (One time password).") }}</p>
                    <form action="{{ setRoute('user.password.forgot.verify.code',$token) }}" class="account-form" method="POST">
                        @csrf
                        <div class="row ml-b-20">
                            <div class="col-lg-12 form-group">
                                <input type="text" placeholder="{{ __("Enter Verification Code") }}" name="code" class="form--control" value="{{ old('code') }}" required>
                            </div>
                            <div class="col-lg-12 form-group">
                                <div class="forgot-item">
                                    <label>{{ __("Don't get code") }}?<a href="{{ setRoute('user.password.forgot.resend.code',$token) }}" class="text--base">{{ __("Resend") }}</a></label>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group text-center">
                                <button type="submit" class="btn--base w-100">{{ __("Verify") }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="account-footer text-center">
                <p>{{ __(@$footer->value->language->$defualt->footer_text) }} <a href="{{route('index')}}" class="text--base">{{ $basic_settings->site_name }}.</a></p>
            </div>
        </div>
    </section>
@endsection

@push('script')

@endpush

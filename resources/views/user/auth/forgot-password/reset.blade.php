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
                    <h3 class="title">{{ __("Password Reset") }}</h3>
                    <p>{{ __("Reset your password") }}</p>
                    <form action="{{ setRoute('user.password.reset',$token) }}" class="account-form" method="POST">
                        @csrf
                        <div class="row ml-b-20">
                            <div class="col-lg-12 form-group">
                                <input type="password" placeholder="{{ __("enter New Password") }}" name="password" class="form--control" required>
                            </div>
                            <div class="col-lg-12 form-group">
                                <input type="password" placeholder="{{ __("enter Confirm Password") }}" name="password_confirmation" class="form--control" required>
                            </div>
                            <div class="col-lg-12 form-group">
                                <div class="forgot-item">
                                    <label><a href="{{ setRoute('user.login') }}" class="text--base">{{ __("Login Now") }}</a></label>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group text-center">
                                <button type="submit" class="btn--base w-100">{{ __("Reset") }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="account-footer text-center">
                <p>{{ __(@$footer->value->language->$defualt->footer_text) }} <a href="{{route('index')}}" class="text--base">{{ $basic_settings->site_name }}</a></p>
            </div>
        </div>
    </section>
@endsection

@push('script')

@endpush

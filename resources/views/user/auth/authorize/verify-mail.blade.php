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
                    <h3 class="title">{{ __("Account Authorization") }}</h3>
                    <p>{{ __("Need to verify your account. Please check your mail inbox to get the authorization code") }}</p>
                    <form action="{{ setRoute('user.authorize.mail.verify',$token) }}" class="account-form" method="POST">
                        @csrf
                        <div class="row ml-b-20">
                            <div class="col-lg-12 form-group">
                                <input type="text" placeholder="Enter Verification Code" name="code" class="form--control" value="{{ old('code') }}" required>
                            </div>
                            <div class="col-lg-12 form-group">
                                <div class="forgot-item">
                                    <div class="form-group text-end">
                                        <div class="time-area">{{ __('You can resend the code after') }} <span id="time"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group text-center">
                                <button type="submit" class="btn--base w-100">{{ __("Authorize") }}</button>
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


<script>
    var resendTime = "{{ $resend_time ?? 0 }}";
    var resendCodeLink = "{{ setRoute('user.authorize.mail.resend',$token) }}";

    function resetTime (second = 20) {
        var coundDownSec = second;
        var countDownDate = new Date();
        countDownDate.setMinutes(countDownDate.getMinutes() + 120);
        var x = setInterval(function () {  // Get today's date and time
            var now = new Date().getTime();  // Find the distance between now and the count down date
            var distance = countDownDate - now;  // Time calculations for days, hours, minutes and seconds  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * coundDownSec)) / (1000 * coundDownSec));
            var seconds = Math.floor((distance % (1000 * coundDownSec)) / 1000);  // Output the result in an element with id="time"
            document.getElementById("time").innerHTML =second + "s ";  // If the count down is over, write some text
            if (distance <= 0 || second <= 0 ) {
                // alert();
                clearInterval(x);
                // document.getElementById("time").innerHTML = "RESEND";
                document.querySelector(".time-area").innerHTML = `Didn't get the code? <a class='text--danger' href='${resendCodeLink}'>Resend</a>`;
            }
            second--
        }, 1000);
    }
    resetTime(resendTime);
</script>

@endpush

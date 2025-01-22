@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
    $contact_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::CONTACT_SECTION);
    $contact = App\Models\Admin\SiteSections::getData( $contact_slug)->first();
@endphp

@section('content')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@include('frontend.partials.breadcrumb')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Contact
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div class="contact-section pt-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="contact-form-area">
                    <form class="contact-form" id="contact-form">
                        @csrf
                        <div class="row justify-content-center mb-10-none">
                            <div class="col-xl-6 col-lg-6 col-md-12 form-group">
                                <input type="text" name="name" class="form--control" placeholder="{{__('Enter your name')}}*" required>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 form-group">
                                <input type="email" name="email" class="form--control" placeholder="{{__('enter Email')}}*" required>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 form-group">
                                <input type="number" name="mobile" class="form--control" placeholder="{{__('Enter your number')}}*" required>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 form-group">
                                <input type="text" name="subject" class="form--control" placeholder="{{__('Subject')}}*" required>
                            </div>
                            <div class="col-lg-12 form-group">
                                <textarea class="form--control" name="message" placeholder="{{__('Enter your message')}}*"></textarea>
                            </div>
                            <div class="col-lg-12 form-group text-center">
                                <button type="submit" class="btn--base mt-40"><i class="fa fa-spinner d-none fa-pulse fa-fw"></i> {{ __('Send Message') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Contact
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->



<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Contact
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="contact-section ptb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="section-header">
                    {{-- <span class="section-sub-title"> {{ __(@$contact->value->language->$defualt->title ??@$contact->value->language->$default_lng->title) }}  </span> --}}
                     <h2 class="section-title">{{ @$contact->value->language->$defualt->heading ?? @$contact->value->language->$default_lng->heading }}</span></h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                <div class="contact-widget">
                    <div class="contact-item-icon">
                        <i class="las la-map"></i>
                    </div>
                    <div class="contact-item-content">
                        <h3 class="title">{{ __('Our Location') }}</h3>
                        <span class="sub-title">{{ __(@$contact->value->language->$defualt->location ??@$contact->value->language->$default_lng->location) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                <div class="contact-widget">
                    <div class="contact-item-icon">
                        <i class="las la-phone-volume"></i>
                    </div>
                    <div class="contact-item-content">
                        <h3 class="title">{{ __('Call us on') }}: {{ __(@$contact->value->language->$defualt->phone ?? @$contact->value->language->$default_lng->phone) }}</h3>
                        <span class="sub-title">{{ __(@$contact->value->language->$defualt->office_hours ?? @$contact->value->language->$default_lng->office_hours) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                <div class="contact-widget">
                    <div class="contact-item-icon">
                        <i class="las la-envelope"></i>
                    </div>
                    <div class="contact-item-content">
                        <h3 class="title">{{ __('Email us directly') }}</h3>
                        <span class="sub-title">{{ __(@$contact->value->language->$defualt->email ??@$contact->value->language->$default_lng->email) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Contact
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
    $download_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::DOWNLOAD_SECTION);
    $download = App\Models\Admin\SiteSections::getData( $download_slug)->first();
    $app_settings = App\Models\Admin\AppSettings::first();
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
    Start app-subscribe
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="app-subscribe-section ptb-120">

    <div class="container">
        <div class="row align-items-center mb-30-none">
            <div class="col-xl-6 col-lg-12 mb-30">
                <div class="app-content">
                    <span class="sub-title">{{ __(@$download->value->language->$defualt->title ?? @$download->value->language->$default_lng->title) }}</span>
                    <h2 class="title">{{ __(@$download->value->language->$defualt->heading ??@$download->value->language->$default_lng->heading) }}</h2>
                    <p>{{ __(@$download->value->language->$defualt->sub_heading ?? @$download->value->language->$default_lng->sub_heading) }}</p>

                    <div class="app-btn">
                        <a href="{{ @$app_settings->android_url }}" target="_blank"><img src="{{ get_image(@$download->value->images->google_play,'site-section') }}" alt="app"></a>

                        <a href="{{ @$app_settings->iso_url }}" target="_blank"><img src="{{ get_image(@$download->value->images->app_store,'site-section') }}" alt="app"></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 mb-30">
                <div class="app-thumb pe-3">
                    <img class="" src="{{ get_image(@$download->value->images->play_store_qr_image,'site-section') }}" alt="phone">
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 mb-30">
                <div class="app-thumb ps-3">
                    <img src="{{ get_image(@$download->value->images->app_store_qr_image,'site-section') }}" alt="phone">
                </div>
            </div>
        </div>
    </div>
</section>

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End app-subscribe
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Brand
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
{{-- @include('frontend.partials.brand') --}}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Brand
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


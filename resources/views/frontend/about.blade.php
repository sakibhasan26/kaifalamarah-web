
@extends('frontend.layouts.master')

@php
   $defualt = get_default_language_code()??'en';
    //    $default_lng = 'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
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
    Start About
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="about-section pt-120">
    <div class="about-shape">
        <img src="{{ asset('public/frontend/') }}/images/about/right-shape.png" alt="shape">
    </div>
    <div class="container">
        <div class="row justify-content-center align-items-center mb-30-none">
            <div class="col-xl-6 col-lg-6 mb-30">
                <div class="about-thumb">
                    <img src="{{ get_image(@$about->value->images->first_section_image,'site-section') }}" alt="about">
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 mb-30">
                <div class="about-content">
                    <div class="sub-title">{{ __(@$about->value->language->$defualt->fitst_section_title??@$about->value->language->$default_lng->fitst_section_title) }}</div>
                    <h2 class="title">{{ __( @$about->value->language->$defualt->fitst_section_heading ?? @$about->value->language->$default_lng->fitst_section_heading) }}</h2>
                    <p>{!! __(@$about->value->language->$defualt->first_section_sub_heading ?? @$about->value->language->$default_lng->first_section_sub_heading) !!}</p>
                    <div class="about-btn">
                        <a href="{{ url(@$about->value->language->$default_lng->first_section_button_link) }}" class="btn--base"><i class="las la-heart"></i> {{ __(@$about->value->language->$defualt->first_section_button_name??@$about->value->language->$default_lng->first_section_button_name) }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End About
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start About
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="about-section pt-120">
    <div class="container">
        <div class="row justify-content-center align-items-center mb-30-none">
            <div class="col-xl-6 col-lg-6 mb-30">
                <div class="about-content">
                    {{-- <div class="sub-title">{{ __(@$about->value->language->$defualt->second_section_title ?? @$about->value->language->$default_lng->second_section_title) }}</div> --}}
                    <h2 class="title">{{ __(@$about->value->language->$defualt->second_section_heading ?? @$about->value->language->$default_lng->second_section_heading) }}</h2>
                    <p>{{ __(@$about->value->language->$defualt->second_section_sub_heading??@$about->value->language->$default_lng->second_section_sub_heading) }}</p>
                    <ul class="about-list">
                        @if(isset($about->value->items))
                            @foreach($about->value->items ?? [] as $key => $item)
                                <li> <a href="{{ @$item->language->$defualt->link ?? @$item->language->$default_lng->link }}">{{__( @$item->language->$defualt->title ?? @$item->language->$default_lng->title) }}</a> </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 mb-30">
                <div class="about-thumb two">
                    <img src="{{ get_image(@$about->value->images->second_section_image,'site-section') }}" alt="about">
                    {{-- <div class="about-thumb-shape">
                        <img src="{{ asset('public/frontend/') }}/images/about/about-shape.png" alt="shape">
                    </div> --}}
                    <div class="about-video">
                        <div class="video-area">
                            <a class="video-icon" data-rel="lightcase:myCollection" href="{{ @$about->value->language->$default_lng->second_section_video_link }}">
                                <img src="{{ asset('public/frontend/') }}/images/play-button.png" alt="play">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End About
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Testimonial
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
{{-- @include('frontend.partials.testimonial') --}}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Testimonial
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Brand
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
{{-- @include('frontend.partials.brand') --}}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Brand
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


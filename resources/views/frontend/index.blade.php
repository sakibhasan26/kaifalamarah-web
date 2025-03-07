@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
    $about_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::ABOUT_SECTION);
    $about = App\Models\Admin\SiteSections::getData( $about_slug)->first();
    $download_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::DOWNLOAD_SECTION);
    $download = App\Models\Admin\SiteSections::getData( $download_slug)->first();
    $video_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::VIDEO_SECTION);
    $video = App\Models\Admin\SiteSections::getData( $video_slug)->first();
    $app_settings = App\Models\Admin\AppSettings::first();
    $service_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::SERVICE_SECTION);
    $service_section = App\Models\Admin\SiteSections::getData($service_slug)->first();
    $subscribe_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::SUBSCRIBE_SECTION);
    $subscribe_section = App\Models\Admin\SiteSections::getData($subscribe_slug)->first();
    $statistics_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::STATISTICS_SECTION);
    $statistics_section = App\Models\Admin\SiteSections::getData($statistics_slug)->first();

    $overview_left_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::OVERVIEW_LEFT_SECTION);
    $overview_left_section = App\Models\Admin\SiteSections::getData($overview_left_slug)->first();

    $overview_right_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::OVERVIEW_RIGHT_SECTION);
    $overview_right_section = App\Models\Admin\SiteSections::getData($overview_right_slug)->first();

    $breadcrumd_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::BREADCRUMB_SECTION);
    $breadcrum = App\Models\Admin\SiteSections::getData( $breadcrumd_slug)->first();

    $idNames = ['software', 'company', 'client'];

@endphp

@push('css')
    <style>


    </style>
@endpush

@section('content')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<section class="banner-section bg-overlay-base bg_img" data-background="{{ get_image(@$breadcrum->value->images->banner_image,'site-section') }}">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-7 col-md-12">
                <div class="banner-content">
                    <h1 class="title">{{__( @$homeBanner->value->language->$defualt->heading ?? @$homeBanner->value->language->$default_lng->heading ) }}</h1>
                    <p>{{ __(@$homeBanner->value->language->$defualt->sub_heading ?? @$homeBanner->value->language->$default_lng->sub_heading) }}</p>
                    <div class="banner-btn">
                        <a href="{{ url($homeBanner->value->language->$defualt->button_link ?? '') }}" class="btn--base white">{{ __(@$homeBanner->value->language->$defualt->button_name) }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="banner-bg">
        <img src="{{ get_image(@$homeBanner->value->images->banner_image,'site-section') }}" alt="banner">
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start About
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
{{-- <section class="about-section pt-120">
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
                    <h2 class="title">{{ __(@$about->value->language->$defualt->fitst_section_heading ?? @$about->value->language->$default_lng->fitst_section_heading) }}</h2>
                    <p>{{ __(@$about->value->language->$defualt->first_section_sub_heading ??@$about->value->language->$default_lng->first_section_sub_heading ) }}</p>
                    <div class="about-btn">
                        <a href="{{ $about->value->language->$default_lng->first_section_button_link ?? '' }}" class="btn--base">{{ $about->value->language->$defualt->first_section_button_name ?? $about->value->language->$default_lng->first_section_button_name}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End About
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->



<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Service
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="service-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">


            @forelse ($service_section?->value?->items ?? [] as $key => $item)
                <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="{{ $item->language?->$defualt?->service_icon ?? $item->language?->$default_lng?->service_icon ?? '' }}"></i>
                        </div>
                        <div class="service-content">
                            <h3 class="title">
                                <a href="
                                    @if ($loop->first)
                                        {{ route('about') }}
                                    @elseif ($loop->iteration == 2)
                                        {{ "#volunteer" }}
                                    @else
                                        {{ route('donation') }}
                                    @endif
                                ">
                                    {{ $item->language?->$defualt?->heading ?? $item->language?->$default_lng?->heading }}
                                </a>
                            </h3>
                            <p>{{ $item->language?->$defualt?->description ?? $item->language?->$default_lng?->description }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center">{{ __('No services available at the moment') }}</p>
            @endforelse


        </div>
    </div>
</section>

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Service
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Call To Action
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="call-to-action-section bg-overlay-base bg_img" data-background="{{ asset('public/frontend/') }}/images/banner/banner-bg.jpg" id="volunteer">
    <div class="call-to-action-bottom-shape">
        <img src="{{ asset('public/frontend/') }}/images/banner/bottom-shape.png" alt="shape">
    </div>
    <div class="container">
        <div class="call-to-action-wrapper">
            <div class="call-to-action-content">
                <h2 class="title"> {{ @$subscribe_section->value->language->$defualt->title ?? @$subscribe_section->value->language->$default_lng->title }}
                    <span class="text--base">
                        {{ @$subscribe_section->value->language->$defualt->sub_title ?? @$subscribe_section->value->language->$default_lng->sub_title }}
                    </span>
                </h2>
                <p>{{ @$subscribe_section->value->language->$defualt->details ?? @$subscribe_section->value->language->$default_lng->details }}</p>
            </div>
            <div class="call-to-action-form">
                <form class="newsletter-form" method="POST" id="newsletter-form">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email" id="email" class="form--control" placeholder="{{ __('Your email') }}" required>
                    </div>
                    <button type="submit" class="btn--base white"><i class="fa fa-spinner d-none fa-pulse fa-fw"></i> {{ __('Subscribe') }}</button>
                </form>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Call To Action
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="blog-section ptb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="section-header">
                    @php
                        $header = explode('|', @$event_head_data->value->language->$defualt->heading ?? @$event_head_data->value->language->$default_lng->heading);
                    @endphp
                    {{-- <span class="section-sub-title">{{ $event_head_data->value->language->$defualt->title ?? 'Events' }}</span> --}}
                    <h2 class="section-title">@isset($header[0]) {{ $header[0] }} @endisset <span>@isset($header[1]) {{ $header[1] }} @endisset</span></h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($recent_events as $item)
            <div class="col-xl-6 col-lg-6 col-md-6 mb-30">
                <div class="blog-item">
                    <div class="blog-thumb">
                        <a href="{{ setRoute('events.details',[$item->id, $item->slug])}}"><img src="{{ get_image($item->image,'events') }}" alt="{{ @$item->title->language->$defualt->title }}"></a>
                    </div>
                    <div class="blog-content">
                        <div class="blog-date">
                            <h6 class="title">{{ dateFormat('d M',$item->created_at) }}</h6>
                            <span class="sub-title">{{ dateFormat('Y',$item->created_at) }}</span>
                        </div>
                        <span class="category">{{ $item->category->name }}</span>
                        <h3 class="title"><a href="{{ setRoute('events.details',[$item->id, $item->slug])}}">{{ @$item->title->language->$defualt->title ?? @$item->title->language->$default_lng->title }}</a></h3>
                        <p>{!! Str::limit(@$item->details->language->$defualt->details ?? @$item->details->language->$default_lng->details, 150); !!}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="blog-subscribe-btn text-center mt-50">
            <a href="#footer-section" class="btn--base scrollButton">{{ __('Subscribe') }}</a>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Statistics
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="statistics-section bg-overlay-base bg_img" data-background="{{ asset('public/frontend/') }}/images/banner/banner-bg.jpg">
    <div class="statistics-bottom-shape">
        <img src="{{ asset('public/frontend/') }}/images/banner/bottom-shape.png" alt="shape">
    </div>
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
                <div class="statistics-item">
                    <div class="statistics-icon">
                        <i class="{{ @$statistics_section->value->language->$defualt->volunteers_icon ?? @$statistics_section->value->language->$default_lng->volunteers_icon }}"></i>
                    </div>
                    <div class="statistics-content">
                        <div class="odo-area">
                            <h3 class="odo-title odometer" data-odometer-final="{{ @$statistics_section->value?->language?->$defualt->volunteers ?? @$statistics_section->value?->language?->$default_lng->volunteers }}">1</h3>
                        </div>
                        <p>{{ __('Volunteers') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
                <div class="statistics-item">
                    <div class="statistics-icon">
                        <i class="{{ @$statistics_section->value->language->$defualt->donations_icon ?? @$statistics_section->value->language->$default_lng->donations_icon }}"></i>
                    </div>
                    <div class="statistics-content">
                        <div class="odo-area">
                            <h3 class="odo-title odometer" data-odometer-final="{{ @$statistics_section->value->language->$defualt->donations ?? @$statistics_section->value->language->$default_lng->donations }}">1</h3>
                        </div>
                        <p>{{ __('Donations') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
                <div class="statistics-item">
                    <div class="statistics-icon">
                        <i class="{{ @$statistics_section->value->language->$defualt->followers_icon ?? @$statistics_section->value->language->$default_lng->followers_icon }}"></i>
                    </div>
                    <div class="statistics-content">
                        <div class="odo-area">
                            <h3 class="odo-title odometer" data-odometer-final="{{ @$statistics_section->value->language->$defualt->followers ?? @$statistics_section->value->language->$default_lng->followers }}">1</h3>
                        </div>
                        <p>{{ __('Followers') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
                <div class="statistics-item">
                    <div class="statistics-icon">
                        <i class="{{ @$statistics_section->value->language->$defualt->likes_icon ?? @$statistics_section->value->language->$default_lng->likes_icon }}"></i>
                    </div>
                    <div class="statistics-content">
                        <div class="odo-area">
                            <h3 class="odo-title odometer" data-odometer-final="{{ @$statistics_section->value->language->$defualt->likes ?? @$statistics_section->value->language->$default_lng->likes }}">1</h3>
                        </div>
                        <p>{{ __('Likes') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Statistics
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->



<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Testimonial
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
{{-- @include('frontend.partials.testimonial') --}}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Testimonial
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Overview
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="overview-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-6 col-md-6 mb-30">
                <h3 class="title mb-20">{{ @$overview_left_section->value->language->$defualt->title ?? @$overview_left_section->value->language->$default_lng->title }}</h3>
                <div class="faq-wrapper">

                    @forelse ($overview_left_section->value?->items ?? [] as $key => $item)
                        <div class="faq-item open">
                            <h3 class="faq-title"><span class="title">{{ @$item->language->$defualt->heading ?? @$item->language->$default_lng->heading }}</span><span class="right-icon"></span></h3>
                            <div class="faq-content">
                                <ul>
                                    <li>
                                        {!! @$item->language?->$defualt->details ?? @$item->language?->$default_lng->details !!}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @empty

                    @endforelse



                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-30">
                <h3 class="title mb-20">{{ @$overview_right_section->value->language->$defualt->title ?? @$overview_left_section->value->language->$default_lng->title }}</h3>
                <div class="choose-tab">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            @foreach ($overview_right_section?->value?->items ?? [] as $key => $item)
                                <button class="nav-link @if ($loop->first) active @endif" id="software-tab" data-bs-toggle="tab" data-bs-target="#target-{{$key}}" type="button" role="tab" aria-controls="software" aria-selected="false">{{ @$item->language?->$defualt->heading ?? '' }}</button>
                            @endforeach

                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">

                        @foreach ($overview_right_section?->value?->items ?? [] as $key => $item)
                            <div class="tab-pane fade @if ($loop->first) show active @endif" id="target-{{$key}}" role="tabpanel" aria-labelledby="software-tab">
                                <div class="choose-item">
                                    <div class="choose-thumb">
                                        <img src="{{ get_image(@$item->image,'site-section') }}" alt="gallery">
                                    </div>
                                    <div class="choose-content">
                                        <p>{{ $item->language?->$defualt->details ?? $item->language?->$default_lng->details }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Overview
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Video
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
{{-- <section class="video-section pt-120">
    <div class="video-shape">
        <img src="{{ asset('public/frontend/') }}/images/map.jpg" alt="shape">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="video-content">
                    @php
                        $viewInfo = explode(' ', @$video->value->language->$defualt->view_info ?? @$video->value->language->$default_lng->view_info);
                    @endphp
                    <h2 class="title"><span class="number">@isset($viewInfo[0]){{ $viewInfo[0] }}@endisset</span> <span class="text"> @isset($viewInfo[1])
                    {{ $viewInfo[1] }}
                    @endisset</span></h2>
                    <h3 class="sub-title"> {{ __(@$video->value->language->$defualt->heading ?? @$video->value->language->$default_lng->heading) }}</h3>
                    <div class="video-area">
                        <a class="video-icon" data-rel="lightcase:myCollection" href="{{ @$video->value->language->$default_lng->video_link }}">
                            <img src="{{ asset('public/frontend/') }}/images/play-button.png" alt="play">
                        </a>
                        <span>{{ __("Watch Video") }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Video
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


@endsection


@push("script")
    <script>
         $(document).ready(function () {
          $(".scrollButton").click(function () {
            // alert("ok");
            // Scroll to the target section with smooth animation
            $("html, body").animate(
              {
                scrollTop: $("#targetSection").offset().top,
              },
              1000 // Duration of the scroll in milliseconds
            );
          });
        });
    </script>
@endpush

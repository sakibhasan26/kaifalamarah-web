@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
    $video_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::VIDEO_SECTION);
    $video = App\Models\Admin\SiteSections::getData($video_slug)->first();

    $viewInfoString = $video->value->language->$defualt->view_info ?? $video->value->language->$default_lng->view_info;
    $viewInfo = explode(' ', $viewInfoString);
@endphp

@push("css")

@endpush

@section('content')

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Video
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="video-section pt-120">
    <div class="video-shape">
        <img src="{{ asset('public/frontend/') }}/images/map.jpg" alt="shape">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="video-content">

                    <h2 class="title">
                        <span class="number">
                            {{ $viewInfo[0] ?? '0' }} {{-- Fallback to '0' if the first part is missing --}}
                        </span>
                        <span class="text">
                            {{ $viewInfo[1] ?? '' }} {{-- Fallback to an empty string if the second part is missing --}}
                        </span>
                    </h2>
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
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Video
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


@endsection


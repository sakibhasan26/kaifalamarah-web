@php
    $default = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
@endphp
@extends('frontend.layouts.master')

@push("css")

@endpush

@section('content')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@include('frontend.partials.breadcrumb')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="blog-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-8 col-lg-7 mb-30">
                <div class="row justify-content-center mb-30-none">
                    <div class="col-xl-12 mb-30">
                        <div class="blog-item">
                            <div class="blog-thumb">
                                <img src="{{ get_image($event->image,'events') }}" alt="{{ @$event->title->language->$default->title ?? @$event->title->language->$default_lng->title }}">
                            </div>
                            <div class="blog-content">
                                <div class="blog-date">
                                    <h6 class="title">{{ dateFormat('d M',$event->created_at) }}</h6>
                                    <span class="sub-title">{{ dateFormat('Y',$event->created_at) }}</span>
                                </div>
                                <span class="category">{{ @$event->category->data->language->$default->name }}</span>
                                <h3 class="title">{{ @$event->title->language->$default->title ?? @$event->title->language->$default_lng->title }}</h3>
                                <p>
                                   {!! @$event->details->language->$default->details ?? @$event->details->language->$default_lng->details !!}
                                </p>
                                <div class="blog-tag-wrapper">
                                    <span>{{ __("Tags") }}:</span>
                                    <ul class="blog-footer-tag">
                                        @foreach ($event->tags->language->$default->tags ?? [] as $item)
                                            <li><a href="javascript:void(0)">{{ @$item }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xl-4 col-lg-5 mb-30">
                <div class="sidebar">
                    <div class="widget-box mb-30">
                        <div class="widget-title-area">
                            <h4 class="widget-title">{{ __("Categories") }}</h4>
                        </div>
                        <div class="category-widget-box">
                            <ul class="category-list">
                                @foreach ($categories ?? [] as $cat)
                                    <li><a href="javascript:void(0)">{{ @$cat->data->language->$default->name ?? @$cat->data->language->$default_lng->name  }} <span>{{ isset($cat->events) ? count($cat->events) : '0' }}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __('Recent Posts') }}</h4>
                        <div class="popular-widget-box">
                            @foreach ($recent_events as $item)
                                <div class="single-popular-item d-flex flex-wrap align-items-center">
                                    <div class="popular-item-thumb">
                                        <a href="{{ route('events.details',[$item->id, $item->slug])}}"><img src="{{ get_image($item->image,'events') }}" alt="blog"></a>
                                    </div>
                                    <div class="popular-item-content">
                                        <span class="date">{{ dateFormat('d M, Y', $item->created_at) }}</span>
                                        <h5 class="title"><a href="{{ route('events.details',[$item->id, $item->slug])}}">{{ Str::limit(@$item->title->language->$default->title, 45, '...') }}</a></h5>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div> --}}
            @include('frontend.partials.events_sidebar', compact('categories', 'recent_events'))
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


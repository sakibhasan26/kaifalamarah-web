@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
    $gallety_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::GALLERY_SCTION);
    $gallery = App\Models\Admin\SiteSections::getData($gallety_slug)->first();
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
    Start Gallery
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="gallery-section ptb-120">
    <div class="container-fluid p-0">
        <div class="row g-0">
            @if(isset($gallery->value->items))
                @php
                    $count = 0;
                @endphp
                @foreach($gallery->value->items ?? [] as $key => $item)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="gallery-item">
                        <div class="thumb">
                            <img src="{{ get_image(@$item->image,'site-section') }}" alt="gallery">
                            <div class="gallery-shape">
                                <img src="{{ asset('public/frontend/') }}/images/gallery/gallery-shape.png" alt="shape">
                            </div>
                            <div class="content">
                                <h2 class="title">{{ @$item->language->$defualt->title ?? @$item->language->$default_lng->title }}</h2>
                                <div class="gallery-btn">
                                    <a href="javascript:void()">#{{ @$item->language->$defualt->tag ?? @$item->language->$default_lng->tag }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $count++;
                    if ($count == 3) {
                        break;
                    }
                @endphp
                @endforeach
            @endif
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Gallery
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


@endsection


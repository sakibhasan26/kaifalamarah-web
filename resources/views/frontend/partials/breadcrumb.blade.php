@php
    $breadcrumd_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::BREADCRUMB_SECTION);
    $breadcrum = App\Models\Admin\SiteSections::getData( $breadcrumd_slug)->first();
@endphp
<section class="banner-section inner-banner-section bg_img bg-overlay-base" data-background="{{ get_image(@$breadcrum->value->images->banner_image,'site-section') }}">
    <div class="banner-bottom-shape">
        <img src="{{ asset('public/frontend/') }}/images/banner/bottom-shape.png" alt="shape">
    </div>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-xl-7 col-lg-7 col-md-12">
                <div class="banner-content">
                    <h1 class="title">{{ __(@$sub_page_title) }}</h1>
                    <div class="breadcrumb-area">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ setRoute('index') }}">{{ __('Home') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __(@$page_title) }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

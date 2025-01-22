
@extends('frontend.layouts.master')
@php
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
    $privacy_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::PRIVACY_POLICY);
    $privacy = App\Models\Admin\SiteSections::getData( $privacy_slug)->first();
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
    Start Privacy
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="privacy-section ptb-120">
    <div class="container">
        <div class="privacy-area">
            <div class="privacy-wrapper">
                @php
                    echo @$privacy->value->language->$defualt->policy ?? @$privacy->value->language->$default_lng->policy
                @endphp
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Privacy
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


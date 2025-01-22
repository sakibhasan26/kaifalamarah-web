@php
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
    $brand = getSectionData('top-partner-section') ;
@endphp
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Brand
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="brand-section ptb-120">
    <div class="container">
        <div class="brand-header">
            <span class="brand-header-line"></span>
            <h3 class="brand-header-title">“ {{ __(@$brand->value->language->$defualt->heading ?? @$brand->value->language->$default_lng->heading) }} ”</h3>
            <span class="brand-header-line"></span>
        </div>
        <div class="brand-slider">
            <div class="swiper-wrapper">

                @if(isset($brand->value->items))
                @foreach($brand->value->items ?? [] as $key => $item)
                <div class="swiper-slide">
                    <div class="brand-item">
                        <img src="{{ get_image(@$item->image,'site-section') }}" alt="brand">
                    </div>
                </div>
                @endforeach
            @endif


            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Brand
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

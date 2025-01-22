
@extends('frontend.layouts.master')

@section('content')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@include('frontend.partials.breadcrumb')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Faq
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="faq-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-4 col-lg-5 mb-30">
                <div class="sidebar">
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __("Categories") }}</h4>
                        <div class="category-widget-box">
                            <ul class="category-list">
                                @forelse ($faqCategories as $cat)
                                @php
                                    $faq = App\models\FaqSection::where('category_id',$cat->id)->count();
                                @endphp
                                @if( $faq > 0)
                                <li><a href="javascript:void(0)">{{ $cat->name }} <span>{{   $faq }}</span></a></li>
                                @endif

                                @empty
                                <li class="text--base">{{ __("No Category Found") }}</li>
                                @endforelse


                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7 mb-30">
                <div class="faq-wrapper">
                    @forelse ($allFaq as $key => $faq)
                    <div class="faq-item {{ $key==0?'active open':'' }}">
                        <h3 class="faq-title"><span class="title">{{ @$faq->question }}</span><span
                                class="right-icon"></span></h3>
                        <div class="faq-content">
                            <p>{{ @$faq->answer }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="faq-item">
                        <p class="text-center text--base">{{ __("No FAQ Found") }}</p>
                    </div>
                    @endforelse



                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Faq
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


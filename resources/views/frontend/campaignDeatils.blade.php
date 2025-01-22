@php
    $campaign_head = getSectionData('campaigns');
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
@endphp
@extends('frontend.layouts.master')

@push("css")
    <style>
        .nice-select .list {
            height: 200px;
            overflow: auto;
        }
    </style>
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
    Start Campaign
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="campaign-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-7 col-lg-7 mb-30">
                <div class="campaign-item details">
                    <div class="campaign-thumb">
                        <img src="{{ get_image($campaign->image,'campaigns') }}" alt="campaign">
                        <div class="campaign-category">
                            <span>#Life</span>
                        </div>
                    </div>
                    <div class="campaign-content">
                        {!! @$campaign->desc->language->$defualt->desc ??@$campaign->desc->language->$default_lng->desc !!}
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 mb-30">
                <div class="sidebar">
                    <div class="donation-form-area">
                        <h3 class="title">{{ __("Enter your donation") }}</h3>
                        <form class="dontation-form" method="POST" action="{{ setRoute('donation.submit') }}">
                            @csrf
                            <input type="hidden" name="campaign_id" value="{{ $campaign_id }}">
                            <input type="hidden" name="campaign_slug" value="{{ $campaign_slug }}">
                            <div class="donation-form-wrapper">
                                <h5 class="sub-title">{{ __("Select the amount") }}:</h5>
                                <div class="radio-wrapper">
                                    <div class="radio-item">
                                        <input type="radio" id="level-1" value="5" name="select_amount">
                                        <label for="level-1">5 {{ get_default_currency_code() }}</label>
                                    </div>
                                    <div class="radio-item">
                                        <input type="radio" id="level-2" value="50" name="select_amount">
                                        <label for="level-2">50 {{ get_default_currency_code() }}</label>
                                    </div>
                                    <div class="radio-item">
                                        <input type="radio" id="level-3" value="100" name="select_amount">
                                        <label for="level-3">100 {{ get_default_currency_code() }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="donation-form-wrapper">
                                <h5 class="sub-title"> {{ __("Or enter other amount(optional)") }} </h5>
                                <input type="text" class="form--control" name="amount" placeholder="{{ __("other Amount") }}">
                            </div>
                            <div class="donation-form-wrapper">
                                <h5 class="sub-title">{{ __("Select Gateway") }}:</h5>
                                <select class="form--control select2-auto-tokenize" name="currency">
                                    @foreach ($payment_gateways_currencies ?? [] as $item)
                                        @if (Auth::check())
                                            <option value="{{ $item->alias  }}">
                                                {{ $item->name }}
                                            </option>
                                        @else
                                            @if (!$item->gateway->isManual())
                                                <option value="{{ $item->alias  }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endif
                                        @endif
                                    @endforeach
                                    @auth
                                        @php
                                            $wallet = DB::table('user_wallets')->where('user_id', Auth::id())->first();
                                        @endphp
                                        <option value="wallet-usd">
                                            Balance ({{ get_amount(@$wallet->balance).' '. get_default_currency_code()}})
                                        </option>
                                    @endauth
                                </select>
                            </div>
                            <button type="submit" class="btn--base w-100 mt-3" @if ($campaign->to_go == 0)disabled="disabled"@endif>{{ __("Donate") }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Campaign
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection
@push('script')
    <script>
        $('.dontation-form input[name="select_amount"]').on('click', function(){
            let amount = $(this).val();
            $('.dontation-form input[name="amount"]').val(amount);
        })
    </script>
@endpush


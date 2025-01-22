@extends('user.layouts.master')

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Add Money")])
@endsection

@section('content')
<div class="row mb-20-none">
    <div class="col-xxl-6 col-xl-12 col-lg-12 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __('Add Money') }}</h4>
            </div>
            <div class="card-body">
                <form class="card-form" action="{{ setRoute("user.add.money.submit") }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 form-group text-center">
                            <div class="exchange-area">
                                <code class="d-block text-center"><span>{{__('Exchange Rate')}}</span>
                                    <span class="rate-show">--</span>
                                    </code>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label>{{ __('Payment Gateway') }}<span>*</span></label>
                            <select class="form--control select2-auto-tokenize" name="currency">
                                @foreach ($payment_gateways_currencies ?? [] as $item)
                                <option
                                    value="{{ $item->alias  }}"
                                    data-currency="{{ $item->currency_code }}"
                                    data-min_amount="{{ $item->min_limit }}"
                                    data-max_amount="{{ $item->max_limit }}"
                                    data-percent_charge="{{ $item->percent_charge }}"
                                    data-fixed_charge="{{ $item->fixed_charge }}"
                                    data-rate="{{ $item->rate }}"
                                    >
                                    {{ $item->name }}
                                </option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label>{{ __("Amount") }}<span>*</span></label>
                            <div class="input-group">
                                <input type="text" class="form--control" name="amount" placeholder="{{ __('enter Amount') }}" maxlength="20">
                                <div class="f-group">
                                    <select class="f-control f-dropdown form--control">
                                        {{-- <option selected value="1" data-image="../assets\images\flag/us.svg">USD</option> --}}
                                        <option value="{{ get_default_currency_code() }}">{{ get_default_currency_code() }}</option>
                                    </select>
                                </div>
                            </div>
                            <code class="d-block mt-10 text-end balance-show">{{ __("Available") }} {{ authWalletBalance() }} {{ get_default_currency_code() }}</code>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <div class="note-area">
                                <code class="d-block limit-show">--</code>
                                <code class="d-block fees-show">--</code>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12">
                        <button type="submit" class="btn--base w-100">{{ __('Add Money') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-xl-12 col-lg-12 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __('Summery') }}</h4>
            </div>
            <div class="card-body">
                <div class="preview-list-wrapper">
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-receipt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('Entered Amount') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--success request-amount">--</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-battery-half"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{__('Total Fees & Charges')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--warning fees">--</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="lab la-get-pocket"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('Will Get') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--danger will-get">--</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-money-check-alt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span class="last">{{ __('Total Payable Amount') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--info last pay-in-total">--</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="dashboard-list-area mt-20">
    <div class="dashboard-header-wrapper">
        <h4 class="title">{{__('Add Money Log')}}</h4>
        <div class="dashboard-btn-wrapper">
            <div class="dashboard-btn">
                <a href="{{ setRoute('user.transactions.index', 'add-money') }}" class="btn--base">{{ __('view More') }}</a>
            </div>
        </div>
    </div>
    <div class="dashboard-list-wrapper">
        @include('user.components.transaction-log', compact('transactions'))
    </div>
</div>
@endsection
@push('script')
    <script>
        var lang_limit = "{{ __('Limit') }} ";
        var lang_charge = "{{ __('Charge') }} ";

        var defualCurrency = "{{ get_default_currency_code() }}";
        var defualCurrencyRate = "{{ get_default_currency_rate() }}";

        $('select[name=currency]').on('change',function(){
            getExchangeRate($(this));
            getLimit();
            getFees();
            getPreview();
        });
        $(document).ready(function(){
            getExchangeRate();
            getLimit();
            getFees();
            // getPreview();
        });
        $("input[name=amount]").keyup(function(){
             getFees();
             getPreview();
        });
        // Get exchange rate
        function getExchangeRate(event) {
            var element = event;
            var currencyCode = acceptVar().currencyCode;
            var currencyRate = acceptVar().currencyRate;
            console.log(currencyCode);
            var currencyMinAmount = acceptVar().currencyMinAmount;
            var currencyMaxAmount = acceptVar().currencyMaxAmount;
            $('.rate-show').html("1 " + defualCurrency + " = " + parseFloat(currencyRate).toFixed(2) + " " + currencyCode);
        }

        // Get Limit
        function getLimit() {
            var sender_currency = acceptVar().currencyCode;
            var sender_currency_rate = acceptVar().currencyRate;
            var min_limit = acceptVar().currencyMinAmount;
            var max_limit =acceptVar().currencyMaxAmount;
            if($.isNumeric(min_limit) || $.isNumeric(max_limit)) {
                var min_limit_calc = parseFloat(min_limit/sender_currency_rate).toFixed(2);
                var max_limit_clac = parseFloat(max_limit/sender_currency_rate).toFixed(2);
                $('.limit-show').html(lang_limit + min_limit_calc + " " + defualCurrency + " - " + max_limit_clac + " " + defualCurrency);
                return {
                    minLimit:min_limit_calc,
                    maxLimit:max_limit_clac,
                };
            }else {
                $('.limit-show').html("Limit --");
                return {
                    minLimit:0,
                    maxLimit:0,
                };
            }
        }

        // Fees calculation
        function feesCalculation() {
            var sender_currency = acceptVar().currencyCode;
            var sender_currency_rate = acceptVar().currencyRate;
            var sender_amount = $("input[name=amount]").val();
            sender_amount == "" ? (sender_amount = 0) : (sender_amount = sender_amount);
            var fixed_charge = acceptVar().currencyFixedCharge;
            var percent_charge = acceptVar().currencyPercentCharge;
            if ($.isNumeric(percent_charge) && $.isNumeric(fixed_charge) && $.isNumeric(sender_amount)) {
                // Process Calculation
                var fixed_charge_calc = parseFloat(fixed_charge);
                var percent_charge_calc = parseFloat(sender_currency_rate)*(parseFloat(sender_amount) / 100) * parseFloat(percent_charge);
                var total_charge = parseFloat(fixed_charge_calc) + parseFloat(percent_charge_calc);
                total_charge = parseFloat(total_charge).toFixed(2);
                // return total_charge;
                return {
                    total: total_charge,
                    fixed: fixed_charge_calc,
                    percent: percent_charge_calc,
                };
            } else {
                // return "--";
                return false;
            }
        }

        // Get Fees
        function getFees() {
            var sender_currency = acceptVar().currencyCode;
            var percent = acceptVar().currencyPercentCharge;
            var charges = feesCalculation();
            if (charges == false) {
                return false;
            }
            $(".fees-show").html(lang_charge + parseFloat(charges.fixed).toFixed(2) + " " + sender_currency + " + " + parseFloat(percent).toFixed(2) + "% = " + parseFloat(charges.total).toFixed(2) + " " + sender_currency);
        }

        function acceptVar() {
            var selectedVal = $("select[name=currency] :selected");
            var currencyCode = $("select[name=currency] :selected").attr("data-currency");
            var currencyRate = $("select[name=currency] :selected").attr("data-rate");
            var currencyMinAmount = $("select[name=currency] :selected").attr("data-min_amount");
            var currencyMaxAmount = $("select[name=currency] :selected").attr("data-max_amount");
            var currencyFixedCharge = $("select[name=currency] :selected").attr("data-fixed_charge");
            var currencyPercentCharge = $("select[name=currency] :selected").attr("data-percent_charge");

            // var sender_select = $("input[name=from_wallet_id] :selected");

            return {
                currencyCode:currencyCode,
                currencyRate:currencyRate,
                currencyMinAmount:currencyMinAmount,
                currencyMaxAmount:currencyMaxAmount,
                currencyFixedCharge:currencyFixedCharge,
                currencyPercentCharge:currencyPercentCharge,
                selectedVal:selectedVal,
            };
        }

        function getPreview() {
                var senderAmount = $("input[name=amount]").val();
                var sender_currency = acceptVar().currencyCode;
                var sender_currency_rate = acceptVar().currencyRate;
                // var receiver_currency = acceptVar().rCurrency;
                senderAmount == "" ? senderAmount = 0 : senderAmount = senderAmount;

                // Sending Amount
                $('.request-amount').text(senderAmount + " " + defualCurrency);

                // Fees
                var charges = feesCalculation();
                // console.log(total_charge + "--");
                $('.fees').text(charges.total + " " + sender_currency);

                // will get amount
                // var willGet = parseFloat(senderAmount) - parseFloat(charges.total);
                var willGet = parseFloat(senderAmount).toFixed(2);
                $('.will-get').text(willGet + " " + defualCurrency);

                // Pay In Total
                var totalPay = parseFloat(senderAmount) * parseFloat(sender_currency_rate)
                 var pay_in_total = parseFloat(charges.total) + parseFloat(totalPay);

                $('.pay-in-total').text(parseFloat(pay_in_total).toFixed(2) + " " + sender_currency);

            }
    </script>
@endpush

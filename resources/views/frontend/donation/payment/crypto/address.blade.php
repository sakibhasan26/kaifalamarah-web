@extends('frontend.layouts.master')

@push('css')

@endpush

@section('content')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@include('frontend.partials.breadcrumb')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Banner
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<div class="contact-section pt-60 pb-60">
    <div class="container">
        <div class="row mb-30-none">
            <div class="col-xl-6 col-lg-6 mb-20">
                <div class="custom-card mt-10">
                    <div class="dashboard-header-wrapper">
                        <h4 class="title">{{ __($page_title) }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($transaction->status == payment_gateway_const()::STATUSWAITING)
                            <form class="row g-4 submit-form" method="POST" action="{{ setRoute('donation.payment.crypto.confirm',$transaction->trx_id) }}">
                                @csrf
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" value="{{ $transaction->details->payment_info->receiver_address ?? "" }}" class="form-control form--control ref-input copiable" readonly>
                                        <div class="input-group-append" style="cursor: pointer">
                                            <span class="input-group-text copytext copy-button">
                                                <i class="la la-copy"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mx-auto mt-4 text-center">
                                    <img class="mx-auto" src="{{ $transaction->details->payment_info->receiver_qr_image ?? "" }}" alt="Qr Code">
                                </div>

                                {{-- Print Dynamic Input Filed if Have START --}}
                                @foreach ($transaction->details->payment_info->requirements ?? [] as $input)
                                    <div class="form-group col-12">
                                        <label for="">{{ $input->label }} </label>
                                        <input type="text" name="{{ $input->name }}" placeholder="{{ $input->placeholder ?? "" }}" class="form-control" @if ($input->required)
                                            @required(true)
                                        @endif>
                                    </div>
                                @endforeach
                                {{-- Print Dynamic Input Filed if Have END --}}

                                <div class="col-12 mt-5">
                                    <button type="submit" class="btn--base w-100 text-center">{{ __("Proceed_WEB") }}</button>
                                </div>
                            </form>
                        @else
                            <div class="payment-received-alert">
                                <div class="text-center text--success">
                                    {{ __("Payment Received Successfully!") }}
                                </div>

                                <div class="txn-hash text-center mt-2 text--info">
                                    <strong>{{ __("Txn Hash:") }} </strong>
                                    <span>{{ $transaction->details->payment_info->txn_hash ?? "" }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 mb-20">
                <div class="custom-card mt-10">
                    <div class="dashboard-header-wrapper">
                        <h4 class="title">{{__("Add Money Preview")}}</h4>
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
                                                <span>{{ __("Request Amount") }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <span class="text--success request-amount">{{ get_amount($transaction->request_amount, $transaction->user_wallet->currency->code) }}</span>
                                    </div>
                                </div>
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-half"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __("Exchange Rate_WEB") }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <span class="text--warning fees">1 {{ get_default_currency_code() }} =
                                            {{ get_amount($transaction->currency->rate, $transaction->gateway_currency->currency_code,8) }}</span>
                                    </div>
                                </div>
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-half"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __("Fees_WEB") }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <span class="text--warning fees">{{ get_amount($transaction->charge->total_charge, $transaction->user_wallet->currency->code,4) }}</span>
                                    </div>
                                </div>
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-half"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __("Total Payable_WEB") }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <span class="text--warning fees">{{ get_amount($transaction->payable, $transaction->gateway_currency->currency_code,8) }}</span>
                                    </div>
                                </div>
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-half"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __("You Will Donate") }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <span class="text--warning fees">{{ get_amount($transaction->request_amount, $transaction->user_wallet->currency->code,4) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')
    <script>
        $(".copy-button").click(function(){
            var value = $('.copiable').val()
            navigator.clipboard.writeText(value);
            throwMessage('success',['Text successfully copied.']);
        });
    </script>
@endpush


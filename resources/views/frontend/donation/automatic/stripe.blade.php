@extends('frontend.layouts.master')

@push('css')
<style>
    .jp-card .jp-card-back, .jp-card .jp-card-front {

      background-image: linear-gradient(160deg, #084c7c 0%, #55505e 100%) !important;
      }
      label{
          color: #000 !important;
      }
      .form--control{
          color: #000 !important;
      }
      .input-group-text {
        border: none;
        font-size: 14px;
        background: #EFF0F0;
        color: #0f1514;
        height: 65px;
        border-radius: 0 5px 5px 0;
        font-weight: 500;
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

<div class="contact-section pt-60">
    <div class="container">
        <div class="card custom--card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">@lang('Stripe Payment')</h5>
                <a class="btn btn--danger rounded text-light" href="{{ setRoute('donation.payment.cancel',@$hasData->type) }}">Cancel</a>

            </div>
            <div class="card-body">

                <div class="card-wrapper"></div>
                <br><br>

                <form role="form" id="payment-form" action="{{setRoute('donation.stripe.payment.confirmed')}}" method="POST">
                    @csrf
                    {{-- <input type="hidden" value="{{$data->track}}" name="track"> --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form--label">@lang('Name on Card')</label>
                            <div class="input-group">
                                <input type="text" class="form-control form--control custom-input" name="name" autocomplete="off" autofocus/>
                                <span class="input-group-text bg--base"><i class="fa fa-font"></i></span>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <label for="cardNumber" class="form--label">@lang('Card Number')</label>
                            <div class="input-group">
                                <input type="tel" size="16" class="form-control form--control custom-input" maxlength="19" id="card_number" name="cardNumber" autocomplete="off" required autofocus/>
                                <span class="input-group-text bg--base"><i class="fa fa-credit-card"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="cardExpiry" class="form--label">@lang('Expiration Date')</label>
                            <input type="tel" class="form-control form--control input-sz custom-input" name="cardExpiry" autocomplete="off" required/>
                        </div>
                        <div class="col-md-6 ">
                            <label for="cardCVC" class="form--label">@lang('CVC Code')</label>
                            <input type="tel" class="form-control form--control input-sz custom-input" name="cardCVC" autocomplete="off" required/>
                        </div>
                    </div>
                    <br>
                    <button class="btn--base w-100 text-center btn-loading my-3" type="submit">
                        @lang('PAY NOW') ( {{ number_format(@$hasData->data->amount->total_amount,2 )}} {{ @$hasData->data->amount->sender_cur_code }} )
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>


@endsection

@push('script')
<script src="{{ asset('public/frontend/') }}/js/card.js"></script>
    <script>
        $('#card_number').on('keyup', function(e) {
            var val = $(this).val();
            var newval = '';
            val = val.replace(/\s/g, '');
            for (var i = 0; i < val.length; i++) {
                if (i % 4 == 0 && i > 0) newval = newval.concat(' ');
                newval = newval.concat(val[i]);
            }
            $(this).val(newval);
        });
    </script>
    <script>
        (function ($) {
            "use strict";
            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="cardNumber"]',
                    expiryInput: 'input[name="cardExpiry"]',
                    cvcInput: 'input[name="cardCVC"]',
                    nameInput: 'input[name="name"]'
                }
            });
        })(jQuery);
    </script>
    <script>
        $('.cancel-btn').click(function(){
            var dataHref = $(this).data('href');
            if(confirm("Are you sure?") == true) {
                window.location.href = dataHref;
            }
        });
      </script>
@endpush

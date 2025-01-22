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
        <div class="row mb-30-none justify-content-center">
            <div class="col-xl-6 col-lg-6 mb-20">
                <div class="custom-card mt-10">
                    <div class="dashboard-header-wrapper">
                        <h4 class="title">
                            @php
                                echo @$gateway->desc;
                            @endphp
                        </h4>
                    </div>
                    <div class="card-body">
                        <form class="card-form" action="{{ setRoute('donation.flutterwave.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" value="{{ $hasData['campaign_id'] }}" name="campaign_id">
                            <input type="hidden" value="{{ $hasData['campaign_slug'] }}" name="campaign_slug">
                            <input type="hidden" value="{{ $hasData['amount'] }}" name="amount">
                            <input type="hidden" value="{{ $hasData['currency'] }}" name="currency">
                            <input type="hidden" value="{{ $hasData['payment_type'] }}" name="payment_type">

                            <div class="row">
                                <div class="col-lg-12">
                                    @include('admin.components.form.input',[
                                        'label'     => "Name",
                                        'name'      => 'name',
                                        'value'     => old('name'),
                                    ])
                                </div>
                                <div class="col-lg-12">
                                    @include('admin.components.form.input',[
                                        'label'     => "Phone",
                                        'name'      => 'phone',
                                        'value'     => old('phone'),
                                    ])
                                </div>
                                <div class="col-lg-12">
                                    @include('admin.components.form.input',[
                                        'label'     => "Email*",
                                        'name'      => 'email',
                                        'required'  => 'required',
                                        'value'     => old('email'),
                                    ])
                                </div>
                                <div class="col-xl-12 col-lg-12">
                                    <button type="submit" class="btn--base w-100 btn-loading"> {{ __("Confirm Payment") }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')


@endpush

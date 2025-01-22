@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Dashboard")])
@endsection

@section('content')
<div class="dashboard-area pt-40">
    <div class="row justify-content-center mb-30-none">
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
            <div class="dashboard-item">
                <a href="{{ setRoute('user.add.money.index') }}" class="dash-btn">{{ __('Add') }}</a>
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="las la-wallet"></i>
                    </div>
                    <h5 class="title">{{ __('Current Balance') }}</h5>
                    <h4 class="num mb-0">{{ get_amount($data['balance'], get_default_currency_code()) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
            <div class="dashboard-item">
                <a href="{{ setRoute('user.donation.history') }}" class="dash-btn">{{ __('View all') }}</a>
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="las la-wallet"></i>
                    </div>
                    <h5 class="title">{{ __('Total Donation Amount') }}</h5>
                    <h4 class="num mb-0">{{ get_amount($data['donation_amount'], get_default_currency_code()) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
            <div class="dashboard-item">
                <a href="{{ setRoute('user.donation.history') }}" class="dash-btn">{{__('View all')}}</a>
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="las la-hands"></i>
                    </div>
                    <h5 class="title">{{ __('Total Donate') }}</h5>
                    <h4 class="num mb-0">{{ $data['total_donate_time'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
            <div class="dashboard-item">
                <a href="{{ setRoute('campaign') }}" class="dash-btn">{{ __('View all') }}</a>
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="lab la-free-code-camp"></i>
                    </div>
                    <h5 class="title">{{ __('Total Campaign') }}</h5>
                    <h4 class="num mb-0">{{ $data['campaign_total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
            <div class="dashboard-item">
                <a href="{{ setRoute('campaign') }}" class="dash-btn">{{ __('View all') }}</a>
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="las la-ticket-alt"></i>
                    </div>
                    <h5 class="title">{{ __('All Time Raised Amount') }}</h5>
                    <h4 class="num mb-0">{{ get_amount($data['all_time_risede'], get_default_currency_code()) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
            <div class="dashboard-item">
                <a href="{{ setRoute('user.donation.history') }}" class="dash-btn">{{ __('View all') }}</a>
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="las la-donate"></i>
                    </div>
                    <h5 class="title">{{ __('Last Donate Amount') }}</h5>
                    <h4 class="num mb-0">{{ get_amount($data['last_donation_amount'], get_default_currency_code()) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
            <div class="dashboard-item">
                <a href="{{ setRoute('events') }}" class="dash-btn">{{ __('View all') }}</a>
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="las la-calendar-check"></i>
                    </div>
                    <h5 class="title">{{ __('Total Event') }}</h5>
                    <h4 class="num mb-0">{{ $data['total_event'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="chart-section pt-60">
    <h4 class="title mb-20">{{ __('Donate History Chart') }}</h4>
    <div class="chart-area">
        <div class="chart-wrapper">
            <div class="chart-body">
                <div class="chart-container">
                    <div id="chart1" class="growth-chart" data-donate_month="{{ json_encode($data['donate_month']) }}" data-donate_chart="{{ json_encode($data['donate_chart']) }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="table-wrapper pt-60">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <h4 class="title d-flex justify-content-between align-items-center">
                {{ __('Donation History') }}
                <a href="{{ setRoute('user.donation.history') }}" class="dash-btn-2">{{ __('View all') }}</a>
            </h4>
            <div class="table-area table-responsive">
                @php
                    $donation_histories = $data['donation_history'];
                @endphp
                @include('user.components.data-table.donation-history', compact('donation_histories','default'))
            </div>
        </div>
    </div>
</div>
@endsection

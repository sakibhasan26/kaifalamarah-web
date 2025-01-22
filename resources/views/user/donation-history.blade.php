@php
    $default = get_default_language_code();
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
@endphp
@extends('user.layouts.master')
@push('css')

<style>
    .pagination {
        margin-top: 20px !important;
    }
</style>

@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Donation History")])
@endsection

@section('content')
<div class="table-wrapper pt-60">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="table-area table-responsive">
                <h4 class="title">{{ __('Donation History') }}</h4>
                @php
                    $donation_histories = $donation_history;
                @endphp
                @include('user.components.data-table.donation-history', compact('donation_histories','default'))
            </div>
            {{ $donation_history->links() }}
        </div>
    </div>
</div>
@endsection



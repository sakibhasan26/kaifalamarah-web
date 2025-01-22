<!-- favicon -->
<link rel="shortcut icon" href="{{ get_fav($basic_settings) }}" type="image/x-icon">
<!-- fontawesome css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/fontawesome-all.min.css">
<!-- bootstrap css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/bootstrap.min.css">
<!-- swipper css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/swiper.min.css">
<!-- lightcase css links -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/lightcase.css">
<!-- line-awesome-icon css -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/line-awesome.min.css">
<!-- odometer css -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/odometer.css">
<!-- animate.css -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/animate.css">
<link rel="stylesheet" href="{{ asset('public/backend/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/backend/library/popup/magnific-popup.css') }}">
<!-- nice select css -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/nice-select.css') }}">
<!-- main style css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/style.css">
<link rel="stylesheet" href="{{ asset('public/frontend/css/rtl.css') }}">
    @php
        $color = @$basic_settings->base_color ?? '#4A8FCA';
    @endphp
<style>
    :root {
        --primary-color: {{$color}};
    }
</style>

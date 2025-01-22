@php
    $cookie_accepted = session()->get('cookie_accepted');
    $cookie_decline = session()->get('cookie_decline');
@endphp
<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $basic_settings->sitename(__($page_title??'')) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @include('partials.header-asset')

    @stack('css')
</head>
<body class="{{ selectedLangDir() ?? "ltr"}}">
{{-- @include('frontend.partials.preloader') --}}
@include('frontend.partials.scroll-to-top')
@include('frontend.partials.header')

@yield("content")

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start cookie
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@if (isset($site_cookie->value->desc))
    <div class="cookie-main-wrapper">
        <div class="cookie-content">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path
                    d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11c-1.654 0-3-1.346-3.003-2.937c.005-.034.016-.136.017-.17a.998.998 0 0 0-1.254-1.006A2.963 2.963 0 0 1 15 7c-1.654 0-3-1.346-3-3c0-.217.031-.444.099-.716a1 1 0 0 0-1.067-1.236A9.956 9.956 0 0 0 2 12c0 5.514 4.486 10 10 10s10-4.486 10-10c0-.049-.003-.097-.007-.16a1.004 1.004 0 0 0-.395-.776zM12 20c-4.411 0-8-3.589-8-8a7.962 7.962 0 0 1 6.006-7.75A5.006 5.006 0 0 0 15 9l.101-.001a5.007 5.007 0 0 0 4.837 4C19.444 16.941 16.073 20 12 20z" />
                <circle cx="12.5" cy="11.5" r="1.5" />
                <circle cx="8.5" cy="8.5" r="1.5" />
                <circle cx="7.5" cy="12.5" r="1.5" />
                <circle cx="15.5" cy="15.5" r="1.5" />
                <circle cx="10.5" cy="16.5" r="1.5" />
            </svg>
            <p class="text-white">{{ strip_tags($site_cookie->value->desc) }}<a
                    href="{{ url('/').'/'.@$site_cookie->value->link }}" target="_blank">{{ __('privacy policy') }}</a></p>
        </div>
        <div class="cookie-btn-area">
            <button class="cookie-btn">{{ __('Allow') }}</button>
            <button class="cookie-btn-cross">{{ __('Decline') }}</button>
        </div>
    </div>
@endif
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End cookie
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@include('frontend.partials.footer')
@include('partials.footer-asset')
@include('admin.partials.notify')
@include('frontend.partials.extensions.tawk-to')

@stack('script')

<script>
    $(document).ready(function () {
        $(".language-select").change(function(){
            var submitForm = `<form action="{{ setRoute('languages.switch') }}" id="local_submit" method="POST"> @csrf <input type="hidden" name="target" value="${$(this).val()}" ></form>`;
            $("body").append(submitForm);
            $("#local_submit").submit();
        });
    });
    //*************** Newsletter Form Submit Start ******************
    $(document).on('submit', '#newsletter-form', function(e){
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ setRoute('subscriber') }}",
            data: new FormData(this),
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function(){
                $('#newsletter-form .fa-spinner').removeClass('d-none');
            },
            complete: function(){
                $('#newsletter-form .fa-spinner').addClass('d-none');
            },
            success: function (data) {
                $('#newsletter-form')[0].reset();
                throwMessage('success',data.message.success);
            },
            error: function(xhr, ajaxOption, thrownError){
                // console.log(thrownError+'\r\n'+xhr.statusText+'\r\n'+xhr.responseText);
                // console.log('errors');
                var errorObj = JSON.parse(xhr.responseText);
                throwMessage(errorObj.type,errorObj.message.error.errors);
            },
        });
    });
    //*************** Newsletter Form Submit End ******************
    //*************** Contact Form Submit Start ******************
    $(document).on('submit', '#contact-form', function(e){
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ setRoute('contact.store') }}",
            data: new FormData(this),
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function(){
                $('#contact-form .fa-spinner').removeClass('d-none');
            },
            complete: function(){
                $('#contact-form .fa-spinner').addClass('d-none');
            },
            success: function (data) {
                $('#contact-form')[0].reset();
                throwMessage('success',data.message.success);
            },
            error: function(xhr, ajaxOption, thrownError){
                // console.log(thrownError+'\r\n'+xhr.statusText+'\r\n'+xhr.responseText);
                // console.log('errors');
                var errorObj = JSON.parse(xhr.responseText);
                throwMessage(errorObj.type,errorObj.message.error.errors);
            },
        });
    });
    //*************** Contact Form Submit End ******************
</script>

<script>
    var status = "{{ @$site_cookie->status }}";
    var cookie_accepted = "{{ @$cookie_accepted }}";
    var cookie_decline = "{{ @$cookie_decline }}";
    const pop = document.querySelector('.cookie-main-wrapper')
    if (status == 1) {
        if (cookie_accepted == true || cookie_decline == true) {
            pop.style.bottom = "-300px";
        } else {
            window.onload = function() {
                setTimeout(function() {
                    pop.style.bottom = "0";
                }, 2000)
            }
        }
    } else {
        pop.style.bottom = "-300px";
    }
</script>
<script>
    (function($) {
        "use strict";
        $('.cookie-btn').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.get('{{ url('cookie/accept') }}', function(response) {
                throwMessage('success', [response]);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        });
        $('.cookie-btn-cross').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.get('{{ url('cookie/decline') }}', function(response) {
                throwMessage('error', [response]);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        });
    })(jQuery)
</script>
</body>
</html>

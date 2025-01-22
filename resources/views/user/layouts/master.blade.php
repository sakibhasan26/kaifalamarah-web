<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (isset($page_title) ? __($page_title) : __("Dashboard")) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @include('partials.header-asset')

    @stack("css")
</head>
<body class="{{ selectedLangDir() ?? "ltr"}}">

@include('frontend.partials.preloader')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Dashboard
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="page-container">
    <!-- sidebar -->
    @include('user.partials.side-nav')

    <div class="body-wrapper">
        <!-- topbar -->
        @include('user.partials.top-nav')

        <div class="body-main-area">
            @yield('content')
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Dashboard
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->



@include('partials.footer-asset')
@include('admin.partials.notify')
{{-- <script src="{{ asset('public/frontend/js/apexcharts.min.js') }}"></script> --}}

@stack("script")
<script>
    function laravelCsrf() {
    return $("head meta[name=csrf-token]").attr("content");
  }

//for popup
function openAlertModal(URL,target,message,actionBtnText = "Remove",method = "DELETE"){
    if(URL == "" || target == "") {
        return false;
    }

    if(message == "") {
        message = "Are you sure to delete ?";
    }
    var method = `<input type="hidden" name="_method" value="${method}">`;
    openModalByContent(
        {
            content: `<div class="card modal-alert border-0">
                        <div class="card-body">
                            <form method="POST" action="${URL}">
                                <input type="hidden" name="_token" value="${laravelCsrf()}">
                                ${method}
                                <div class="head mb-3">
                                    ${message}
                                    <input type="hidden" name="target" value="${target}">
                                </div>
                                <div class="foot d-flex align-items-center justify-content-between">
                                    <button type="button" class="modal-close btn btn--info rounded text-light">{{ __("Close") }}</button>
                                    <button type="submit" class="alert-submit-btn btn btn--danger btn-loading rounded text-light">${actionBtnText}</button>
                                </div>
                            </form>
                        </div>
                    </div>`,
        },

    );
  }
function openModalByContent(data = {
content:"",
animation: "mfp-move-horizontal",
size: "medium",
}) {
$.magnificPopup.open({
    removalDelay: 500,
    items: {
    src: `<div class="white-popup mfp-with-anim ${data.size ?? "medium"}">${data.content}</div>`, // can be a HTML string, jQuery object, or CSS selector
    },
    callbacks: {
    beforeOpen: function() {
        this.st.mainClass = data.animation ?? "mfp-move-horizontal";
    },
    open: function() {
        var modalCloseBtn = this.contentContainer.find(".modal-close");
        $(modalCloseBtn).click(function() {
        $.magnificPopup.close();
        });
    },
    },
    midClick: true,
});
}

</script>
<script>
    window.onload = function () {
        const d = new Date();
        let year = d.getFullYear();
        var chart1 = $('#chart1');
        var chart_amount = chart1.data('donate_chart');
        var donate_month = chart1.data('donate_month');
        var data_chart = [];
        $.each(chart_amount, function (key, value) {
            data_chart.push(
                { x: new Date(donate_month[key]), y: parseInt(value) }
            );
        });

        var chart = new CanvasJS.Chart("chart1", {
            animationEnabled: true,
            zoomEnabled: true,
            theme: "light2",
            axisX: {
                title: "Year "+year,
                lineColor: "#4A8FCA",
                valueFormatString: "MMM",
            },
            axisY: {
                logarithmic: true, //change it to false
                title: "Donation (Log)",
                prefix: "$",
                titleFontColor: "#4A8FCA",
                lineColor: "#4A8FCA",
                gridThickness: 0,
                lineThickness: 1,
            },
            legend: {
                verticalAlign: "top",
                fontSize: 16,
                dockInsidePlotArea: true
            },
            data: [{
                type: "line",
                lineColor: "#4A8FCA",
                color: "#4A8FCA",
                name: "Donation Log",
                dataPoints: data_chart
            }]
        });
        chart.render();

        function addSymbols(e) {
            var suffixes = ["", "K", "M", "B"];

            var order = Math.max(Math.floor(Math.log(e.value) / Math.log(1000)), 0);
            if (order > suffixes.length - 1)
                order = suffixes.length - 1;

            var suffix = suffixes[order];
            return CanvasJS.formatNumber(e.value / Math.pow(1000, order)) + suffix;
        }

    }

    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_password  input').attr("type") == "text"){
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass( "fa-eye-slash" );
                $('#show_hide_password i').removeClass( "fa-eye" );
            }else if($('#show_hide_password input').attr("type") == "password"){
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass( "fa-eye-slash" );
                $('#show_hide_password i').addClass( "fa-eye" );
            }
        });
        $("#show_hide_password2 a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_password2  input').attr("type") == "text"){
                $('#show_hide_password2 input').attr('type', 'password');
                $('#show_hide_password2 i').addClass( "fa-eye-slash" );
                $('#show_hide_password2 i').removeClass( "fa-eye" );
            }else if($('#show_hide_password2 input').attr("type") == "password"){
                $('#show_hide_password2 input').attr('type', 'text');
                $('#show_hide_password2 i').removeClass( "fa-eye-slash" );
                $('#show_hide_password2 i').addClass( "fa-eye" );
            }
        });
        $("#show_hide_password3 a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_password3  input').attr("type") == "text"){
                $('#show_hide_password3 input').attr('type', 'password');
                $('#show_hide_password3 i').addClass( "fa-eye-slash" );
                $('#show_hide_password3 i').removeClass( "fa-eye" );
            }else if($('#show_hide_password3 input').attr("type") == "password"){
                $('#show_hide_password3 input').attr('type', 'text');
                $('#show_hide_password3 i').removeClass( "fa-eye-slash" );
                $('#show_hide_password3 i').addClass( "fa-eye" );
            }
        });
    });
</script>


</body>
</html>

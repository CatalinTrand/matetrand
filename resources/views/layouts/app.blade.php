<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{trim(env("APP_NAME", "Materom SRM"))}}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.1.12.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.1.12.1.min.css') }}" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>
    <style>
        html, body {
            overflow-x: hidden;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            line-height: 1;
        }

        .form-group {
            margin-bottom: 0.3rem;
        }

        .first{
            margin-left:-20px;
        }
        .selector{
            background-color: rgb(248, 248, 255);
        }

        .card-header {
            background-color: rgba(0,0,0,.05);
        }
        .card-body{
            background-color: rgb(248, 248, 255);
        }
        .card-line {
            font-size: 150%;
            margin-bottom: -12px;
            margin-top: -12px;
            line-height: 50px;
            display: inline-block;
            border-right: solid #000;
            border-width: 0 1px;
            padding-left: 30px;
            padding-right: 30px
        }
        a:link{
            text-decoration: none;
        }
        .card-line:hover{
            background-color: rgb(240,240,255);
        }
        .filterForm{
            padding-left: 20px;
        }
        .basicTable a{
            text-decoration:none;
            color: black;
        }
        .basicTable a:hover{
            text-decoration:none;
            color: red;
        }
        img.logo{
            width: 60%;
            height: 60%;
        }
        .role-card{
            width: 17%;
            padding-left: 10px;
            padding-right: 10px;
            border-right: 1px solid #000;
        }
        .edit{
            padding-right: 15px;
            margin-left: -25px;
        }

        .ajaxloadermodal {
            display:    none;
            position:   fixed;
            z-index:    1000;
            top:        0;
            left:       0;
            height:     100%;
            width:      100%;
            background: rgba( 255, 255, 255, .8 )
            url("/images/ajax-loader.gif")
            50% 50%
            no-repeat;
        }

        body.ajaxloading .ajaxloadermodal {
            overflow: hidden;
            display: block;
        }

        .background-image-save {
            background-image:url('/images/icons8-save-64.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center left;
        }

        .background-image-pass-fail {
            background-image:url('/images/icons8-pass-fail-64.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center left;
        }


        .order-button-accepted {
            background-image:url('/images/icons8-checkmark-50.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .order-button-accepted-changed {
            background-image:url('/images/icons8-forward-button-50.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .order-button-accepted-keep {
            background-image:url('/images/icons8-collect-48.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .order-button-rejected {
            background-image:url('/images/icons8-delete-50-4.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .order-button-rejected4 {
            background-image:url('/images/icons8-red-arrow-left-64.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .order-button-rejected-cancel {
            background-image:url('/images/icons8-no-entry-64.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .order-button-request {
            background-image:url('/images/icons8-greater-than-50.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .order-item-tools {
            background-image:url('/images/icons8-maintenance-30.png');
            background-size:     90% 90%;
            background-repeat:   no-repeat;
            background-position: center center;
        }

        .background-image-filters {
            background-image:url('/images/icons8-filter-40.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center left;
        }

        .header-fixed {
            width: 100%
        }

        .header-fixed > thead,
        .header-fixed > tbody,
        .header-fixed > thead > tr,
        .header-fixed > tbody > tr,
        .header-fixed > thead > tr > th,
        .header-fixed > tbody > tr > td {
            display: block;
        }

        .header-fixed > tbody > tr:after,
        .header-fixed > thead > tr:after {
            content: ' ';
            display: block;
            visibility: hidden;
            clear: both;
        }

        .header-fixed > tbody {
            overflow-y: auto;
            height: 150px;
        }

        .header-fixed > tbody > tr > td,
        .header-fixed > thead > tr > th {
            width: 33%;
            float: left;
        }

        .spaced-input {
            height: 1.2rem;
            padding-left: 0.2rem;
        }
        .spaced-label {
            padding-left: 1.2rem;
            padding-top: 0.2rem;
            margin-bottom: 0rem;
        }

        input.user-icon-background {
            padding-left: 2.2rem;
            background-repeat: no-repeat;
            background-color: #fff;
            background-image: url('/images/icons8-user-50.png');
            background-position: left center;
            background-size: contain;
        }

        input.passw-icon-background {
            padding-left: 2.2rem;
            background-repeat: no-repeat;
            background-color: #fff;
            background-image: url('/images/icons8-lock-50.png');
            background-position: left center;
            background-size: contain;
        }

        .orders-table {
            margin-top: 0.5vh;
            width: 100%;
            border: 2px solid black;
            line-height: 1rem;
        }
        .orders-table td, .orders-table th {
            padding: 0.2rem;
            vertical-align: middle;
        }

        .td01 {
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .td02 {
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .td02_c {
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            text-shadow: 1px 1px red;
        }
        .td02h {
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .td02h_c {
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            text-shadow: 1px 1px red;
        }
        .td02h:hover {
            color: #0000F0;
            cursor: pointer;
        }
        .td02h_c:hover {
            color: #F000F0;
            cursor: pointer;
            text-shadow: 1px 1px red;
        }
        .resetfilters:hover {
            color: #8080FF;
            text-decoration: underline;
            cursor: pointer;
        }
        .cursorpointer:hover {
            cursor: pointer;
        }

        .klabc_a {
            color: red;
            font-weight: bold;
            text-decoration: underline double red;
        }
        .klabc_b {
            color: darkorange;
            font-weight: bold;
            text-decoration: underline dotted black;
        }
        .klabc_c {
            color: darkorange;
            font-weight: bold;
        }

        .ui-widget-content {
            background: #F9F9F9;
            color: #222222;
        }

        .ui-dialog {
            left: 0;
            outline: 0 none;
            padding: 0 !important;
            position: absolute;
            top: 0;
        }

        .ui-dialog .ui-dialog-content{
            background: none repeat scroll 0 0 transparent;
            border: 0 none;
            overflow: auto;
            position: relative;
            padding: 15px !important;
        }

        .ui-widget-header {
            background: slategrey;
            border: 0;
            color: white;
            font-weight: bold;
        }

        .ui-dialog.ui-dialog-titlebar {
            padding: 0.1em .5em;
            position: relative;
            font-size: 1em;
            background-color: lightgray;
        }

        .MessageReasonTooltipClass {
            max-width: 600px;
        }

        .order-tools-menu {
            width: 13em;
            position: absolute;
            display: none;
        }

        .mass-change-menu {
            width: 18em;
            position: absolute;
            display: none;
        }

        .delivery-date-menu {
            width: 18em;
            position: absolute;
            display: none;
        }

        .ui-menu .ui-menu-item.ui-state-focus{
            border: none;
            background-image: none;
            background-color: #eee;
        }

        .leftInquiryDialogButton {
            position: absolute;
            left: 16px;
            background-color: #9DC0F8;
        }
        .leftInquiryDialogButton:hover {
            background-color: #9DB0E8;
        }

        .rightInquiryDialogButton {
            background-color: #E0F0C0;
        }
        .rightInquiryDialogButton:hover {
            background-color: #C0C08F;
        }

        .list-item-selected {
            background-color: #9DB0E8;
            cursor: cell;
        }
        .list-item:hover {
            background-color: #BDE0FF;
            cursor: cell;
        }

        .blurry-text {
            color: transparent;
            text-shadow: 0 0 5px rgba(0,0,0,0.7);
        }


    </style>
</head>
<body style="font-size: 12px">
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid">
            @guest
                <a class="navbar-brand" href="{{ url('http://www.materom.ro') }}">
                    <img src="/images/logo.png" class="logo">
                </a>
            @else
                <a class="navbar-brand" href="{{ url('http://www.materom.ro') }}">
                    <img src="/images/logo.png" class="logo">
                </a>
                @php
                    $system_color = "#007bff";
                    if ("X".\Illuminate\Support\Facades\Auth::user()->sap_system == "X300") {
                        $system_color = "#00a07b";
                    }
                @endphp
                <div style="margin-left: -4rem; font-weight: bold; font-size: 200%; color: {{$system_color}};
                        background-color: lightyellow; padding: 5px; border-style: double;">
                    {{__("System")}}:&nbsp;{{App\Materom\System::$system_name}}
                </div>
                @if (trim(env("MATEROM_DISPLAYNAME", "")) != "")
                    <div style="margin-left: 2rem; font-weight: bold; font-size: 200%; color: red;">
                        {{trim(env("MATEROM_DISPLAYNAME", ""))}}
                    </div>
                @endif
            @endguest
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    <form action="language" method="post" style="padding-right: 10px;padding-top: 8px">
                        <select name="locale" onchange="this.form.submit()">
                            <option>{{trans('strings.language')}}</option>
                            <option value="ro">Română</option>
                            <option value="hu">Magyar</option>
                            <option value="de">Deutsch</option>
                            <option value="en">English</option>
                        </select>
                        {{ csrf_field() }}
                    </form>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" style="margin-top: 4px;" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __(Auth::user()->role) . " " . (Auth::user()->readonly == 1? (Auth::user()->pnad == 1 ? "PNAD " : "R/O "): "") . Auth::user()->username }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    @foreach (['danger', 'info', 'warning', 'success'] as $msg)
        @if (\Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }}"><b class="blinking-text">{!! \Session::pull('alert-'.$msg) !!}</b></div>
        @endif
    @endforeach
    <script>
        $(function(){
            $('div.alert').not('.alert-danger').delay(3000).fadeOut(500);
            $('div.alert').on('click', function () {$(this).fadeOut(100);});
            (function blink_alert_text() {
                $('.blinking-text').fadeOut(500).fadeIn(500, blink_alert_text);
            })();
        });
    </script>

    <main class="py-4" style="width:150%;margin-left:-25%">
        @yield('content')
    </main>
</div>
<div align="center" style="position:fixed;bottom:5px;width:100%">
    Copyright &copy; 2018 by Materom. All rights reserved.
</div>

<div class="ajaxloadermodal"><!-- ajaxload.info  --></div>
<script>
    $(function() {
        /*
        $body = $("body");
        $(document).on({
            ajaxStart: function() {$body.addClass("ajaxloading");},
            ajaxStop: function() {$body.removeClass("ajaxloading"); }
        });
        */
        $(document).tooltip();
    });

    $.fn.swapWith = function(that) {
        var $this = this;
        var $that = $(that);
        var $temp = $("<div>");
        $this.before($temp);
        $that.before($this);
        $temp.after($that).remove();
        return $this;
    }

</script>
</body>
</html>

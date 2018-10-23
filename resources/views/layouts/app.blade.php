<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Materom SRM</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.1.12.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.1.12.1.min.css') }}" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        html, body {
            overflow-x: hidden;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
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

        .order-button-accepted {
            background-image:url('/images/icons8-checkmark-50.png');
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

        .order-button-request {
            background-image:url('/images/icons8-greater-than-50.png');
            background-size:     contain;
            background-repeat:   no-repeat;
            background-position: center center;
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

        .orders-table-div {
            overflow-y: scroll;
            height: 700px;
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
            margin-top: 1vw;
            width: 100%;
            border: 1px solid black;
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
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->username }} <span class="caret"></span>
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
<div align="center" style="position:absolute;bottom:2px;width:100%">
    Copyright &copy; 2018 by Materom. All rights reserved.
</div>

<div class="ajaxloadermodal"><!-- ajaxload.info  --></div>
<script>
    $(function() {
        $body = $("body");

        /*
        $(document).on({
            ajaxStart: function() {$body.addClass("ajaxloading");},
            ajaxStop: function() {$body.removeClass("ajaxloading"); }
        });
        */
    });
</script>
</body>
</html>

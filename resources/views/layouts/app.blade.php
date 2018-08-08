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
        html, body{
            overflow-x: hidden;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .first{
            margin-left:-20px;
        }
        .selector{
            background-color: white;
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
            background-color: #eee;
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

    </style>
</head>
<body style="font-size: 12px">
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid">
            @guest
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="images/logo.png" class="logo">
                </a>
            @else
                <a class="navbar-brand" href="{{ url('/orders') }}">
                    <img src="images/logo.png" class="logo">
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
                            <option value="en">English</option>
                            <option value="ro">Romana</option>
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

    <main class="py-4" style="width:150%;margin-left:-25%">
        @yield('content')
    </main>
</div>
<div align="center" style="position:absolute;bottom:0;width:100%">
    Copyright &copy; 2018 by Materom. All rights reserved.
</div>
</body>
</html>

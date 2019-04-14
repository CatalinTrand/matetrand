@extends('layouts.app')

@section('content')
    @php
        $sap_system = trim(Auth::user()->sap_system);
        if(empty($sap_system)) $sap_system = "200";
        $currentsystem200 = "";
        $currentsystem300 = "";
        if ($sap_system == "200") $currentsystem200 = "selected";
        if ($sap_system == "300") $currentsystem300 = "selected";
        $readonly = "";
        if (Auth::user()->readonly == 1) $readonly = "checked";
    @endphp
    @if (Auth::user() && Auth::user()->role == 'Administrator')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card input-group input-group-lg">
                        <div class="card-header"><a style="padding-right: 20px" href="/users">&larr; Back</a>{{ __('Register Panel') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="role"
                                           class="col-md-4 col-form-label text-md-right">{{ __('User Type') }}</label>

                                    <div class="col-md-6">
                                        <select id="role" type="text" class="form-control" name="role" required
                                                autofocus onchange="selectCheck(this);">
                                            <option>Administrator</option>
                                            <option>Furnizor</option>
                                            <option>Referent</option>
                                            <option>CTV</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-4 col-form-label text-md-right">User ID</label>

                                    <div class="col-md-6">
                                        <input class="form-control" id="id" type="text" name="id" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sap_system"
                                           class="col-md-4 col-form-label text-md-right">{{ __('System') }}</label>

                                    <div class="col-md-6">
                                        <select id="sap_system" type="text" class="form-control" name="sap_system" required
                                                onchange="selectCheck(this);">
                                            <option value="200" {{$currentsystem200}}>200</option>
                                            <option value="300" {{$currentsystem300}}>300</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

                                    <div class="col-md-6">
                                        <input class="form-control" id="username" type="text" name="username" required>
                                    </div>
                                </div>

                                <div class="form-group row" id="lifnr_div" style="display: none;">
                                    <label for="lifnr"
                                           class="col-md-4 col-form-label text-md-right">Vendor</label>

                                    <div class="col-md-6">
                                        <input class="form-control" id="lifnr" type="text" name="lifnr">
                                    </div>
                                </div>

                                <div class="form-group row" id="ekgrp_div" style="display: none;">
                                    <label for="ekgrp"
                                           class="col-md-4 col-form-label text-md-right">Purchasing group</label>

                                    <div class="col-md-6">
                                        <input class="form-control" id="ekgrp" type="text" name="ekgrp">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                               class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                               name="email" value="{{ old('email') }}" required>

                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="lang"
                                           class="col-md-4 col-form-label text-md-right">Language</label>

                                    <div class="col-md-6">
                                        <select id="lang" type="text" class="form-control" name="lang" required>
                                            <option>RO</option>
                                            <option>HU</option>
                                            <option>DE</option>
                                            <option>EN</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="readonly"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Read-only') }}</label>

                                    <div class="col-md-6">
                                        <input type="checkbox" style="float: left; margin-top: 1em;" id="readonly" name="readonly" {{$readonly}}>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password"
                                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                               name="password" required>

                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password-confirm"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control"
                                               name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Register') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Access denied!') }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        function selectCheck(nameSelect)
        {
            var lifnr_div = document.getElementById("lifnr_div");
            var ekgrp_div = document.getElementById("ekgrp_div");

            if(nameSelect){
                if(nameSelect.value == "Referent" || nameSelect.value == "Furnizor"){
                    if(nameSelect.value == "Referent") {
                        ekgrp_div.style.display = "";
                        lifnr_div.style.display = "none";
                    } else {
                        ekgrp_div.style.display = "none";
                        lifnr_div.style.display = "";
                    }
                }
                else {
                    lifnr_div.style.display = "none";
                    ekgrp_div.style.display = "none";
                }
            }
            else{
                lifnr_div.style.display = "none";
                ekgrp_div.style.display = "none";
            }
        }
    </script>
@endsection

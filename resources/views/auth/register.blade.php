@extends('layouts.app')

@section('content')
    @php
        $sap_system = trim(\Illuminate\Support\Facades\Auth::user()->sap_system);
        if(empty($sap_system)) $sap_system = "200";
        $currentsystem200 = "";
        $currentsystem300 = "";
        if ($sap_system == "200") $currentsystem200 = "selected";
        if ($sap_system == "300") $currentsystem300 = "selected";
        $readonly = "";
        if (\Illuminate\Support\Facades\Auth::user()->readonly == 1) $readonly = "checked";
        $none = "";
        if (\Illuminate\Support\Facades\Auth::user()->none == 1) $none = "checked";
        $mirror_user1 = trim(\Illuminate\Support\Facades\Auth::user()->mirror_user1);
        $ctvrole = "";
        if (\Illuminate\Support\Facades\Auth::user()->role == "CTV") $ctvrole = "selected";
    @endphp
    @if (\Illuminate\Support\Facades\Auth::user() && (\Illuminate\Support\Facades\Auth::user()->role == 'Administrator' ||(\Illuminate\Support\Facades\Auth::user()->role == 'CTV' && \Illuminate\Support\Facades\Auth::user()->ctvadmin == 1)))
        <div class="container" style="width: 60%;">
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <div class="card input-group input-group-lg">
                        <div class="card-header"><a style="padding-right: 20px" href="/users">&larr; Back</a>{{ __('Register Panel') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="role" class="col-md-2 col-form-label text-md-left">{{ __('User Type') }}</label>
                                    <div class="col-md-4">
                                        <select id="role" type="text" class="form-control" name="role" required
                                                autofocus onchange="selectCheck(this);">
                                            @if($ctvrole == "")
                                                <option>Administrator</option>
                                                <option>Furnizor</option>
                                                <option>Referent</option>
                                            @endif
                                            <option {{$ctvrole}}>CTV</option>
                                        </select>
                                    </div>

                                    <div id="ctvadmin_div" class="col-md-5" style="display: block;">
                                        <input type="checkbox" style="float: left; margin-top: 1em;" id="ctvadmin" name="ctvadmin">
                                        <label for="ctvadmin" style="padding-left: 5px; padding-top: 0.75em;"
                                               class="col-form-label text-md-left">{{ __('Limited CTV administrator') }}</label>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-2 col-form-label text-md-left">User ID</label>

                                    <div class="col-md-3">
                                        <input class="form-control" id="id" type="text" name="id" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sap_system"
                                           class="col-md-2 col-form-label text-md-left">{{ __('System') }}</label>

                                    <div class="col-md-1" style="min-width: 7rem;">
                                        <select id="sap_system" type="text" class="form-control" name="sap_system" required
                                                onchange="$('#mirror_user1').prev().text('{{ __('Mirror user') }}' + ' ' + (this.options[this.selectedIndex].value == '200'?'300':'200'));">
                                            <option value="200" {{$currentsystem200}}>200</option>
                                            <option value="300" {{$currentsystem300}}>300</option>
                                        </select>
                                    </div>
                                    <div id="mirror_user1_div" class="col-md-5" style="display: block; padding-left: 5px;">
                                        <label for="mirror_user1" style=""
                                               class="col-form-label text-md-left">{{__('Mirror user')}} {{$currentsystem200 == "" ? "200" : " 300"}}</label>
                                        <input id="mirror_user1" type="text" name="mirror_user1" class="form-control"
                                               style="display: inline-block; margin-left: 5px; width: 7.3rem;" maxlength="20">
                                    </div>
                                </div>

                                <div class="form-group row" id="rgroup_div">
                                    <label for="rgroup"
                                           class="col-md-2 col-form-label text-md-left">Reporting group</label>
                                    <div class="col-md-2">
                                        <input class="form-control" id="rgroup" type="text" name="rgroup" maxlength="2" style="width: 3rem;">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-2 col-form-label text-md-left">{{ __('Username') }}</label>

                                    <div class="col-md-4">
                                        <input class="form-control" id="username" type="text" name="username" required>
                                    </div>
                                </div>

                                <div class="form-group row" id="lifnr_div" style="display: none;">
                                    <label for="lifnr"
                                           class="col-md-2 col-form-label text-md-left">Vendor</label>

                                    <div class="col-md-3">
                                        <input class="form-control" id="lifnr" type="text" name="lifnr">
                                    </div>
                                </div>

                                <div class="form-group row" id="ekgrp_div" style="display: none;">
                                    <label for="ekgrp"
                                           class="col-md-2 col-form-label text-md-left">Purchasing group</label>

                                    <div class="col-md-2">
                                        <input class="form-control" id="ekgrp" type="text" name="ekgrp">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-2 col-form-label text-md-left">{{ __('E-Mail Address') }}</label>

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
                                           class="col-md-2 col-form-label text-md-left">Language</label>

                                    <div class="col-md-3">
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
                                           class="col-md-2 col-form-label text-md-left">{{ __('Read-only') }}</label>
                                    <div class="col-md-5">
                                        <input type="checkbox" style="float: left; margin-top: 1em;" id="readonly" name="readonly" {{$readonly}}>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="none"
                                           class="col-md-2 col-form-label text-md-left">{{ __('Empty list (NONE)') }}</label>
                                    <div class="col-md-5">
                                        <input id="none" type="checkbox" name="none" style="float: left; margin-top: 1em;" {{$none}}>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password"
                                           class="col-md-2 col-form-label text-md-left">{{ __('Password') }}</label>

                                    <div class="col-md-5">
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
                                           class="col-md-2 col-form-label text-md-left">{{ __('Confirm Password') }}</label>

                                    <div class="col-md-5">
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
        $(document).ready(function () {
            selectCheck(document.getElementById("role"));
        });
        function selectCheck(nameSelect)
        {
            var lifnr_div = document.getElementById("lifnr_div");
            var ekgrp_div = document.getElementById("ekgrp_div");
            var ctvadmin_div = document.getElementById("ctvadmin_div");

            if(nameSelect){
                if(nameSelect.value == "Referent" || nameSelect.value == "Furnizor"){
                    if(nameSelect.value == "Referent") {
                        ekgrp_div.style.display = "";
                        lifnr_div.style.display = "none";
                    } else {
                        ekgrp_div.style.display = "none";
                        lifnr_div.style.display = "";
                    }
                    ctvadmin_div.style.display = "none";
                }
                else {
                    lifnr_div.style.display = "none";
                    ekgrp_div.style.display = "none";
                    if (nameSelect.value == "CTV") ctvadmin_div.style.display = "";
                    else ctvadmin_div.style.display = "none";
                }
            }
            else{
                lifnr_div.style.display = "none";
                ekgrp_div.style.display = "none";
                ctvadmin_div.style.display = "none";
            }
        }
    </script>
@endsection

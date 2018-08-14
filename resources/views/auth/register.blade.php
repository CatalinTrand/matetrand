@extends('layouts.app')

@section('content')
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
                                                autofocus>
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
                                    <label for="username"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

                                    <div class="col-md-6">
                                        <input class="form-control" id="username" type="text" name="username" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="lifnr"
                                           class="col-md-4 col-form-label text-md-right">Vendor</label>

                                    <div class="col-md-6">
                                        <input class="form-control" id="lifnr" type="text" name="lifnr" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="ekgrp"
                                           class="col-md-4 col-form-label text-md-right">Purchasing group</label>

                                    <div class="col-md-6">
                                        <input class="form-control" id="ekgrp" type="text" name="ekgrp" required>
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
                                        <select id="lang" type="text" class="form-control" name="lang" required
                                                autofocus>
                                            <option>EN</option>
                                            <option>RO</option>
                                        </select>
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
@endsection

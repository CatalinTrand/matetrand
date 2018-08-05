@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Super Admin'))
        @php
            header("/");
            exit();
        @endphp
    @endif
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                            <p class="card-line first selector">Roles</p><p class="card-line"><a href="/home">Users</a></p><p class="card-line"><a href="/messages">Messages</a></p><p class="card-line"><a href="/orders">Comenzi</a></p>
                    </div>

                    <div class="card-body">
                        No roles to show.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
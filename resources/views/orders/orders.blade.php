@extends('layouts.app')

@section('content')
    @guest
        @php
            header("/");
            exit();
        @endphp
    @endguest
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        @if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Administrator" ) == 0)
                            <p class="card-line first"><a href="/roles">Roles</a></p>
                            <p class="card-line"><a href="/home">Users</a></p>
                            <p class="card-line"><a href="/messages">Messages</a></p>
                            <p class="card-line selector">Comenzi</p>
                        @else
                            <p class="card-line first"><a href="/Messages">Messages</a></p>
                            <p class="card-line selector">Comenzi</p>
                        @endif
                    </div>

                    <div class="card-body">
                        No orders to show.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
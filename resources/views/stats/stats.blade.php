@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Administrator'))
        @php
            header("/");
            exit();
        @endphp
    @endif

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Administrator" ) == 0)
                            <a href="/roles">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line first">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-administrative-tools-48.png'/>
                                    {{__("Roles")}}
                                </p>
                            </a>
                            <a href="/users">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line selector">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-user-account-80.png'/>
                                    {{__("Users")}}
                                </p>
                            </a>
                            <a href="/messages">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-chat-80.png'/>
                                    {{__("Messages")}}
                                </p>
                            </a>
                            <a href="/orders">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p>
                            </a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;'
                                       src='/images/icons8-area-chart-64.png'/>
                                {{__("Statistics")}}
                            </p>
                        @else
                            <p style="display: inline-block;" class="card-line first">{{__('Messages')}}</p>
                            <a href="/orders"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">{{__('Orders')}}</p></a>
                        @endif
                    </div>

                    <div class="card-body" style="padding-bottom: 0px;">
                        <div style="border: 1px solid black; border-radius: 0.5rem; padding: 4px; height: 2.2rem;">
                            <div class="container row" style="display: block; max-width: 100%;">
                                <table style="border: none; width: 100%;">
                                    <tr>
                                        <td>
                                            {{__('Supplier')}}:
                                            <select id="selected-supplier" class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                                    onchange="return false;">
                                                <option value="1">21376 Test</option>
                                            </select>
                                        </td>
                                        <td width="100px" style="text-align: right;">
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

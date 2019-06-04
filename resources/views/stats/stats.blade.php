@extends('layouts.app')

@section('content')
    @if (!\Illuminate\Support\Facades\Auth::user() || \Illuminate\Support\Facades\Auth::user()->role == "CTV")
        @php
            header("Location: /users");
            exit();
        @endphp
    @endif

    @php
        $message_count = App\Materom\Orders::unreadMessageCount();
        $message_svg = "";
        if ($message_count > 0) {
            if ($message_count > 99) $message_count = '>99';
            else $message_count = "&nbsp;" . $message_count;
            $message_svg = '&nbsp;<svg style="vertical-align: middle;" width="41" height="38">
                  <g>
                  <rect x="2" y="2" rx="8" ry="8" width="35" height="32" style="fill:red;stroke:black;stroke-width:2;opacity:0.99" />
                  <text x="5" y="23" font-family="Arial" font-size="16" fill="white">' . $message_count . '</text>
                  </g>
                </svg>';
    }
    @endphp

    <script src="{{ asset('js/Chart.bundle.min.js') }}"></script>

    <div class="container-fluid" style="height: 89vh;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if(\Illuminate\Support\Facades\Auth::user()->role == "Administrator")
                            <a href="/roles">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-administrative-tools-48.png'/>
                                    {{__("Roles")}}
                                </p>
                            </a>
                            <a href="/users">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line selector">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                    {{__("Users")}}
                                </p>
                            </a>
                            <a href="/messages">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                    {{__("Messages")}}
                                </p>
                            </a>
                            <a href="/orders">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p>
                            </a>
                        @else
                            @if (\Illuminate\Support\Facades\Auth::user()->role == "CTV" && \Illuminate\Support\Facades\Auth::user()->ctvadmin == 1)
                                <a href="/users">
                                    <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">
                                        <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                        {{__("Users")}}
                                    </p>
                                </a>
                                <a href="/messages">
                                    <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                        <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                        {{__('Messages')}}{!!$message_svg!!}
                                    </p>
                                </a>
                            @else
                                <a href="/messages">
                                    <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">
                                        <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                        {{__('Messages')}}{!!$message_svg!!}
                                    </p>
                                </a>
                            @endif

                            <a href="/orders">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p>
                            </a>
                        @endif
                        <p style="display: inline-block;" class="card-line selector">
                            <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-area-chart-64.png'/>
                            {{__("Statistics")}}
                        </p>
                    </div>

                    <div class="card-body" style="padding-bottom: 0px;">
                        <div style="border: 1px solid black; border-radius: 0.5rem; padding: 4px; height: 8rem;">
                            <div class="container row" style="display: block; max-width: 100%;">
                                <table style="border: none; width: 100%;">
                                    <colgroup>
                                        <col style="width: 10rem; padding: 2px;">
                                        <col style="width: 24rem; padding: 2px;">
                                        <col style="width: 30rem; padding: 2px;">
                                        <col style="padding: 2px;">
                                        <col style="width: 100px;">

                                    </colgroup>
                                    <tr>
                                        <td>{{__('Graphical chart type')}}:</td>
                                        <td>
                                            <select id="graphical-chart-type" class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                                    onchange="return false;">
                                                <option value="A" selected>{{__("Delayed vs. open number of purchase orders/items")}}</option>
                                            </select>
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Supplier')}}:</td>
                                        <td>
                                            <select id="selected-supplier" class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                                    onchange="fill_ekgrp_of_lifnr();return false;" required>
                                            @php

                                                if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor") {
                                                    $lifnr = \Illuminate\Support\Facades\Auth::user()->lifnr;
                                                    $lifnr_name = trim(\App\Materom\SAP\MasterData::getLifnrName($lifnr));
                                                    $xlifnr = \App\Materom\SAP::alpha_output($lifnr);
                                                    echo "<option value='$lifnr'>$xlifnr $lifnr_name</option>";
                                                } else {
                                                    $lifnrs = \Illuminate\Support\Facades\DB::select("select distinct lifnr from ".
                                                        \App\Materom\System::$table_stat_orders);
                                                    foreach($lifnrs as $lifnr) {
                                                        $lifnr = $lifnr->lifnr;
                                                        $lifnr_name = trim(\App\Materom\SAP\MasterData::getLifnrName($lifnr));
                                                        $xlifnr = \App\Materom\SAP::alpha_output($lifnr);
                                                        echo "<option value='$lifnr'>$xlifnr $lifnr_name</option>";
                                                    }
                                            }
                                            @endphp
                                            </select>
                                        </td>
                                        <td>
                                            {{__('Refferal')}}:&nbsp;
                                            <select id="selected-ekgrp" class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                                    onchange="return false;">
                                                <option value="***" selected>{{__("Toti")}}</option>
                                            </select>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            {{__('Order type')}}:&nbsp;
                                            <select id="selected-otype" class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;">
                                                <option value="A" selected>{{__("All")}}</option>
                                                <option value="S">{{__("Stock order")}}</option>
                                                <option value="C">{{__("Client order")}}</option>
                                            </select>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Starting date')}}:</td>
                                        <td>
                                            @php
                                                $now = substr(now(), 0, 10);
                                            @endphp
                                            <input type="text" id="starting-date" class="form-control-sm"
                                                   style="height: 1.6rem; width: 6rem;" value="{{$now}}">
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Interval')}}:</td>
                                        <td>
                                            <select id="reporting-interval" class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                                    onchange="return false;">
                                                <option value="A">{{__("One week")}}</option>
                                                <option value="B">{{__("Two weeks")}}</option>
                                                <option value="C">{{__("One month")}}</option>
                                            </select>
                                        </td>
                                        <td colspan="2"></td>
                                        <td width="100px" style="text-align: right; padding: 2px;">
                                            <button type="button" style="margin-left: 2px; height: 1.5rem; "
                                                    onclick="redraw_canvas();return false;">{{__('Display')}}</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <canvas id="m1chart" style="margin-top: 4px; margin-left: 26vw; max-width: 98vw; max-height: 69vh;"></canvas>
        </div>

    </div>


    <script>
        var m1chart;
        $(function() {
            $("#starting-date").datepicker({dateFormat: "yy-mm-dd"});
            var ctx = document.getElementById('m1chart').getContext('2d');
            m1chart = new Chart(ctx, {
                type: 'bar',
                data: null,
                options: {
                    title: {
                        display: true,
                        text: '',
                        fontStyle: 'bold',
                        fontSize: 24,
                        lineHeight: 2
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    responsive: true,
                    legend: {
                        position: 'bottom'
                    },
                    scales: {
                        xAxes: [{
                            stacked: true,
                            gridLines: {
                                display: false
                            },
                        }],
                        yAxes: [{
                            stacked: true,
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }]
                    }
                }
            });
            fill_ekgrp_of_lifnr();
        });

        function redraw_canvas() {
            var _data, _status;
            var type, lifnr, sdate, interval, ekgrp, otype;
            type = $("#graphical-chart-type").val();
            lifnr = $("#selected-supplier").val();
            sdate = $("#starting-date").val().trim().substr(0, 10);
            interval = $("#reporting-interval").val();
            ekgrp = $("#selected-ekgrp").val();
            otype = $("#selected-otype").val();
            $('body').addClass('ajaxloading');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.get("webservice/get_stat_data",
                {
                    type: type,
                    lifnr: lifnr,
                    sdate: sdate,
                    interval: interval,
                    ekgrp: ekgrp,
                    otype: otype
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            $('body').removeClass('ajaxloading');
            if (_status == undefined || _status == null) {
                alert("An error occurred loading chart data");
                return;
            }
            m1chart.options.title.text = _data.title;
            m1chart.data = _data.data;
            m1chart.update();
            if (_data.debug != undefined && _data.debug != null)
                alert(_data.debug);
        }

        function fill_ekgrp_of_lifnr() {
            var _data, _status;
            let type = $("#graphical-chart-type").val();
            let lifnr = $("#selected-supplier").val();
            let sdate = $("#starting-date").val().trim().substr(0, 10);
            let interval = $("#reporting-interval").val();
            $('#selected-ekgrp')
                .find('option')
                .remove()
                .end()
                .append('<option value="***" selected>{{__("Toti")}}</option>')
                .val('***');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.get("webservice/get_stat_ekgrp_of_lifnr",
                {
                    type: type,
                    lifnr: lifnr,
                    sdate: sdate,
                    interval: interval
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_status == undefined || _status == null) {
                return;
            }
            for (i = 0; i < _data.length; i++)
                $('#selected-ekgrp')
                    .append('<option value="' + _data[i].ekgrp + '">' + _data[i].ekgrp + ' ' + _data[i].name+ '</option>');
        }

    </script>

@endsection

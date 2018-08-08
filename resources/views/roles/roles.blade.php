@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Administrator'))
        @php
            header("/");
            exit();
        @endphp
    @endif
    @php
        use App\Materom\RFCData;
        use App\Materom\globalRFCData;
        use Illuminate\Support\Facades\DB;

        //load configuration from the database
        $adminData = DB::select("select * from roles where rfc_role = 'administrator'");
        if($adminData)
            $adminData = $adminData[0];
        else
            $adminData = new RFCData();

        $referentData = DB::select("select * from roles where rfc_role = 'referent'");
        if($referentData)
            $referentData = $referentData[0];
        else
            $referentData = new RFCData();

        $furnizorData = DB::select("select * from roles where rfc_role = 'furnizor'");
        if($furnizorData)
            $furnizorData = $furnizorData[0];
        else
            $furnizorData = new RFCData();

        $ctvData = DB::select("select * from roles where rfc_role = 'ctv'");
        if($ctvData)
            $ctvData = $ctvData[0];
        else
            $ctvData = new RFCData();

        $global = DB::select("select * from global_rfc_config");
        if($global)
            $global = $global[0];
        else{
            $global = new globalRFCData();
        }



    @endphp
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first selector">Roles</p>
                        <a href="/users"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">Users</p></a>
                        <a href="/messages"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">Messages</p></a>
                        <a href="/orders"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">Comenzi</p></a>
                    </div>

                    <div class="card-body">
                        <div style="width:33%;">
                            <form method="POST" action="/roles/globalUpdate">
                                {{ csrf_field() }}
                                <div class="card-header">
                                    Global RFC Settings
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="router"
                                               class="col-md-6 col-form-label text-md-right">RFC Router</label>

                                        <div class="col-md-6">
                                            <input id="rfc_router" type="text" name="rfc_router" required
                                                   value="{{$global->rfc_router}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="server"
                                               class="col-md-6 col-form-label text-md-right">RFC Server</label>

                                        <div class="col-md-6">
                                            <input id="rfc_server" type="text" name="rfc_server" required
                                                   value="{{$global->rfc_server}}">
                                        </div>
                                    </div>
                                    <input type="submit" value="Save" style="border-top: 4px black;margin-left: 74px;height: 24px">
                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <div style="width: 25%;" class="role-card">
                                <div class="card-header">
                                    Administrator
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="a_rfc_sysnr" type="text" name="rfc_sysnr" required
                                                       value="{{$adminData->rfc_sysnr}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="a_rfc_client" type="text" name="rfc_client" required
                                                       value="{{$adminData->rfc_client}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="a_rfc_user" type="text" name="rfc_user" required
                                                       value="{{$adminData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="a_rfc_passwd" type="text" name="rfc_passwd" required
                                                       value="{{$adminData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <button type="button" id="Administrator_test"
                                                onclick="rfc_ping('a');return false;"
                                                style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test
                                        </button>
                                        <input type="hidden" name="role" value="administrator">
                                        <input type="submit" value="Save" style="border-top: 4px black;width: 100%">
                                    </form>
                                </div>
                            </div>
                            <div style="width: 25%;" class="role-card">
                                <div class="card-header">
                                    Furnizor
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="f_rfc_sysnr" type="text" name="rfc_sysnr" required
                                                       value="{{$furnizorData->rfc_sysnr}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="f_rfc_client" type="text" name="rfc_client" required
                                                       value="{{$furnizorData->rfc_client}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="f_rfc_user" type="text" name="rfc_user" required
                                                       value="{{$furnizorData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="f_rfc_passwd" type="text" name="rfc_passwd" required
                                                       value="{{$furnizorData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <button id="Furnizor_test"
                                                style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test
                                        </button>
                                        <input type="hidden" name="role" value="furnizor">
                                        <input type="submit" value="Save" style="border-top: 4px black;width: 100%">
                                    </form>
                                </div>
                            </div>
                            <div style="width: 25%;" class="role-card">
                                <div class="card-header">
                                    Referent
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="r_rfc_sysnr" type="text" name="rfc_sysnr" required
                                                       value="{{$referentData->rfc_sysnr}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="r_rfc_client" type="text" name="rfc_client" required
                                                       value="{{$referentData->rfc_client}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="r_rfc_user" type="text" name="rfc_user" required
                                                       value="{{$referentData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="r_rfc_passwd" type="text" name="rfc_passwd" required
                                                       value="{{$referentData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <button id="Referent_test"
                                                style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test
                                        </button>
                                        <input type="hidden" name="role" value="referent">
                                        <input type="submit" value="Save" style="border-top: 4px black;width: 100%">
                                    </form>
                                </div>
                            </div>
                            <div style="width: 25%;" class="role-card">
                                <div class="card-header">
                                    CTV
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="c_rfc_sysnr" type="text" name="rfc_sysnr" required
                                                       value="{{$ctvData->rfc_sysnr}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="c_rfc_client" type="text" name="rfc_client" required
                                                       value="{{$ctvData->rfc_client}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="c_rfc_user" type="text" name="rfc_user" required
                                                       value="{{$ctvData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="c_rfc_passwd" type="text" name="rfc_passwd" required
                                                       value="{{$ctvData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <button id="CTV_test"
                                                style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test
                                        </button>
                                        <input type="hidden" name="role" value="ctv">
                                        <input type="submit" value="Save" style="border-top: 4px black;width: 100%">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function rfc_ping(prefix) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.post("webservice/rfcping",
                {
                    rfc_router: $("#" + prefix + "rfc_router").val(),
                    rfc_server: $("#" + prefix + "_rfc_server").val(),
                    rfc_sysnr: $("#" + prefix + "_rfc_sysnr").val(),
                    rfc_client: $("#" + prefix + "_rfc_client").val(),
                    rfc_user: $("#" + prefix + "_rfc_user").val(),
                    rfc_password: $("#" + prefix + "_rfc_password").val()
                },
                function(data, status){
                    $("body").removeClass("ajaxloading");
                    alert("Data: " + data + "\nStatus: " + status);
                });
            jQuery.ajaxSetup({async: true});
        }
    </script>
@endsection
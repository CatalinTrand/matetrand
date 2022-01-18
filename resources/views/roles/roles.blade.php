@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Administrator'))
        @php
            header("Location: /");
            exit();
        @endphp
    @endif
    @php
        use App\Materom\RFCData;
        use App\Materom\globalRFCData;
        use Illuminate\Support\Facades\DB;

        // Load global configuration from the database
        $adminData = DB::select("select * from ". App\Materom\System::$table_roles ." where rfc_role = 'administrator'");
        if($adminData) $adminData = $adminData[0];
        else $adminData = new RFCData('', '', '', '', '', '');

        $referentData = DB::select("select * from ". App\Materom\System::$table_roles ." where rfc_role = 'referent'");
        if($referentData) $referentData = $referentData[0];
        else $referentData = new RFCData('', '', '', '', '', '');

        $furnizorData = DB::select("select * from ". App\Materom\System::$table_roles ." where rfc_role = 'furnizor'");
        if($furnizorData) $furnizorData = $furnizorData[0];
        else $furnizorData = new RFCData('', '', '', '', '', '');

        $ctvData = DB::select("select * from ". App\Materom\System::$table_roles ." where rfc_role = 'ctv'");
        if($ctvData) $ctvData = $ctvData[0];
        else $ctvData = new RFCData('', '', '', '', '', '');

        $global = DB::select("select * from ". App\Materom\System::$table_global_rfc_config);
        if($global) $global = $global[0];
        else $global = new globalRFCData();
        $oldglobal = $global;

        // Load EWM configuration from the database
        $adminEWMData = DB::select("select * from ". App\Materom\System::$table_ewm_roles ." where rfc_role = 'administrator'");
        if($adminEWMData) $adminEWMData = $adminEWMData[0];
        else $adminEWMData = new RFCData('', '', '', '', '', '');

        $referentEWMData = DB::select("select * from ". App\Materom\System::$table_ewm_roles ." where rfc_role = 'referent'");
        if($referentEWMData) $referentEWMData = $referentEWMData[0];
        else $referentEWMData = new RFCData('', '', '', '', '', '');

        $ewm = DB::select("select * from ". App\Materom\System::$table_ewm_rfc_config);
        if($ewm) $ewm = $ewm[0];
        else $ewm = new globalRFCData();
        $oldewm = $ewm;

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

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first selector">
                            <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-administrative-tools-48.png'/>
                            {{__("Roles")}}
                        </p>
                        <a href="/users"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                            <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                            {{__("Users")}}
                        </p></a>
                        <a href="/messages"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                            <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                            {{__("Messages")}}{!!$message_svg!!}
                        </p></a>
                        <a href="/orders"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                            <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                            {{__("Orders")}}
                        </p></a>
                        <a href="/stats"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                            <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-area-chart-64.png'/>
                            {{__("Statistics")}}
                        </p></a>
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div style="width: 28%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('Global RFC Settings')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/globalUpdate">
                                        {{ csrf_field() }}

                                        <div style="height: 7rem;">
                                            <div class="form-group row">
                                                <label for="rfc_router"
                                                       class="col-md-5 text-md-left spaced-label">{{__('RFC Router')}}</label>

                                                <div class="col-md-7">
                                                    <input id="rfc_router" type="text" name="rfc_router" class="spaced-input"
                                                           value="{{$global->rfc_router}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rfc_server"
                                                       class="col-md-5 text-md-left spaced-label">{{__('RFC Server')}}</label>

                                                <div class="col-md-7">
                                                    <input id="rfc_server" type="text" name="rfc_server" class="spaced-input" required
                                                           value="{{$global->rfc_server}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rfc_sysnr"
                                                       class="col-md-5 text-md-left spaced-label">{{__('RFC SysNr')}}</label>

                                                <div class="col-md-7">
                                                    <input id="rfc_sysnr" type="text" name="rfc_sysnr" class="spaced-input" required
                                                           value="{{$global->rfc_sysnr}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rfc_client"
                                                       class="col-md-5 text-md-left spaced-label">{{__('RFC Client')}}</label>

                                                <div class="col-md-7">
                                                    <input id="rfc_client" type="text" name="rfc_client" class="spaced-input" required
                                                           value="{{$global->rfc_client}}">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="submit" class="background-image-save" value="    {{__('Save')}}" style="width: 10rem; height: 32px;">
                                    </form>
                                </div>
                            </div>
                            <div style="width: 18%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('Administrator')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="rfc_user"
                                                   class="col-md-5 text-md-left spaced-label">{{__('RFC User')}}</label>

                                            <div class="col-md-6">
                                                <input id="a_rfc_user" type="text" name="rfc_user" class="spaced-input" required
                                                       value="{{$adminData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="rfc_passwd"
                                                   class="col-md-5 text-md-left spaced-label">{{__('RFC Password')}}</label>

                                            <div class="col-md-6">
                                                <input id="a_rfc_passwd" type="password" name="rfc_passwd" class="spaced-input" required
                                                       value="{{$adminData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-top: 0.8rem;">
                                            <button type="button" id="Administrator_test" class="background-image-pass-fail"
                                                    onclick="rfc_ping('a', '{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');return false;"
                                                    style="width: 45%; height: 1.5rem;">{{__('Test')}}
                                            </button>
                                            <input type="hidden" name="rfc_role" value="administrator">&nbsp;&nbsp;
                                            <input type="submit" value="{{__('Save')}}" class="background-image-save"
                                                   onclick="return check_rfc_global_data('{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');"
                                                   style="width: 45%; height: 1.5rem;">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div style="width: 18%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('Supplier')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="rfc_user"
                                                   class="col-md-5 text-md-left spaced-label">{{__('RFC User')}}</label>

                                            <div class="col-md-6">
                                                <input id="f_rfc_user" type="text" name="rfc_user" class="spaced-input" required
                                                       value="{{$furnizorData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="rfc_passwd"
                                                   class="col-md-5 text-md-left spaced-label">{{__('RFC Password')}}</label>

                                            <div class="col-md-6">
                                                <input id="f_rfc_passwd" type="password" name="rfc_passwd" class="spaced-input" required
                                                       value="{{$furnizorData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-top: 0.8rem;">
                                            <button id="Furnizor_test" class="background-image-pass-fail"
                                                    onclick="rfc_ping('f', '{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');return false;"
                                                    style="width: 45%; height: 1.5rem;">{{__('Test')}}
                                            </button>
                                            <input type="hidden" name="rfc_role" value="furnizor">&nbsp;&nbsp;
                                            <input type="submit" value="{{__('Save')}}" class="background-image-save"
                                                   onclick="return check_rfc_global_data('{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');"
                                                   style="width: 45%; height: 1.5rem;">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div style="width: 18%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('Refferal')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="rfc_user"
                                                   class="col-md-5 text-md-left spaced-label">{{__('RFC User')}}</label>

                                            <div class="col-md-6">
                                                <input id="r_rfc_user" type="text" name="rfc_user" class="spaced-input" required
                                                       value="{{$referentData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="rfc_passwd"
                                                   class="col-md-5 text-md-left spaced-label">{{__('RFC Password')}}</label>

                                            <div class="col-md-6">
                                                <input id="r_rfc_passwd" type="password" name="rfc_passwd" class="spaced-input" required
                                                       value="{{$referentData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-top: 0.8rem;">
                                            <button id="Referent_test" class="background-image-pass-fail"
                                                    onclick="rfc_ping('r', '{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');return false;"
                                                    style="width: 45%; height: 1.5rem;">{{__('Test')}}
                                            </button>
                                            <input type="hidden" name="rfc_role" value="referent">&nbsp;&nbsp;
                                            <input type="submit" value="{{__('Save')}}" class="background-image-save"
                                                   onclick="return check_rfc_global_data('{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');"
                                                   style="width: 45%; height: 1.5rem;">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div style="width: 18%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('CTV')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/roleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="rfc_user"
                                                   class="col-md-6 text-md-left spaced-label">{{__('RFC User')}}</label>

                                            <div class="col-md-5">
                                                <input id="c_rfc_user" type="text" name="rfc_user" class="spaced-input" required
                                                       value="{{$ctvData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="rfc_password"
                                                   class="col-md-6 text-md-left spaced-label">{{__('RFC Password')}}</label>

                                            <div class="col-md-5">
                                                <input id="c_rfc_passwd" type="password" name="rfc_passwd" class="spaced-input" required
                                                       value="{{$ctvData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="user1"
                                                   class="col-md-6 text-md-left spaced-label">{{__('Fallback CTV user')}}</label>

                                            <div class="col-md-5">
                                                <input id="c_user1" type="text" name="user1" class="spaced-input" value="{{$ctvData->user1}}">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-top: 0.8rem;">
                                            <button id="CTV_test" class="background-image-pass-fail"
                                                    onclick="rfc_ping('c', '{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');return false;"
                                                    style="width: 45%; height: 1.5rem;">{{__('Test')}}
                                            </button>
                                            <input type="hidden" name="rfc_role" value="ctv">&nbsp;&nbsp;
                                            <input type="submit" value="{{__('Save')}}" class="background-image-save"
                                                   onclick="return check_rfc_global_data('{{$oldglobal->rfc_router}}','{{$oldglobal->rfc_server}}','{{$oldglobal->rfc_sysnr}}','{{$oldglobal->rfc_client}}');"
                                                   style="width: 45%; height: 1.5rem;">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div style="width: 28%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('EWM RFC Settings')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/EWMUpdate">
                                        {{ csrf_field() }}

                                        <div style="height: 7rem;">
                                            <div class="form-group row">
                                                <label for="rfc_router" class="col-md-5 text-md-left spaced-label">{{__('RFC Router')}}</label>
                                                <div class="col-md-6">
                                                    <input id="ewm_rfc_router" type="text" name="rfc_router" class="spaced-input"
                                                           value="{{$ewm->rfc_router}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rfc_server" class="col-md-5 text-md-left spaced-label">{{__('RFC Server')}}</label>
                                                <div class="col-md-6">
                                                    <input id="ewm_rfc_server" type="text" name="rfc_server" class="spaced-input" required
                                                           value="{{$ewm->rfc_server}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rfc_sysnr" class="col-md-5 text-md-left spaced-label">{{__('RFC SysNr')}}</label>
                                                <div class="col-md-6">
                                                    <input id="ewm_rfc_sysnr" type="text" name="rfc_sysnr" class="spaced-input" required
                                                           value="{{$ewm->rfc_sysnr}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rfc_client" class="col-md-5 text-md-left spaced-label">{{__('RFC Client')}}</label>
                                                <div class="col-md-6">
                                                    <input id="ewm_rfc_client" type="text" name="rfc_client" class="spaced-input" required
                                                           value="{{$ewm->rfc_client}}">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="submit" class="background-image-save" value="    {{__('Save')}}" style="width: 10rem; height: 32px;">
                                    </form>
                                </div>
                            </div>
                            <div style="width: 18%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('Administrator')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/EWMroleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="rfc_user" class="col-md-5 text-md-left spaced-label">{{__('RFC User')}}</label>
                                            <div class="col-md-6">
                                                <input id="ewm_a_rfc_user" type="text" name="rfc_user" class="spaced-input" required
                                                       value="{{$adminEWMData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="rfc_passwd" class="col-md-5 text-md-left spaced-label">{{__('RFC Password')}}</label>
                                            <div class="col-md-6">
                                                <input id="ewm_a_rfc_passwd" type="password" name="rfc_passwd" class="spaced-input" required
                                                       value="{{$adminEWMData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-top: 0.8rem;">
                                            <button type="button" id="EWM_Administrator_test" class="background-image-pass-fail"
                                                    onclick="rfc_ewm_ping('ewm_a', '{{$oldewm->rfc_router}}','{{$oldewm->rfc_server}}','{{$oldewm->rfc_sysnr}}','{{$oldewm->rfc_client}}');return false;"
                                                    style="width: 45%; height: 1.5rem;">{{__('Test')}}
                                            </button>
                                            <input type="hidden" name="rfc_role" value="administrator">&nbsp;&nbsp;
                                            <input type="submit" value="{{__('Save')}}" class="background-image-save"
                                                   onclick="return check_rfc_ewm_data('{{$oldewm->rfc_router}}','{{$oldewm->rfc_server}}','{{$oldewm->rfc_sysnr}}','{{$oldewm->rfc_client}}');"
                                                   style="width: 45%; height: 1.5rem;">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div style="width: 18%;" class="role-card">
                                <div class="card-header">
                                    <b>{{__('Refferal')}}</b>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles/EWMroleUpdate">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="rfc_user" class="col-md-5 text-md-left spaced-label">{{__('RFC User')}}</label>
                                            <div class="col-md-6">
                                                <input id="ewm_r_rfc_user" type="text" name="rfc_user" class="spaced-input" required
                                                       value="{{$referentEWMData->rfc_user}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="rfc_passwd" class="col-md-5 text-md-left spaced-label">{{__('RFC Password')}}</label>
                                            <div class="col-md-6">
                                                <input id="ewm_r_rfc_passwd" type="password" name="rfc_passwd" class="spaced-input" required
                                                       value="{{$referentEWMData->rfc_passwd}}">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-top: 0.8rem;">
                                            <button type="button" id="EWM_Referent_test" class="background-image-pass-fail"
                                                    onclick="rfc_ewm_ping('ewm_r', '{{$oldewm->rfc_router}}','{{$oldewm->rfc_server}}','{{$oldewm->rfc_sysnr}}','{{$oldewm->rfc_client}}');return false;"
                                                    style="width: 45%; height: 1.5rem;">{{__('Test')}}
                                            </button>
                                            <input type="hidden" name="rfc_role" value="referent">&nbsp;&nbsp;
                                            <input type="submit" value="{{__('Save')}}" class="background-image-save"
                                                   onclick="return check_rfc_ewm_data('{{$oldewm->rfc_router}}','{{$oldewm->rfc_server}}','{{$oldewm->rfc_sysnr}}','{{$oldewm->rfc_client}}');"
                                                   style="width: 45%; height: 1.5rem;">
                                        </div>
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
        function check_rfc_global_data(old_rfc_router,
                                       old_rfc_server,
                                       old_rfc_sysnr,
                                       old_rfc_client
        ) {
            return ((old_rfc_router == $("#rfc_router").val()) &&
                    (old_rfc_server == $("#rfc_server").val()) &&
                    (old_rfc_sysnr == $("#rfc_sysnr").val()) &&
                    (old_rfc_client == $("#rfc_client").val())) &&
                    old_rfc_server != "" &&
                    old_rfc_sysnr != "" &&
                    old_rfc_client != ""
                ;
        }
        function check_rfc_ewm_data(old_rfc_router,
                                    old_rfc_server,
                                    old_rfc_sysnr,
                                    old_rfc_client
        ) {
            return ((old_rfc_router == $("#ewm_rfc_router").val()) &&
                    (old_rfc_server == $("#ewm_rfc_server").val()) &&
                    (old_rfc_sysnr == $("#ewm_rfc_sysnr").val()) &&
                    (old_rfc_client == $("#ewm_rfc_client").val())) &&
                    old_rfc_server != "" &&
                    old_rfc_sysnr != "" &&
                    old_rfc_client != ""
                ;
        }
        function rfc_ping(prefix,
                          old_rfc_router,
                          old_rfc_server,
                          old_rfc_sysnr,
                          old_rfc_client) {
            if (!check_rfc_global_data(old_rfc_router, old_rfc_server, old_rfc_sysnr, old_rfc_client)) {
                alert("Please fill/check/save RFC global settings first");
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajaxSetup({async: false});
            $.post("webservice/rfcping",
                {
                    rfc_router: $("#rfc_router").val(),
                    rfc_server: $("#rfc_server").val(),
                    rfc_sysnr: $("#rfc_sysnr").val(),
                    rfc_client: $("#rfc_client").val(),
                    rfc_user: $("#" + prefix + "_rfc_user").val(),
                    rfc_passwd: $("#" + prefix + "_rfc_passwd").val()
                },
                function(data, status){
                    // $("body").removeClass("ajaxloading");
                    alert("Data: " + data + "\nStatus: " + status);
                });
            $.ajaxSetup({async: true});
        }
        function rfc_ewm_ping(prefix,
                          old_rfc_router,
                          old_rfc_server,
                          old_rfc_sysnr,
                          old_rfc_client) {
            if (!check_rfc_ewm_data(old_rfc_router, old_rfc_server, old_rfc_sysnr, old_rfc_client)) {
                alert("Please fill/check/save RFC EWM settings first");
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajaxSetup({async: false});
            $.post("webservice/rfcping",
                {
                    rfc_router: $("#ewm_rfc_router").val(),
                    rfc_server: $("#ewm_rfc_server").val(),
                    rfc_sysnr: $("#ewm_rfc_sysnr").val(),
                    rfc_client: $("#ewm_rfc_client").val(),
                    rfc_user: $("#" + prefix + "_rfc_user").val(),
                    rfc_passwd: $("#" + prefix + "_rfc_passwd").val()
                },
                function(data, status){
                    // $("body").removeClass("ajaxloading");
                    alert("Data: " + data + "\nStatus: " + status);
                });
            $.ajaxSetup({async: true});
        }
    </script>
@endsection
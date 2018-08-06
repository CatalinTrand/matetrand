@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Super Admin'))
        @php
            header("/");
            exit();
        @endphp
    @endif
    @php
        use Illuminate\Support\Facades\DB;

        if(isset($_POST['role'])){
            $role = $_POST['role'];
            $router = $_POST['rfc_router'];
            $server = $_POST['rfc_server'];
            $sysnr = $_POST['rfc_sysnr'];
            $client = $_POST['rfc_client'];
            $user = $_POST['rfc_user'];
            $passwd = $_POST['rfc_passwd'];

            DB::delete("delete from roles where rfc_role = '$role'");
            DB::insert("insert into roles (rfc_role,rfc_router,rfc_server,rfc_sysnr,rfc_client,rfc_user,rfc_passwd) values ('$role','$router','$server','$sysnr','$client','$user','$passwd')");
        }

    @endphp
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <p class="card-line first selector">Roles</p>
                        <p class="card-line"><a href="/home">Users</a></p>
                        <p class="card-line"><a href="/messages">Messages</a></p>
                        <p class="card-line"><a href="/orders">Comenzi</a></p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="role-card">
                                <div class="card-header">
                                    Administrator
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="router"
                                                   class="col-md-4 col-form-label text-md-right">RFC Router</label>

                                            <div class="col-md-6">
                                                <input id="rfc_router" type="text" name="rfc_router" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="server"
                                                   class="col-md-4 col-form-label text-md-right">RFC Server</label>

                                            <div class="col-md-6">
                                                <input id="rfc_server" type="text" name="rfc_server" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_sysnr" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="rfc_client" type="text" name="rfc_client" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_user" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="rfc_passwd" type="text" name="rfc_passwd" required>
                                            </div>
                                        </div>
                                        <button id="Administrator_test" style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test</button>
                                        <input type="hidden" name="role" value="administrator">
                                        <input type="submit" value="Save" style="border-top: 4px black;width: 100%">
                                    </form>
                                </div>
                            </div>
                            <div class="role-card">
                                <div class="card-header">
                                    Furnizor
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="router"
                                                   class="col-md-4 col-form-label text-md-right">RFC Router</label>

                                            <div class="col-md-6">
                                                <input id="rfc_router" type="text" name="rfc_router" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="server"
                                                   class="col-md-4 col-form-label text-md-right">RFC Server</label>

                                            <div class="col-md-6">
                                                <input id="rfc_server" type="text" name="rfc_server" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_sysnr" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="rfc_client" type="text" name="rfc_client" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_user" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="rfc_passwd" type="text" name="rfc_passwd" required>
                                            </div>
                                        </div>
                                        <button id="Furnizor_test" style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test</button>
                                        <input type="hidden" name="role" value="furnizor">
                                        <input type="submit" value="Save" style="border-top: 4px black;width: 100%">
                                    </form>
                                </div>
                            </div>
                            <div class="role-card">
                                <div class="card-header">
                                    Referent
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="router"
                                                   class="col-md-4 col-form-label text-md-right">RFC Router</label>

                                            <div class="col-md-6">
                                                <input id="rfc_router" type="text" name="rfc_router" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="server"
                                                   class="col-md-4 col-form-label text-md-right">RFC Server</label>

                                            <div class="col-md-6">
                                                <input id="rfc_server" type="text" name="rfc_server" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_sysnr" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="rfc_client" type="text" name="rfc_client" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_user" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="rfc_passwd" type="text" name="rfc_passwd" required>
                                            </div>
                                        </div>
                                        <button id="Referent_test" style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test</button>
                                        <input type="hidden" name="role" value="referent">
                                        <input type="submit" value="Save" style="border-top: 4px black;width: 100%">
                                    </form>
                                </div>
                            </div>
                            <div class="role-card">
                                <div class="card-header">
                                    CTV
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/roles">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="router"
                                                   class="col-md-4 col-form-label text-md-right">RFC Router</label>

                                            <div class="col-md-6">
                                                <input id="rfc_router" type="text" name="rfc_router" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="server"
                                                   class="col-md-4 col-form-label text-md-right">RFC Server</label>

                                            <div class="col-md-6">
                                                <input id="rfc_server" type="text" name="rfc_server" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sysnr"
                                                   class="col-md-4 col-form-label text-md-right">RFC Sysnr</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_sysnr" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="client"
                                                   class="col-md-4 col-form-label text-md-right">RFC Client</label>

                                            <div class="col-md-6">
                                                <input id="rfc_client" type="text" name="rfc_client" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username"
                                                   class="col-md-4 col-form-label text-md-right">RFC User</label>

                                            <div class="col-md-6">
                                                <input id="rfc_user" type="text" name="rfc_user" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-md-right">RFC Password</label>

                                            <div class="col-md-6">
                                                <input id="rfc_passwd" type="text" name="rfc_passwd" required>
                                            </div>
                                        </div>
                                        <button id="CTV_test" style="border-top: 4px black;width: 100%;margin-bottom: 10px">Test</button>
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
@endsection
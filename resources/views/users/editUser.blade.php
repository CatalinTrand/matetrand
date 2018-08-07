@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Administrator' && (isset($_POST['id']) || isset($_GET['id']))))
        @php
            header("/");
            exit();
        @endphp
    @endif

    @php
        use Illuminate\Support\Facades\DB;

        $msg = "";
        $msg_wf = "";

        if(isset($_POST['id']))
            $id = $_POST['id'];
        else
            $id = $_GET['id'];

        //modify if needed
        if(isset($_POST['role'])){
            $role = $_POST['role'];
            $user = $_POST['username'];
            $email = $_POST['email'];

            DB::update("update users set role = '$role', username = '$user', email = '$email' where id = '$id'");

            if(isset($_POST['password'])){
                $passwd = $_POST['password'];
                $hash = \Illuminate\Support\Facades\Hash::make($passwd);

                DB::update("update users set password = '$hash' where id = '$id'");
            }

            $msg = "Changes saved!";
        }

        //load user data
        $users = DB::select("select * from users where id='$id'");

        if(!$users){
            echo "<h2>Error - no such user!</h2>";
            header("/");
            exit();
        }

        $user = $users[0];

        $selectedAdmin = "";
        $selectedReferent = "";
        $selectedFurnizor = "";
        $selectedCTV = "";

        switch ($user->role){
            case 'Administrator':
                $selectedAdmin = "selected";
            break;
            case 'Furnizor':
                $selectedFurnizor = "selected";
            break;
            case 'Referent':
                $selectedReferent = "selected";
            break;
            case 'CTV':
                $selectedCTV = "selected";
            break;
        }

        //add if needed
        if(isset($_POST['id_wf'])){
            $id_wf = $_POST['id_wf'];

            $result = DB::select("select * from users where id = '$id_wf'");

            if($result){
                $find = DB::select("select * from users_wf where id = '$id' and follow_up_id = '$id_wf'");

                if(count($find) == 0){
                    DB::insert("insert into users_wf (id,follow_up_id) values ('$id','$id_wf')");
                    $msg_wf = "Follow-up set!";
                } else $msg_wf = "Entry already exists!";
            } else $msg_wf = "No such user or user with specified ID is not a verified follow-up!";
        }

        //delete if needed
        if(isset($_GET['delWF'])){
            $delWF = $_GET['delWF'];
            DB::delete("delete from users_wf where id = '$id' and follow_up_id = '$delWF'");
            $msg_wf = "Follow-up deleted!";
        }

    @endphp
    <div class="row container-fluid">
        <div class="container" style="width: 40%;margin-right: -30vw">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><a style="padding-right: 20px" href="/users">&larr; Back</a>Edit User
                            Panel
                        </div>

                        <div class="card-body">
                            <form method="POST" action="/editUser" aria-label="Edit User">
                                @csrf
                                <font color='green'>{{$msg}}</font>
                                <div class="form-group row">
                                    <label for="role"
                                           class="col-md-4 col-form-label text-md-right">{{ __('User Type') }}</label>

                                    <div class="col-md-6">
                                        <select id="role" type="text" class="form-control" name="role" required
                                                autofocus>
                                            <option {{$selectedAdmin}}>Administrator</option>
                                            <option {{$selectedFurnizor}}>Furnizor</option>
                                            <option {{$selectedReferent}}>Referent</option>
                                            <option {{$selectedCTV}}>CTV</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

                                    <div class="col-md-6">
                                        <input id="username" type="text" name="username" class="form-control" required
                                               value="{{$user->username}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                               class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                               name="email" value="{{$user->email}}" required>

                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password"
                                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                               name="password">

                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="{{$id}}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            Edit Workflow Follow-up
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/editUser" aria-label="Edit Workflow Follow-up">
                                @csrf
                                <font color='green'>{{$msg_wf}}</font>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-4 col-form-label text-md-right">New Follow-up ID</label>

                                    <div class="col-md-6">
                                        <input id="id_wf" type="text" name="id_wf" class="form-control" required
                                               value="">
                                    </div>
                                </div>

                                <input type="hidden" name="id" value="{{$id}}">

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <br>
                            <table class="basicTable table table-striped">
                                <tr>
                                    <th>
                                        Follow-up ID
                                    </th>
                                    <th>
                                        Username
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                                @php
                                    $myIDs = DB::select("select * from users_wf where id='$id'");
                                    $table = "";
                                    foreach ($myIDs as $anID){
                                        $aUser = App\User::all()->find($anID->follow_up_id);
                                        if($aUser)
                                            $table .= "<tr style='line-height: 35px'><td>$anID->follow_up_id</td><td>$aUser->username</td><td><a href='/editUser?delWF=$anID->follow_up_id&id=$id'><img src='images/delete.png' class='delete'></a></td></tr>";
                                    }
                                    echo $table;
                                @endphp
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
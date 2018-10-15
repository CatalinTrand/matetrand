@extends('layouts.app')

@section('content')
    @guest
        @php
            header("/");
            exit();
        @endphp
    @endguest
    @php
        if(isset($_GET['del'])){
            $id_del = $_GET['del'];
            DB::delete("delete from messages where id = '$id_del'");
        }
    @endphp
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
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-user-account-80.png'/>
                                    {{__("Users")}}
                                </p>
                            </a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                {{__("Messages")}}
                            </p>
                            <a href="/orders">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p>
                            </a>
                        @else
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line first selector">Messages</p>
                            <a href="/orders"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">Comenzi</p></a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <br><br>
                        <table id="messages_table" class="orders-table basicTable table table-striped">
                            <tr>
                                <th>EBELN</th>
                                <th>EBELP</th>
                                <th>Sales Order</th>
                                <th>CDATE</th>
                                <th>CUSER & CNAME</th>
                                <th></th>
                                <th></th>
                                <th>Text mesaj</th>
                            </tr>
                            @php
                            $messages = App\Materom\Orders::getMessageList();
                            foreach ($messages as $message){

                                $tablerow = "<tr><td>$message->ebeln</td>
                                                 <td>$message->ebelp</td>
                                                 <td>$message->vbeln</td>
                                                 <td>$message->cdate</td>
                                                 <td>$message->cuser ( $message->cuser_name )</td>
                                                 <td><button>V</button></td>
                                                 <td><button><-</button></td>
                                                 <td>$message->text</td></tr>";

                                echo $tablerow;
                            }
                            @endphp
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
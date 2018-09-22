<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
})->name('base');

Route::post('/language',array(
        'Middleware'=>'LanguageSwitcher',
        'uses'=>'LanguageController@index'
));

Route::post('/users/{lang}', 'HomeController@setDefault($lang)');

Auth::routes();

Route::get('/users', 'HomeController@index')->name('users');

Route::get('/editUser', 'HomeController@editUser')->name('editUser');
Route::post('/editUser', 'HomeController@save_edit')->name('save_edit');
Route::post('/editUser/edit','EditUserController@editUsers');

Route::post('webservice/rfcping','WebserviceController@rfcPing');
Route::post('webservice/insertvendoruser','WebserviceController@insertVendorID');
Route::post('webservice/changepassword','WebserviceController@changePassword');
Route::post('webservice/getOrderInfo','WebserviceController@getOrderInfo');

Route::get('webservice/get_vendor_users','WebserviceController@getVendorUsers');
Route::get('webservice/sap_activate_user','WebserviceController@sapActivateUser');
Route::get('webservice/sap_deactivate_user','WebserviceController@sapDeactivateUser');
Route::get('webservice/sap_create_user','WebserviceController@sapCreateUser');
Route::get('webservice/sap_delete_user','WebserviceController@sapDeleteUser');
Route::get('webservice/sap_reset_password','WebserviceController@changePassword');

Route::post('/roles/globalUpdate','RolesController@insertGlobalData');
Route::post('/roles/roleUpdate','RolesController@insertRoleData');

Route::get('/roles', 'HomeController@roles')->name('roles');
Route::post('/roles', 'HomeController@save_roles')->name('save_roles');

Route::get('/orders', 'HomeController@orders')->name('orders');

Route::group(['prefix' => 'messages'], function () {
    Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
    Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
    Route::get('viewMsg', ['as' => 'messages.viewMsg', 'uses' => 'MessagesController@viewMsg']);
    Route::get('sentMessages', ['as' => 'messages.sentMessages', 'uses' => 'MessagesController@sentMessages']);
    Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
    Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
    Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
});

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
Route::post('/editUser/selDel','EditUserController@selDel');
Route::post('/editUser/refDel','EditUserController@refDel');
Route::post('/editUser/agentDel','EditUserController@agentDel');
Route::post('/editUser/kunnrDel','EditUserController@kunnrDel');

// Webservices
Route::post('webservice/rfcping','WebserviceController@rfcPing');
Route::post('webservice/insertmanufacturer','WebserviceController@insertManufacturer');
Route::post('webservice/insertreferenceuser','WebserviceController@insertReferenceUser');
Route::post('webservice/insertagent','WebserviceController@insertAgent');
Route::post('webservice/insertcustomer','WebserviceController@insertCustomer');
Route::post('webservice/changepassword','WebserviceController@changePassword');
Route::post('webservice/impersonate_as_user','WebserviceController@impersonateAsUser');
Route::post('webservice/acceptitemchange','WebserviceController@acceptItemChange');
Route::post('webservice/cancelItem','WebserviceController@cancelItem');
Route::post('webservice/dochangeitem','WebserviceController@doChangeItem');
Route::post('webservice/sendAck','WebserviceController@sendAck');
Route::post('webservice/replymessage','WebserviceController@replyMessage');
Route::get('webservice/getsubtree','WebserviceController@getSubTree');
Route::post('webservice/sortmessages','WebserviceController@sortMessages');
Route::get('webservice/itemsOfOrder','WebserviceController@itemsOfOrder');
Route::post('webservice/refilter','WebserviceController@refilter');
Route::post('webservice/deletefilters','WebserviceController@deletefilters');
Route::post('webservice/readproposals','WebserviceController@readProposals');
Route::get('webservice/readpitem','WebserviceController@readPOItem');
Route::get('webservice/processproposal','WebserviceController@processProposal');
Route::get('webservice/processproposal2','WebserviceController@processProposal2');
Route::get('webservice/acceptproposal','WebserviceController@acceptProposal');
Route::get('webservice/rejectproposal','WebserviceController@rejectProposal');
Route::get('webservice/readlifnrname','WebserviceController@readLifnrName');
Route::post('webservice/sendinquiry','WebserviceController@sendInquiry');
Route::get('webservice/downloadordersxls','WebserviceController@downloadOrdersXLS');
Route::get('webservice/processsplit','WebserviceController@processSplit');
Route::get('webservice/acceptsplit','WebserviceController@acceptSplit');
Route::get('webservice/rejectsplit','WebserviceController@rejectSplit');
// Statistics
Route::get('webservice/get_stat_data','WebserviceController@getStatData');
Route::get('webservice/get_stat_ekgrp_of_lifnr','WebserviceController@getStatEkgrpOfLifnr');

// SAP webservices
Route::get('webservice/get_vendor_users','WebserviceController@getVendorUsers');
Route::get('webservice/get_ctv_users','WebserviceController@getCTVUsers');
Route::get('webservice/sap_activate_user','WebserviceController@sapActivateUser');
Route::get('webservice/sap_deactivate_user','WebserviceController@sapDeactivateUser');
Route::get('webservice/sap_create_user','WebserviceController@sapCreateUser');
Route::get('webservice/sap_delete_user','WebserviceController@sapDeleteUser');
Route::get('webservice/sap_reset_password','WebserviceController@changePassword');
Route::get('webservice/sap_process_po','WebserviceController@sapProcessPO');
Route::get('webservice/read_inforecords','WebserviceController@readInforecords');
Route::get('webservice/read_zpretrecords','WebserviceController@readZPRETrecords');
Route::get('webservice/get_sales_margin','WebserviceController@getSalesMargin');
Route::get('webservice/get_sales_price','WebserviceController@getSalesPrice');
Route::get('webservice/get_message_history','WebserviceController@getMessageHistory');
Route::get('webservice/get_fx_rate','WebserviceController@getFXRate');
Route::get('webservice/archive_item','WebserviceController@archiveItem');
Route::get('webservice/unarchive_item','WebserviceController@unarchiveItem');
Route::get('webservice/rollback_item','WebserviceController@rollbackItem');

// Global data
Route::post('/roles/globalUpdate','RolesController@insertGlobalData');
Route::post('/roles/roleUpdate','RolesController@insertRoleData');

// Roles
Route::get('/roles', 'HomeController@roles')->name('roles');
Route::post('/roles', 'HomeController@save_roles')->name('save_roles');

// Orders
Route::get('/orders', 'HomeController@orders_get')->name('orders');
Route::post('/orders', 'HomeController@orders_post');

// Messages
Route::get('/messages', 'HomeController@messages_get')->name('messages');
Route::post('/messages', 'HomeController@messages_post');

// Statistics
Route::get('/stats', 'HomeController@stats_get')->name('stats');

// Debug
Route::post('webservice/debug_job','WebserviceController@debug');

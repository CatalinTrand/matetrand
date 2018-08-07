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
    return view('welcome');
});

Route::post('/language',array(
        'Middleware'=>'LanguageSwitcher',
        'uses'=>'LanguageController@index'
));

Auth::routes();

Route::get('/users', 'HomeController@index')->name('users');

Route::get('/editUser', 'HomeController@editUser')->name('editUser');
Route::post('/editUser', 'HomeController@save_edit')->name('save_edit');

Route::post('webservice/rfcping','WebserviceController@rfcPing');

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

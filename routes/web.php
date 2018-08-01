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

Route::get('/', 'LoginController@index');
Route::post('/check','AuthController@check');

Route::get('/test','testController@index');

Route::group(['middleware'=>['AuthCheck']],function(){
  Route::get('/mypage','LoginController@login');
  Route::get('/logout','AuthController@logout');
  Route::post('/dakoku','KintaiController@dakoku');
  Route::get('/archives','KintaiController@getArchives');
  Route::get('/archivesUpdate','KintaiController@ArchivesUpdate');
  Route::get('/archives/{ym}','KintaiController@getArchivesList');
  //Route::get('/archives/{ym}/{d}','KintaiController@getArchivesDtails');
  Route::get('/edit','KintaiController@kintaiEdit');
  Route::post('/search','KintaiController@search');
  Route::get('/search/{ymid}','KintaiController@searchKintai');
  Route::post('/editkintai','KintaiController@editKintai');
  Route::post('/registkintai','KintaiController@registKintai');

  Route::get('/usermastar', 'UserController@getUser');
  Route::post('/registuser', 'UserController@registUser');
  Route::post('/edituser', 'UserController@editUser');
  Route::get('/deluser/{id}', 'UserController@deleteUser');

  Route::post('/changepass', 'UserController@changePass');
  Route::get('/help',function(){return view('help');});
  Route::post('/checkFA', 'FAController@checkFA');
  Route::post('/registFA', 'FAController@registFA');


});

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
//Route untuk home dan about
Route::get('/', 'HomepageController@index');

Route::get('about', 'AboutController@index');

//latihan Route halaman rahasia
Route::get('halaman-rahasia', [
	'as' => 'secret',
	'uses' => 'RahasiaController@halamanRahasia'
]);

Route::get('showmesecret', 'RahasiaController@showMeSecret');

//Route yang bisa diganti jadi route resource
/*Route::get('siswa', 'SiswaController@index');

Route::get('siswa/create', 'SiswaController@create');

Route::get('siswa/{siswa}', 'SiswaController@show');

Route::post('siswa', 'SiswaController@store');

Route::get('siswa/{siswa}/edit', 'SiswaController@edit');

Route::patch('siswa/{siswa}', 'SiswaController@update');

Route::delete('siswa/{siswa}', 'SiswaController@destroy');*/

//Route otomatis auth
Auth::routes(['register' => false]);

Route::get('siswa/cari', 'SiswaController@cari');
//Route resource pengganti semua route untuk SiswaController
Route::resource('siswa', 'SiswaController');
//Route resource Kelas
Route::resource('kelas', 'KelasController')->parameters(['kelas' => 'kelas']);
//Route resource Hobi
Route::resource('hobi', 'HobiController');
//Route resource user
Route::resource('user', 'UserController');

//Route tes collection dan dateMutator
Route::get('tes-collection', 'SiswaController@tesCollection');

Route::get('date-mutator', 'SiswaController@dateMutator');


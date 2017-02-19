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
/*
 *
Route::get('/', function () {
    return view('welcome');
});
 */

Auth::routes();

Route::get('/vue', function () {
    return view('vue');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/phpinfo', function () {
    return phpinfo();
});

Route::get('/', function () {
    return redirect('/main');
});
/*
 * 로그인 처리
 */
Route::post('/ldap', 'LdapController@ldap');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index');

    Route::get('/main', function () {
        return view('main');
    })->name('main');

    Route::get('/ucc/ucc', 'UccController@ucc');

    Route::get('/ucc/thumbnail', 'UccController@thumbnail');

    Route::get('/ucc/status', 'UccController@status');

    Route::patch('/ucc/marking', 'UccController@marking');
});

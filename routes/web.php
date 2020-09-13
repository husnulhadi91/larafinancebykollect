<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/customer', 'HomeController@customer')->name('customer');
Route::get('/invoice_cust/{id}', 'HomeController@invoice_cust')->name('invoice_cust');
Route::post('/apply_finance', 'HomeController@apply_finance')->name('apply_finance');
Route::post('/pay_finance', 'HomeController@pay_finance')->name('pay_finance');
Route::get('/admin', 'HomeController@admin')->name('admin');
Route::get('/invoice_admin/{id}', 'HomeController@invoice_admin')->name('invoice_admin');
Route::post('/new_user', 'HomeController@new_user')->name('new_user');


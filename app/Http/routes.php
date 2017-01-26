<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', function () {
    return view('login');
});
Route::get('/login', 'View\MemberController@toLogin');
Route::get('/register', 'View\MemberController@toRegister');
Route::get('/category','View\BookController@toCategory');
Route::get('/product/category_id/{category_id}','View\BookController@toProduct');
Route::get('/product/{product_id}','View\BookController@toPdtContent');



Route::group(['prefix' => 'service'], function() {
    Route::get('validate_code/create', 'Service\ValidateController@create');
    Route::post('validate_phone/send', 'Service\ValidateController@sendSMS');
    Route::get('validate_email', 'Service\ValidateController@validateEmail');
    Route::post('register', 'Service\MemberController@register');
    Route::post('login', 'Service\MemberController@login');
    Route::get('category/parent_id/{parent_id}', 'Service\BookController@getCategoryByParentId');
    Route::get('cart/add/{product_id}', 'Service\CartController@addCart');
    Route::get('cart/delete', 'Service\CartController@deleteCart');
});




/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

Route::group(['middleware' => ['check.login']], function () {
    // your routes here
    Route::get('/cart','View\CartController@toCart');
});


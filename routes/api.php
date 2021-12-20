<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'user.'], function () {
    Route::post('/login', 'AuthenticateController@login')->name('login');
    Route::post('/register', 'UserController@create')->name('create');
    Route::post('/verify', 'UserController@verify')->name('verify');
    Route::post('/forgetpassword', 'UserController@forgetPassword')->name('forgetPassword');

    Route::group(['prefix' => 'category', 'as' => 'user.'], function () {
        Route::get('/', 'CategoryController@index')->name('index');
        Route::get('/{id}/product', 'CategoryController@getProductsForCategory')->name('getProductsForCategory');
    });

    Route::group(['prefix' => 'product', 'as' => 'user.'], function () {
        Route::get('/', 'ProductController@index')->name('index');
        Route::get('/{id}', 'ProductController@getDetailProduct')->name('getDetailProduct');
        Route::put('/{id}', 'ProductController@updateView')->name('updateView');
        Route::get('/user/search', 'ProductController@search')->name('search');
    });

    Route::group(['prefix' => 'comment', 'as' => 'user.'], function () {
        Route::get('/{id}', 'CommentController@index')->name('index');
        Route::get('/star/{id}', 'CommentController@getAccordingToStar')->name('getAccordingToStar');
    });

    Route::group(['prefix' => 'transaction', 'as' => 'user.'], function () {
        Route::post('/', 'TransactionController@create')->name('create');
        Route::get('/payment-return', 'TransactionController@returnPayment')->name('returnPayment');
    });

    Route::group(['prefix' => 'order', 'as' => 'user.'], function () {
        Route::get('/{id}', 'OrderController@index')->name('index');
    });

    Route::group(['middleware' => 'api-user'], function () {
        Route::get('/logout', 'AuthenticateController@logout')->name('logout');
        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('/', 'UserController@index')->name('index');
            Route::put('/', 'UserController@update')->name('update');
        });
        Route::group(['prefix' => 'cart', 'as' => 'user.'], function () {
            Route::get('/', 'CartController@index')->name('index');
            Route::post('/', 'CartController@createOrUpdate')->name('createOrUpdate');
            Route::delete('/{id}', 'CartController@delete')->name('delete');
        });

        Route::group(['prefix' => 'comment', 'as' => 'user.'], function () {
            Route::post('/', 'CommentController@create')->name('create');
        });

        Route::group(['prefix' => 'transaction', 'as' => 'user.'], function () {
            Route::get('/', 'TransactionController@index')->name('index');
            Route::put('/{id}', 'TransactionController@update')->name('update');
        });
    });
});

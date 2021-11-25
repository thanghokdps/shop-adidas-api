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

Route::group(['as' => 'admin.'], function () {
    Route::post('/login', 'AuthenticateController@login')->name('login');
    Route::group(['middleware' => 'api-admin'], function () {
        Route::get('/logout', 'AuthenticateController@logout')->name('logout');

        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('/', 'UserController@index')->name('index');
            Route::post('/', 'UserController@create')->name('create');
            Route::put('/{id}', 'UserController@update')->name('update');
            Route::get('/deleted-users', 'UserController@getDeletedUsers')->name('deleted-users');
            Route::delete('/', 'UserController@deleteUsers')->name('delete-users');
            Route::post('/restore-users', 'UserController@updateDeletedUsers')->name('restore-users');
        });

        Route::group(['prefix' => 'admin', 'as' => 'user.'], function () {
            Route::get('/', 'AdminController@index')->name('index');
        });

        Route::group(['prefix' => 'category', 'as' => 'user.'], function () {
            Route::get('/', 'CategoryController@index')->name('index');
            Route::post('/', 'CategoryController@create')->name('create');
            Route::put('/{id}', 'CategoryController@update')->name('update');
            Route::get('/deleted-categories', 'CategoryController@getDeletedCategories')->name('deleted-categories');
            Route::delete('/', 'CategoryController@deleteCategories')->name('delete');
            Route::post('/restore-categories', 'CategoryController@updateDeletedCategories')->name('restore-categories');
        });

        Route::group(['prefix' => 'product', 'as' => 'user.'], function () {
            Route::get('/', 'ProductController@index')->name('index');
            Route::post('/', 'ProductController@create')->name('create');
            Route::post('/{id}', 'ProductController@update')->name('update');
            Route::get('/deleted-products', 'ProductController@getDeletedProducts')->name('deleted-products');
            Route::delete('/', 'ProductController@deleteProducts')->name('delete');
            Route::post('/restore-products', 'ProductController@updateDeletedProducts')->name('restore-products');
            Route::get('/search-products', 'ProductController@search')->name('search');
        });

        Route::group(['prefix' => 'detail-product', 'as' => 'user.'], function () {
            Route::get('/', 'DetailProductController@index')->name('index');
            Route::get('/{id}', 'DetailProductController@getDetail')->name('getDetail');
            Route::post('/', 'DetailProductController@createOrUpdate')->name('createOrUpdate');
            Route::delete('/', 'DetailProductController@deleteDetailProducts')->name('delete');
        });

        Route::group(['prefix' => 'transaction', 'as' => 'user.'], function () {
            Route::get('/', 'TransactionController@index')->name('index');
            Route::get('/{id}', 'TransactionController@getDetail')->name('getDetail');
            Route::put('/{id}', 'TransactionController@update')->name('update');
        });

        Route::group(['prefix' => 'order', 'as' => 'user.'], function () {
            Route::get('/', 'OrderController@index')->name('index');
            Route::get('/{id}', 'OrderController@getDetail')->name('getDetail');
            Route::get('/admin/budget-product', 'OrderController@budgetProduct')->name('budgetProduct');
            Route::get('/admin/budget-product-detail', 'OrderController@budgetProductDetail')->name('budgetProductDetail');
            Route::get('/admin/budget-date', 'OrderController@budgetDate')->name('budgetDate');
        });
    });
});

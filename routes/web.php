<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('products', 'ProductsController@index')->name('products.index');

<?php

Route::get('/', 'TestController@indexAction');
Route::post('add', 'TestController@addAction');
Route::get('list/{id}', 'TestController@listAction');

Route::post('upload',      ['as' => '.upload', 'uses'  => 'FilepondController@upload']);
Route::delete('revert',    ['as' => '.revert', 'uses'  => 'FilepondController@revert']);
Route::get('load/{id}',    ['as' => '.load', 'uses'    => 'FilepondController@load']);
Route::get('fetch',        ['as' => '.fetch', 'uses'   => 'FilepondController@fetch']);
Route::get('restore/{id}', ['as' => '.restore', 'uses' => 'FilepondController@restore']);


<?php

Route::get('/',         'TestController@indexAction');
Route::post('add',      'TestController@addAction');
Route::get('list/{id}', 'TestController@listAction');
Route::get('edit/{id}', 'TestController@editAction');
Route::post('update',   'TestController@updateAction');

Route::post('transfer', ['as' => '.transfer', 'uses' => 'FilepondController@transfer']);
Route::delete('revert', ['as' => '.revert', 'uses'   => 'FilepondController@revert']);
Route::get('load/{id}', ['as' => '.load', 'uses'     => 'FilepondController@load']);

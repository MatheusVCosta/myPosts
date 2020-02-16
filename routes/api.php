<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->namespace('Api')->group(function(){
    
    Route::prefix('users')->group(function(){
        Route::get('/', 'ControllerUser@index');
        Route::get('/{id}', 'ControllerUser@search');
        Route::post('/', 'ControllerUser@create');
        Route::delete('/{id}', 'ControllerUser@delete');
        Route::put('/{id}', 'ControllerUser@update');
        Route::patch('/{id}', 'ControllerUser@update');

    });
    Route::prefix('posts')->group(function(){
        Route::get('/', 'ControllerPost@index');
        Route::get('/{id}', 'ControllerPost@search');
        Route::post('/', 'ControllerPost@create');
        Route::delete('/{id}', 'ControllerPost@delete');
        Route::put('/{id}', 'ControllerPost@update');
        Route::patch('/{id}', 'ControllerPost@update');
    });

    Route::prefix('group')->group(function(){

        Route::get('/', 'ControllerGroup@index');
        Route::get('/search_group', 'ControllerGroup@searchBy');

        Route::post('/addNewMember', 'ControllerGroup@addNewMember');
        Route::post('/removeMember/{id_group}/{id_user}', 'ControllerGroup@removeMember');
        Route::post('/', 'ControllerGroup@create');
        
        Route::delete('/{id}', 'ControllerGroup@delete');
        Route::put('/{id}', 'ControllerGroup@update');
        Route::patch('/{id}', 'ControllerGroup@update');
    });
});

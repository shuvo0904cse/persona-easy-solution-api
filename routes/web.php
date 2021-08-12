<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->get('/', function () {
    return 'Hello World';
});

$router->group(['prefix' => 'api'], function () use ($router){

    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');

    //$router->group(['middleware' => 'auth'], function () use ($router){
        //Category
        $router->get('categories', 'CategoryController@category');
        $router->post('store-category', 'CategoryController@store');
        $router->put('update-category/{id}', 'CategoryController@update');
        $router->delete('delete-category/{id}', 'CategoryController@delete');

        //Money
        $router->get('/money', 'MoneyController@index');
        $router->post('/store-money', 'MoneyController@store');
        $router->put('/update-money/{id}', 'MoneyController@update');
        $router->delete('/delete-money/{id}', 'MoneyController@delete');
    // /});
});

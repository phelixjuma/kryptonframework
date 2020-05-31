<?php

/**
 * @file
 *
 * We handle all system routes here
 */

use Pecee\SimpleRouter\SimpleRouter;


// ================== SECTION FOR VIEW URL DEFINITIONS ===============================


//
SimpleRouter::group(['prefix' => '/auth'], function () {

    SimpleRouter::get('login', 'AuthView@login');
    SimpleRouter::get('signup', 'AuthView@signup');

});

// front functions.
SimpleRouter::group(['prefix' => '/'], function () {

    SimpleRouter::get('home', 'HomeView@index');

});

// admin functions.
SimpleRouter::group(['prefix' => '/admin', 'middleware' => \Kuza\Krypton\Framework\Middlewares\AuthenticationMiddleware::class], function () {

    SimpleRouter::get('/users', 'UsersView@getUsers');

});


 // ================= SECTION FOR API ENDPOINT DEFINITIONS ===========================
SimpleRouter::group(['prefix' => '/api'], function () {


    // 1. loign
    SimpleRouter::post('login', 'AuthApi@login');
    //2. signup
    SimpleRouter::post('signup', 'AuthApi@signup');

    // These APIs require authentication
    SimpleRouter::group(['middleware' => \Kuza\Krypton\Framework\Middlewares\APIAuthenticationMiddleware::class], function () {

        //1. Users APIs
        SimpleRouter::group(['prefix' => 'users'], function(){

            // a. list of users
            SimpleRouter::get('/', 'UsersApi@allUsers');
            //b. specific user
            SimpleRouter::get('/{id}', 'UsersApi@oneUser');
            // c. user roles
            SimpleRouter::get('/{id}/roles', 'UsersApi@userRoles');

        });
    });

});
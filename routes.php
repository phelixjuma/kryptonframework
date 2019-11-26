<?php

/**
 * @file
 *
 * We handle all system routes here
 */


$this->router->filter('auth', function(){
    if(isset($_SESSION['user']))
    {
        header('Location: /login');

        return false;
    }
});


/**
 * Views on the admin side
 */
$this->router->group(['prefix' => 'admin', "before" => "auth"], function() {

    /**
     * User Management
     */
    $this->router->controller("users", "Kuza\Krypton\Framework\Controllers\UsersView");

});

/**
 * APIs
 */
$this->router->group(['prefix' => 'api'], function() {

    /**
     * Handle all user APIs
     */
    $this->router->controller("users", "Kuza\Krypton\Framework\Controllers\UsersApi");

});
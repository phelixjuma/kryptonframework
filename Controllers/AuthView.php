<?php

/**
 * This is the Auth View
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Framework\Controller;

class AuthView extends Controller {

    /**
     * AuthView constructor.
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Login
     *
     * @Route("/login")
     */
    public function login() {

        $users = ['name'=>'phelix'];
        $test = "this is a test";

        $this->view("login", compact('users', 'test'));
    }

    /**
     * Signup
     *
     * @Route("/signup")
     */
    public function signup() {
        $this->view("signup");
    }
}
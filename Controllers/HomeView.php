<?php

/**
 * This is the Home Controller Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2020, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Controllers;


use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Framework\Controller;
use Kuza\Krypton\Framework\Models\UserModel;

class HomeView extends Controller {

    /**
     * HomeView constructor.
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Home Page
     *
     * @Route("/home")
     *
     * @throws CustomException
     */
    public function index() {

        $validation_errors = $this->validate($this->requests->body, UserModel::$validation_rules);

        $title = "Welcome to Krypton Framework";
        $description = "A minimalistic PHP Framework that let's you decide on how to write your code with just a basic structure in place";

        $this->view("home", compact('title', 'description'), compact('validation_errors'));
    }
}
<?php

/**
 * This is the Users Controller Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Classes\Utils;
use Kuza\Krypton\Framework\Framework\Controller;
use Kuza\Krypton\Framework\Models\SessionAuthentication;
use Kuza\Krypton\Framework\Models\User;

class AuthApi extends Controller {

    protected $auth;
    protected $user;

    /**
     * AuthApi constructor.
     * @param SessionAuthentication $auth
     * @param User $user
     */
    public function __construct(SessionAuthentication $auth, User $user) {
        parent::__construct();

        $this->auth = $auth;
        $this->user = $user;
    }

    /**
     * Handle Login
     *
     * @Route('/api/auth/login')
     *
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function postLogin() {

        $body = $this->requests->body;

        $this->validateInputRequired($body, ['email_address', 'password']);

        if ($this->validation_errors) {
            $this->apiResponse(Requests::RESPONSE_OK, false,"Please enter all required fields", null,0);
        }
        // we log in the user
        else {

            $response = $this->auth->login($body['email_address'], $body['password']);

            $this->apiResponse(Requests::RESPONSE_OK, $response['success'], $response['message'], $response['data']);
        }
    }

    /**
     * Handle Sign up
     *
     * @Route('/api/auth/signup')
     *
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function postSignup() {

        $body = $this->requests->body;

        $this->validateInputRequired($body, ['email_address', 'password', 'first_name', 'surname', 'gender']);

        if ($this->validation_errors) {
            $this->apiResponse(Requests::RESPONSE_BAD_REQUEST, false,"Please enter all required fields", null,0);
        }

        // we sign up the user
        else {

            $body['password'] = Utils::hashPassword($body['password']);
            $body['role_id'] = 2; // we set the 'user' type role.

            $user = $this->user->createUser($body);

            $this->apiResponse(Requests::RESPONSE_OK, true, "Your account has been successfully created", $user);
        }
    }
}
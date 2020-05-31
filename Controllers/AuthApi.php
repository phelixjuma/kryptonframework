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
use Kuza\Krypton\Framework\Controller;
use Kuza\Krypton\Framework\Models\UserModel;
use Kuza\Krypton\Framework\Repository\SessionAuthentication;
use Kuza\Krypton\Framework\Repository\UserRepository;

class AuthApi extends Controller {

    protected $auth;
    protected $userRepository;

    /**
     * AuthApi constructor.
     * @param SessionAuthentication $auth
     * @param UserRepository $userRepository
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     */
    public function __construct(SessionAuthentication $auth, UserRepository $userRepository) {
        parent::__construct();

        $this->auth = $auth;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle Login
     *
     * @Route('/api/auth/login')
     *
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function login() {

        $body = $this->requests->body;

        $this->validate($body, UserModel::$login_validation_rules);

        if ($this->validation_errors) {
            $this->apiResponse(Requests::RESPONSE_BAD_REQUEST, false,"Please enter all required fields", null,$this->errors);
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
    public function signup() {

        $body = $this->requests->body;

        $this->validate($body, UserModel::$validation_rules);

        if ($this->validation_errors) {
            $this->apiResponse(Requests::RESPONSE_BAD_REQUEST, false,"Please enter all required fields", null,$this->errors);
        }

        // we sign up the user
        else {

            $body['password'] = Utils::hashPassword($body['password']);
            $body['role_id'] = 2; // we set the 'user' type role.

            $user = $this->userRepository->createUser($body);

            $this->apiResponse(Requests::RESPONSE_OK, true, "Your account has been successfully created", $user);
        }
    }
}
<?php

/**
 * This is the Session Authentication Model Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Repository;

use Kuza\Krypton\Exceptions\ConfigurationException;
use Kuza\Krypton\Exceptions\CustomException;


class SessionAuthentication {

    protected $userRepository;

    /**
     * SessionAuthentication constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * Log in a user.
     *
     * @param $email
     * @param $password
     * @return array
     * @throws ConfigurationException
     * @throws CustomException
     */
    public function login($email, $password) {

        $data = ["success" => false, "message" => "", "data" => null];

        // 1.  check if the user exists
        $this->userRepository->setUserByEmailAddress($email);

        if (!$this->userRepository->is_user) {
            $data['message'] = "The email address is not registered with us";
        }
        // 2. check if the password is correct
        elseif(!$this->userRepository->validatePassword($password)) {
            $data['message'] = "The password entered for this email address does not match our records";
        }
        // 3. everything ok. We log in the user
        else {
            $_SESSION['current_user'] = $this->userRepository->getUserDetails();

            $data['success'] = true;
            $data['message'] = "You have been successfully logged in.";
            $data['data'] = $this->userRepository->getUserDetails();
        }

        return $data;
    }

    /**
     * Log out
     */
    public function logout() {
        session_destroy();
        $_SESSION = null;
    }

    /**
     * Get authenticated user
     * @return UserRepository
     * @throws CustomException
     */
    public function authenticate() {
        return $this->userRepository->setUserById(2);
    }

}
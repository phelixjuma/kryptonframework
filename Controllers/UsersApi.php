<?php

/**
 * This is the Users Controller Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Controller;
use Kuza\Krypton\Framework\Models\UserModel;
use Kuza\Krypton\Framework\Repository\UserRepository;

class UsersApi extends Controller {

    protected $userRepository;

    /**
     * UsersApi constructor.
     * @param UserModel $user
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     */
    public function __construct(UserRepository $userRepository) {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * Get all users
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function allUsers() {

        $usersList = $this->userRepository->getUsers();
        $count = $this->userRepository->countUsers();

        $this->apiResponse(Requests::RESPONSE_OK, true, "", $usersList,[], $count);
    }

    /**
     * @param $userId
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function oneUser($userId) {

        $this->userRepository->setUserById($userId);

        $this->apiResponse(Requests::RESPONSE_OK, true, "", $this->userRepository->getUserDetails());
    }

    /**
     * Handle creation of a user
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function userRoles($userId) {

        $this->userRepository->setUserById($userId);

        $this->apiResponse(Requests::RESPONSE_OK, true, "", $this->userRepository->getUserDetails());
    }
}
<?php

/**
 * This is the Users Controller Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Framework\Controller;
use Kuza\Krypton\Framework\Repository\UserRepository;


class UsersView extends Controller {

    protected $userRepository;

    /**
     * UsersView constructor.
     * @param UserRepository $userRepository
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     */
    public function __construct(UserRepository $userRepository) {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * View the list of users.
     *
     * @Route("/admin/users")
     *
     * @throws \Kuza\Krypton\Exceptions\ConfigurationException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function getUsers() {

        # print_r($this->currentUser->getUserDetails());

        $usersList = $this->userRepository->getUsers();
        $count = $this->userRepository->countUsers();

        $this->view("users", ["usersList" => $usersList, "count" => $count]);
    }
}
<?php

/**
 * This is the Current User Model Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

class CurrentUser {


    public $isLoggedIn = false;
    public $userId;

    /**
     * @var User null
     */
    protected $user;

    /**
     * CurrentUser constructor.
     */
    public function __construct() {

        $this->user = isset($_SESSION['current_user']) && !empty($_SESSION['current_user']) ? $_SESSION['current_user'] : null;

        $this->setCurrentUser();

    }

    /**
     * We set the current user
     */
    private function setCurrentUser() {

        if (!is_null($this->user)) {

            if ($this->user->is_user) {
                $this->isLoggedIn = true;
            }
        }
    }

    /**
     * Get the details of the current user.
     * @return array
     */
    public function getUserDetails() {
        return $this->user->getUserDetails();
    }

}
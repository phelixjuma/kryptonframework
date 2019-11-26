<?php

/**
 * This is the Users Controller Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Framework\Framework\Controller;
use Kuza\Krypton\Framework\Models\CurrentUser;
use Kuza\Krypton\Framework\Models\User;

class UsersView extends Controller {

    protected $user;
    protected $currentUser;

    /**
     * UsersView constructor.
     * @param User $user
     * @param CurrentUser $currentUser
     */
    public function __construct(User $user, CurrentUser $currentUser) {
        parent::__construct();

        $this->user = $user;
        $this->currentUser = $currentUser;
    }

    /**
     * @Route("/admin/users")
     */
    public function getIndex() {

        $usersList = [["name" => "Phelix"]];
        $count = count($usersList);

        $this->view("users", ["usersList" => $usersList, "count" => $count]);
    }
}
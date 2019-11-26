<?php

/**
 * This is the Users Controller Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Controllers;

use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Framework\Controller;

class UsersApi extends Controller {

    /**
     * UserController constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @Route("/api/users")
     */
    public function getIndex() {

        $data = [["name" => "Phelix"]];
        $count = count($data);
        $message = "endpoint is GET /api/users";

        $this->apiResponse(Requests::RESPONSE_OK, true, $message, $data, $count);
    }

    /**
     * Handle creation of a user
     * @return string
     */
    public function postIndex() {

        print "endpoint is POST /api/users";

        return 'This will respond to /controller/test with only a POST method';
    }

    /**
     * Handle editing of a user
     * @param $id
     * @return string
     */
    public function patchIndex($id) {

        print "endpoint is PATCH /api/users/$id";

        return 'This will respond to /controller/test with only a PUT method';
    }

    /**
     * Handle deletion of a user
     * @param $id
     * @return string
     */
    public function deleteIndex($id) {

        print "endpoint is DELETE /api/users/$id";

        return 'This will respond to /controller/test with only a DELETE method';
    }
}
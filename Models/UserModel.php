<?php

/**
 * This is the Users Model Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Framework\DBConnection;

class UserModel extends DBConnection {

    public $is_user = false;

    public $id;
    public $email_address;
    public $phone_number;
    public $first_name;
    public $surname;
    public $other_names;
    public $gender;
    public $avatar_id;
    public $date_of_birth;
    public $status;
    public $role_id;
    public $password;

    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;
    public $is_archived = false;
    public $archived_by;
    public $archived_at;

    public static $validation_rules = [
        'email_address' => 'required|email',
        'phone_number'  => 'required',
        'first_name'    => 'required',
        'password'      => 'required|min:6',
        'role_id'       => 'required'
    ];

    public static $login_validation_rules = [
        'email_address' => 'required|email',
        'password'      => 'required|min:6'
    ];

    /**
     * UserModel constructor.
     */
    public function __construct() {
        parent::__construct("users");
    }

}
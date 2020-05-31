<?php
/**
 * This is the Role Permission model
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;


use Kuza\Krypton\Framework\DBConnection;

class RolePermissionModel extends DBConnection {

    public $id;
    public $role_id;
    public $permission_id;


    /**
     * RolePermission constructor.
     */
    public function __construct() {
        parent::__construct("roles_permissions");
    }
}

<?php
/**
 * This is the Roles model
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Framework\DBConnection;

class RoleModel extends DBConnection {

    public $id;
    public $name;
    public $type;
    public $permissions = [];
    public $is_role = false;
    public $is_back_office_role = false;

    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;
    public $is_archived = false;
    public $archived_by;
    public $archived_at;

    protected $rolePermissionModel;

    /**
     * RoleModel constructor.
     */
    public function __construct() {

        parent::__construct("roles");
    }

    /**
     * Get the details of a role
     */
    public function getDetails() {
        return $this->toArray();
    }
}

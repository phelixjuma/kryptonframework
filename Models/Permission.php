<?php
/**
 * This is the Permissions model
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Framework\Framework\DBConnection;

final class Permission extends DBConnection {

    public $id;
    public $name;
    public $description;

    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;
    public $is_archived = false;
    public $archived_by;
    public $archived_at;


    /**
     * Permission constructor.
     */
    public function __construct() {
        parent::__construct("permissions");
    }

    /**
     * Set the details of the role.
     * @param $data
     */
    private function setPermissionDetails($data) {
        if(sizeof($data) > 0) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->description = $data['description'];
            $this->created_at = isset($data['created_at']) ? $data['created_at'] : "";
            $this->created_by = isset($data['created_by']) ? $data['created_by'] : "";
            $this->updated_at = isset($data['updated_at']) ? $data['updated_at'] : "";
            $this->updated_by = isset($data['updated_by']) ? $data['updated_by'] : "";
            $this->is_archived = isset($data['is_archived']) ? $data['is_archived'] : "";
            $this->archived_by = isset($data['archived_by']) ? $data['archived_by'] : "";
            $this->archived_at = isset($data['archived_at']) ? $data['archived_at'] : "";
        }
    }

    /**
     * Get the details of a role
     */
    public function getRoleDetails() {
        return $this->toArray();
    }

    /**
     * Get permissions
     * @return array
     */
    private function getPermissions() {

        $this->table("permissions");

        $resultSet = $this->select();

        return $resultSet;
    }
}

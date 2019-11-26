<?php
/**
 * This is the Role Permission model
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Data;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Framework\DBConnection;

final class RolePermission extends DBConnection {

    public $id;
    public $role_id;
    public $permission_id;


    /**
     * RolePermission constructor.
     */
    public function __construct() {
        parent::__construct("roles_permissions");
    }

    /**
     * Get the list of permissions for a role.
     * @param $roleId
     * @return array|null
     */
    public function getRolePermissions($roleId) {

        $this->table("roles_permissions");
        $this->join("permissions", "roles_permissions.permission_id == permissions.id");

        $columns = [
            "permissions.id",
            "permissions.name",
            "permissions.description"
        ];

        $rolePermissions = $this->select(["role_id" => $roleId], $columns);

        return !is_null($rolePermissions) ? $rolePermissions : [];
    }

    /**
     * Check if is role permission.
     * @param $roleId
     * @param $permissionId
     * @return bool
     */
    public function isRolePermission($roleId, $permissionId) {
        $count = $this->count(["role_id" => $roleId, "permission_id" => $permissionId]);

        return $count > 0 ? true : false;
    }

    /**
     * Add permissions to a role
     * @param int $roleId
     * @param array $permissionIds
     * @return int
     */
    public function bulkAddRolePermissions($roleId, $permissionIds) {
        $data = [];

        foreach ($permissionIds as $pId) {
            $data[] = [
                "role_id" => $roleId,
                "permission_id" => $pId
            ];
        }

        return $this->insert($data);
    }

    /**
     * Delete a permission from a role
     * @param $roleId
     * @param $permissionId
     * @return int
     */
    public function deleteRolePermission($roleId, $permissionId) {
        return $this->delete(["role_id" => $roleId, "permission_id" => $permissionId]);
    }

    /**
     * Delete all role permissions
     * @param $roleId
     * @return int
     */
    public function deleteAllRolePermissions($roleId) {
        return $this->delete(["role_id" => $roleId]);
    }

}

<?php

/**
 * This is the Users-Roles Model Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Framework\DBConnection;

class UserRole extends DBConnection {

    /**
     * @Inject
     * @var Role
     */
    protected $roleModel;

    /**
     * @Inject
     * @var User
     */
    protected $userModel;

    /**
     * Users constructor.
     */
    public function __construct() {
        parent::__construct("users_roles");
    }

    /**
     * Set user roles
     * @param $userId
     * @return array
     */
    public function getUserRoles($userId) {

        $roles = [];

        $userRoles = parent::select(["user_id"=>$userId],["role_id"]);
        if(sizeof($userRoles) > 0) {
            foreach ($userRoles as $r) {
                $this->roleModel->setRoleById($r['role_id']);
                $roles[] = $this->roleModel->getRoleDetails();
            }
        }

        return $roles;
    }

    /**
     * Check if the user is a backend user or not.
     * @param $userId
     * @return bool
     */
    public function isBackEndUser($userId) {
        parent::table("users_roles");
        parent::join("roles","users_roles.role_id=roles.id","inner");

        $criteria = ["users_roles.user_id"=>$userId,"roles.type"=>"super_admin"];
        $records =  parent::select($criteria);

        if(sizeof($records) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Check if the role belongs to the user
     * @param $userId
     * @param $roleId
     * @return bool
     */
    public function isUsersRole($userId,$roleId) {
        return parent::exists(["user_id"=>$userId,"role_id"=>$roleId]);
    }

    /**
     * Create user role
     * @param $userId
     * @param $roleId
     * @return bool
     * @throws CustomException
     */
    public function createUserRole($userId,$roleId) {

        $data = ["role_id"=>$roleId,"user_id"=>$userId];

        $this->prepareInsertData($data);

        parent::insert($data);

        if($this->is_error) {
            throw new CustomException("Database Error: ".$this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }

    /**
     * Delete a role from a user
     * @param $userId
     * @param $roleId
     * @return bool
     * @throws CustomException
     */
    public function deleteRoleFromAUser($userId,$roleId) {
        $records = parent::select(["user_id"=>$userId,"role_id"=>$roleId],["id"]);

        parent::deleteOne($records[0]['id']);

        if($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }

    /**
     * Get backend users
     * @param string $roleType
     * @param string $offset
     * @param string $limit
     * @return array
     * @throws CustomException
     */
    public function getUsersByRoleType($roleType,$offset,$limit) {
        $users = [];
        parent::table("users_roles");
        parent::join("roles", "users_roles.role_id=roles.id","inner");

        $criteria['roles.type'] = $roleType;
        $columns = ['users_roles.user_id'];

        $queryLimit = "$offset,$limit";

        $resultSet = parent::select($criteria,$columns,"","users_roles.user_id DESC",$queryLimit);

        if ($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        if (sizeof($resultSet) > 0) {
            foreach($resultSet as $r) {
                $this->userModel->setUserByUuid($r['user_id']);
                $users[] = $this->userModel->getUserDetails();
            }
        }
        return $users;
    }
}
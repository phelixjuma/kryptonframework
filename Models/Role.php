<?php
/**
 * This is the Roles model
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Framework\DBConnection;

final class Role extends DBConnection {

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
     * Role constructor.
     * @param RolePermission $rolePermissionModel
     */
    public function __construct(RolePermission $rolePermissionModel) {
        parent::__construct("roles");

        $this->rolePermissionModel = $rolePermissionModel;
    }

    /**
     * Set the details of the role.
     * @param $role
     */
    private function setRoleDetails($role) {
        if(sizeof($role) > 0) {
            $this->is_role = true;
            $this->id = $role['id'];
            $this->name = $role['name'];
            $this->type = $role['type'];
            $this->created_at = isset($role['created_at']) ? $role['created_at'] : "";
            $this->created_by = isset($role['created_by']) ? $role['created_by'] : "";
            $this->updated_at = isset($role['updated_at']) ? $role['updated_at'] : "";
            $this->updated_by = isset($role['updated_by']) ? $role['updated_by'] : "";
            $this->is_archived = isset($role['is_archived']) ? $role['is_archived'] : "";
            $this->archived_by = isset($role['archived_by']) ? $role['archived_by'] : "";
            $this->archived_at = isset($role['archived_at']) ? $role['archived_at'] : "";

            $this->is_back_office_role = $this->type == 'admin' ? true : false;

            $this->permissions = $this->rolePermissionModel->getRolePermissions($this->id);//set the permissions of the role
        }
    }

    /**
     * Get the details of a role
     */
    public function getRoleDetails() {
        return $this->toArray();
    }

    /**
     * Set role by id
     * @param $id
     */
    public function setRoleById($id) {

        $role = parent::selectOne($id);

        if(sizeof($role) > 0) {
            $this->setRoleDetails($role);
        }
    }

    /**
     * Set role by name
     * @param $name
     */
    public function setRoleByName($name) {

        $role = parent::select(["name"=>$name]);

        if(sizeof($role) > 0) {
            $this->setRoleDetails($role[0]);
        }
    }

    /**
     * Check if a role exists
     * @param string $id
     * @param string $name
     * @return bool
     */
    public function isRole($id="",$name=""){
        $criteria = [];
        if(!empty($id)) {
            $criteria = ["id"=>$id];
        }
        elseif(!empty($name)) {
            $criteria = ["name"=>$name];
        }

        $this->prepareCriteria($criteria);

        return parent::exists($criteria);
    }

    /**
     * Get the list of roles
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getRoles($criteria=[],$offset=0,$limit=20){
        $roles = [];

        $queryLimit = "$offset,$limit";

        $this->prepareCriteria($criteria);

        $resultSet = parent::select($criteria,"","","",$queryLimit);

        if(sizeof($resultSet) > 0) {

            foreach($resultSet as $role){
                $this->setRoleDetails($role);
                $roles[] = $this->getRoleDetails();
            }
        }
        return $roles;
    }

    /**
     * Create a new role.
     * @param $data
     * @return int|null
     * @throws CustomException
     */
    public function createRole($data){

        $this->prepareInsertData($data);

        $this->insert($data);

        if ($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        return !$this->is_error ? $this->lastAffectedId() : null;
    }

    /**
     * Update the name of the specified role
     * @param $roleId
     * @param $data
     * @return boolean
     * @throws CustomException
     */
    public function updateRole($roleId,$data){

        $this->prepareUpdateData($data);

        parent::update($data,["id"=>$roleId]);

        if ($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }

    /**
     * Archive a role
     * The role is archived
     * @param $roleId
     * @return mixed
     * @throws CustomException
     */
    public function archiveRole($roleId){

        $data = [];
        $this->prepareDeleteData($data);

        parent::update($data,['id'=>$roleId]);

        if($this->is_error !== false) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }

    /**
     * Purge a role
     * The role is completely deleted
     * @param $roleId
     * @return mixed
     * @throws CustomException
     */
    public function purgeRole($roleId){

        parent::deleteOne($roleId);

        if($this->is_error !== false) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }
}

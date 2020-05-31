<?php
/**
 * This is the Roles repository
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Repository;

use Kuza\Krypton\Classes\Data;
use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Models\RoleModel;

class RoleRepository extends RoleModel {

    protected $rolePermissionRepository;

    /**
     * RoleRepository constructor.
     * @param RolePermissionRepository $rolePermissionRepository
     */
    public function __construct(RolePermissionRepository $rolePermissionRepository) {
        parent::__construct();

        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    /**
     * Set the details of the role.
     * @param $role
     */
    private function setRoleDetails($role) {
        if(sizeof($role) > 0) {

            Data::mapArrayToObject($this, $role);

            $this->is_back_office_role = $this->type == 'admin' ? true : false;

            $this->permissions = $this->rolePermissionRepository->getRolePermissions($this->id);//set the permissions of the role
        }
    }

    /**
     * Set role by id
     * @param $id
     */
    public function setRoleById($id) {

        $role = $this->selectOne($id);

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
                $roles[] = $this->getDetails();
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

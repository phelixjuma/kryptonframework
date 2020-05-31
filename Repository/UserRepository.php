<?php

/**
 * This is the Users Repository Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Repository;

use Kuza\Krypton\Classes\Data;
use Kuza\Krypton\Exceptions\ConfigurationException;
use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Classes\Utils;
use Kuza\Krypton\Config\Config;
use Kuza\Krypton\Database\Predicates\Match;
use Kuza\Krypton\Framework\Models\UserModel;

class UserRepository extends UserModel {


    protected $roleRepository;

    /**
     * User constructor.
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository) {

        parent::__construct();

        $this->roleRepository = $roleRepository;
    }

    /**
     * Set the details of the user. Uses user data from the database
     * @param $user
     */
    private function setUserDetails($user) {

        if(sizeof($user) > 0) {
            Data::mapArrayToObject($this, $user);
        }
    }

    /**
     * Set user details using the provided user's id
     * @param $id
     * @return $this
     * @throws CustomException
     */
    public function setUserById($id) {

        $this->table('users');

        $columns = [
            'users.*'];

        $user = $this->select(["users.id"=>$id],$columns);

        if ($this->is_error) {
            throw new CustomException($this->message);
        }

        if(sizeof($user) > 0) {
            $this->setUserDetails($user[0]);
        }

        return $this;
    }

    /**
     * Set user details using the user's email address
     * @param $emailAddress
     * @throws CustomException
     * @throws ConfigurationException
     */
    public function setUserByEmailAddress($emailAddress) {
        $this->table('users');

        $columns = ['users.*'];

        $user = $this->select(["users.email_address"=>$emailAddress], $columns);

        if(sizeof($user) > 0) {
            $this->setUserDetails($user[0]);
        }
    }

    /**
     * Set the users details using the user's mobile number
     * @param $mobileNo
     * @throws CustomException
     * @throws ConfigurationException
     */
    public function setUserByMobileNo($mobileNo) {

        $this->table('users');

        $columns = ['users.*'];

        $user = $this->select(["users.phone_number"=> $mobileNo], $columns);

        if ($this->is_error) {
            throw new CustomException($this->message, Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        if(sizeof($user) > 0) {
            $this->setUserDetails($user[0]);
        }
    }

    /**
     * Get the name of the user
     * @return string
     */
    public function getName() {
        return $this->first_name." ".$this->surname." ".$this->other_names;
    }

    /**
     * Get user details
     * @return array
     */
    public function getUserDetails() {
        return $this->toArray();
    }

    /**
     * Check if the user exists
     * @param string $id
     * @param string $phone
     * @param string $email
     * @return mixed
     */
    public function isUser($id="",$phone="", $email = ""){
        if(!empty($id)) {
            $criteria['id'] = $id;
        } elseif(!empty($phone)) {
            $criteria['phone_number'] = $phone;
        } elseif(!empty($email)) {
            $criteria['email_address'] = $email;
        }

        $this->prepareCriteria($criteria);

        return $this->exists($criteria);
    }

    /**
     * Validate a user's password
     * @param $password
     * @return mixed
     */
    public function validatePassword($password) {
        return Utils::verifyPassword($password,$this->password);
    }

    /**
     * Get the list of users
     * @param array $criteria
     * @param $offset
     * @param $limit
     * @return array
     * @throws CustomException
     * @throws ConfigurationException
     */
    public function getUsers($criteria=[], $offset=0,$limit= Config::PAGE_SIZE) {
        $queryLimit = "$offset,$limit";
        $usersList = [];
        $this->prepareCriteria($criteria);

        $this->table('users');

        $columns = ['users.*'];

        $users = $this->select($criteria,$columns,"","users.id desc",$queryLimit);

        if ($this->is_error) {
            throw new CustomException($this->message, Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        foreach ($users as $u) {

            $this->setUserDetails($u);

            $usersList[] = $this->getUserDetails();
        }
        return $usersList;
    }

    /**
     * Get only the specified user details.
     * @param $criteria
     * @param $columns
     * @param $offset
     * @param $limit
     * @return array
     * @throws ConfigurationException
     * @throws CustomException
     */
    public function getUsersSpecificDetails($criteria,$columns, $offset = "",$limit = "") {

        $queryLimit = !empty($offset) ?  "$offset,$limit" : "";
        $usersList = [];

        $this->prepareCriteria($criteria);

        $this->table('users');

        $users = $this->select($criteria,$columns,"","users.id desc",$queryLimit);

        if ($this->is_error) {
            throw new CustomException($this->message, Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        foreach ($users as $u) {

            $this->setUserDetails($u);

            $usersList[] = $this->getUserDetails();
        }
        return $usersList;
    }

    /**
     * Search for a user
     * @param $keyword
     * @param $offset
     * @param $limit
     * @return array
     * @throws CustomException
     * @throws ConfigurationException
     */
    public function searchUser($keyword, $offset, $limit) {

        $this->table('users');

        $columns = [
            'users.*'];

        $queryLimit = "$offset,$limit";
        $usersList = [];

        $this->prepareCriteria($criteria);

        // we add the search criteria
        $this->prepareFullTextSearchKeyWord($keyword);
        $criteria[] = new Match("users.phone_number, users.email_address, users.first_name,users.surname,users.other_names,users.national_id_number", $keyword, "keyword");

        $users = $this->select($criteria,$columns,"","users.id desc",$queryLimit);

        if ($this->is_error) {
            throw new CustomException($this->message, Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        foreach ($users as $u) {

            $this->setUserDetails($u);

            $usersList[] = $this->getUserDetails();
        }
        return $usersList;
    }

    /**
     * Get the number of users in the system
     * @param $criteria
     * @param string $keyword
     * @return int
     * @throws CustomException
     */
    public function numberOfUsers($criteria, $keyword = "") {
        $this->table("users");

        $this->prepareCriteria($criteria);

        if (!empty($keyword)) {
            $this->prepareFullTextSearchKeyWord($keyword);
            $criteria[] = new Match("users.phone_number, users.email_address, users.first_name,users.surname,users.other_names,users.national_id_number", $keyword, "keyword");
        }

        $count = $this->count($criteria);

        if ($this->is_error) {

            throw new CustomException($this->message, Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return $count;
    }

    /**
     * Count system users
     * @return int
     */
    public function countUsers() {
        return $this->count([]);
    }

    /**
     * Create user.
     * @param $user
     * @return array
     * @throws ConfigurationException
     * @throws CustomException
     */
    public function createUser($user) {

        $this->prepareInsertData($user);

        $this->insert($user);

        // we return the details of the created user
        if($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        $this->setUserById($this->lastAffectedId());

        return $this->getUserDetails();
    }

    /**
     * Update the details of a user
     * @param $id
     * @param $data
     * @return bool
     * @throws CustomException
     * @throws ConfigurationException
     */
    public function updateUser($id,$data) {

        $this->prepareUpdateData($data);

        $updated = $this->update($data,["id"=>$id]);

        // we return the details of the created user
        if($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_BAD_REQUEST);
        }
        $this->setUserById($this->lastAffectedId());

        return $updated;
    }

    /**
     * Update the password of a user
     * @param $phone_number
     * @param $data
     * @return bool
     * @throws CustomException
     * @throws ConfigurationException
     */
    public function updatePassword($phone_number,$data) {

        $this->prepareUpdateData($data);

        $this->update($data,["phone_number"=>$phone_number]);

        // we return the details of the updated user
        if($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_BAD_REQUEST);
        }
        $this->setUserById($this->lastAffectedId());

        return true;
    }

    /**
     * Completely delete a user from the system
     * @param $id
     * @return int
     * @throws CustomException
     */
    public function purgeUser($id) {
        parent:$this->table("users");

        $deleted = $this->deleteOne($id);

        if ($this->is_error) {
            throw new CustomException($this->message, Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return $deleted;
    }

    /**
     * Archive a user. This does not eliminate the records from the database.
     * @param $id
     * @return bool
     * @throws CustomException
     */
    public function archiveUser($id) {
        $this->table("users");

        $data = [];
        $this->prepareDeleteData($data);

        $this->update($data,['id'=>$id]);

        if($this->is_error !== false) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }
}
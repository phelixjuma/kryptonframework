<?php

/**
 * This is the Users Model Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Exceptions\ConfigurationException;
use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Classes\Utils;
use Kuza\Krypton\Config\Config;
use Kuza\Krypton\Database\Predicates\Match;
use Kuza\Krypton\Framework\Framework\DBConnection;

class User extends DBConnection {

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
    private $password;

    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;
    public $is_archived = false;
    public $archived_by;
    public $archived_at;

    public $avatar_url = Config::DEFAULT_AVATAR;

    protected $role;
    protected $documentModel;

    /**
     * User constructor.
     * @param Role $roleModel
     * @param Document $documentModel
     */
    public function __construct(Role $roleModel,Document $documentModel) {
        parent::__construct("users");

        $this->role = $roleModel;
        $this->documentModel = $documentModel;
    }

    /**
     * Set the details of the user. Uses user data from the database
     * @param $user
     * @throws ConfigurationException
     */
    private function setUserDetails($user) {

        if(sizeof($user) > 0) {
            $this->is_user = true;
            $this->id = isset($user['id']) ? $user['id'] : "";
            $this->password = isset($user['password']) ? $user['password'] : "";
            $this->email_address = isset($user['email_address']) ? $user['email_address'] : "";
            $this->phone_number = isset($user['phone_number']) ? $user['phone_number'] : "";
            $this->given_name = isset($user['given_name']) ? $user['given_name'] : "";
            $this->surname = isset($user['surname']) ? $user['surname'] : "";
            $this->other_names = isset($user['other_names']) ? $user['other_names'] : "";
            $this->avatar = !empty($user['avatar']) ? $user['avatar'] : "0";
            $this->avatar_url = $this->getAvatar($this->avatar);
            $this->national_id_number = isset($user['national_id_number']) ? $user['national_id_number'] : "";
            $this->gender = isset($user['gender']) ? $user['gender'] : "";
            $this->date_of_birth = isset($user['date_of_birth']) ? $user['date_of_birth'] : "";

            $this->created_at = isset($user['created_at']) ? $user['created_at'] : "";
            $this->created_by = isset($user['created_by']) ? $user['created_by'] : "";
            $this->updated_at = isset($user['updated_at']) ? $user['updated_at'] : "";
            $this->updated_by = isset($user['updated_by']) ? $user['updated_by'] : "";
            $this->is_archived = isset($user['is_archived']) ? $user['is_archived'] : false;
            $this->archived_by = isset($user['archived_by']) ? $user['archived_by'] : "";
            $this->archived_at = isset($user['archived_at']) ? $user['archived_at'] : "";

            $this->status = isset($user['status']) ? $user['status'] : "";
        }
    }

    /**
     * Set user details using the provided user's id
     * @param $id
     * @throws ConfigurationException
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
     * Get the user's avatar
     * @param $documentId
     * @return string
     * @throws ConfigurationException
     */
    public function getAvatar($documentId) {
        if ($this->documentModel != null && !empty($documentId) && $documentId != 0) {

            $this->documentModel->setDocumentById($documentId);

            return $this->documentModel->getLink();
        }
        return rtrim(Config::getSiteURL(),"/") . Config::DEFAULT_AVATAR;
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
    public function getUsers($criteria, $offset,$limit) {
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
     * Create a user
     * @param $user
     * @throws CustomException
     * @throws ConfigurationException
     */
    public function createUser($user) {

        $this->prepareInsertData($user);

        $this->insert($user);

        // we return the details of the created user
        if($this->is_error) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        $this->setUserById($this->lastAffectedId());
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
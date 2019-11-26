<?php
/**
 * This is the password reset model model
 * @author Phelix Juma <jumaphelix@kuzalab.co.ke>
 * @copyright (c) 2018, Kuza Lab
 * @package Kuzalab
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\Framework\DBConnection;

final class PasswordReset extends DBConnection {

    public $id;
    public $user_id;
    public $code;
    public $request_date;
    public $ip_address;
    public $user_agent;

    /**
     * PasswordReset constructor.
     */
    public function __construct() {
        parent::__construct("password_reset");
    }

    /**
     * Set the details
     * @param $data
     */
    private function setDetails($data) {
        if(sizeof($data) > 0) {
            $this->id = $data['id'];
            $this->user_id = isset($data['user_id']) ? $data['user_id'] : "";
            $this->code = isset($data['code']) ? $data['code'] : "";
            $this->request_date = isset($data['request_date']) ? $data['request_date'] : "";
            $this->ip_address = isset($data['ip_address']) ? $data['ip_address'] : "";
            $this->user_agent = isset($data['user_agent']) ? $data['user_agent'] : "";
        }
    }

    /**
     * Get the details
     * @return array
     */
    public function getDetails() {
        return [
            "id"            => $this->id,
            "user_id"       => $this->user_id,
            "code"          => $this->code,
            "request_date"  => $this->request_date,
            "ip_address"    => $this->ip_address,
            "user_agent"    => $this->user_agent
        ];
    }

    /**
     * Set by code
     * @param $code
     * @throws CustomException
     */
    public function setByCode($code) {

        $criteria = ["code" => $code];

        $data = parent::select($criteria);

        if ($this->is_error) {
            throw new CustomException($this->message, Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }

        if(sizeof($data) > 0) {
            $this->setDetails($data[0]);
        }
    }

    /**
     * Save the request
     * @param $data
     * @return bool
     * @throws CustomException
     */
    public function saveRequest($data) {

        $this->prepareInsertData($data);

        parent::insert($data);

        if($this->is_error !== false){
            throw new CustomException($this->message,Requests::RESPONSE_BAD_REQUEST);
        }
        return true;
    }

    /**
     * Delete a request
     * @param $id
     * @return bool
     * @throws CustomException
     */
    public function deleteRequest($id) {
        $data = [];
        $this->prepareDeleteData($data);

        parent::deleteOne($id);

        if($this->is_error !== false) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }

    /**
     * Delete the requetss by a user id
     * @param $userId
     * @return bool
     * @throws CustomException
     */
    public function deleteRequestsForUser($userId) {
        $data = [];
        $this->prepareDeleteData($data);

        parent::delete(["user_id" => $userId]);

        if($this->is_error !== false) {
            throw new CustomException($this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }

    /**
     * Set the body of the email to be sent for password reset code
     * @param $code
     * @return string
     */
    public function passwordResetEmail($code) {
        $messageTitle = "<p><h4>Password Reset Code</h4></p>";

        $messageBody = "<p>Please use this code to reset the password for the account associated with this email.<p>";
        $messageBody .= "<p> Here is your code: $code</p>";

        $messageFooter = "<p>Thanks, <br>The Ibuild Global Team<br></p>";

        $message = $messageTitle . $messageBody . $messageFooter;

        return $message;
    }
}

<?php
/**
 * This is the DB Connection model
 * @author Allan Dhoye <allan@kuzalab.com>
 * @copyright (c) 2018, Kuza Lab
 * @package Kuzalab
 */

namespace Kuza\Krypton\Framework\Framework;


use Kuza\Krypton\Config\Config;
use Kuza\Krypton\Database\Model;

class DBConnection extends Model {

    /**
     * DBConnection constructor.
     * @param null $table
     */
    public function __construct($table = null) {

        parent::__construct($this->dbConnection(), $table);
    }

    /**
     * Connect to the database. Sets the PDO connection.
     */
    private function dbConnection() {

        $pdoConnection = null;

        try {

            $source = Config::getSource();
            $user = Config::getDBUser();
            $password = Config::getDBPassword();
            $pdoConnection = new \PDO($source, $user, $password);

        } catch (\Exception $ex) {
            $title = 'Connection Failed';
            switch ($ex->getCode()) {
                case 2002:
                    $message = 'Attempt to Connect to database failed';
                    break;
                default:
                    $message = $ex->getMessage();
                    break;
            }
            $response = json_encode(['message' => $message, 'title' => $title, 'status' => 'error']);
            die($response);
        }

        return $pdoConnection;
    }
}

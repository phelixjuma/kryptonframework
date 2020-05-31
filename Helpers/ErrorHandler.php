<?php
/**
 * Created by PhpStorm.
 * User: phelix
 * Date: 5/17/20
 * Time: 12:14 PM
 */

namespace Kuza\Krypton\Framework\Helpers;


use Kuza\Krypton\App;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Classes\Response;

/**
 * Exception handler.
 * @param mixed $ex
 * @throws \Exception
 */

class ErrorHandler {


    /**
     * Handle errors arising in the system
     * @param App $app
     * @param $errorNumber
     * @param $errorString
     * @param $errorFile
     * @param $errorLine
     * @return bool
     */
    public function errorHandler(App $app, $errorNumber, $errorString, $errorFile, $errorLine) {
        if (!(error_reporting() & $errorNumber)) {
            // This error code is not included in error_reporting, so let it fall through to the standard PHP error handler
            return false;
        }

        switch ($errorNumber) {
            case E_ERROR:
                $errorType = "FATAL ERROR";
                break;
            case E_WARNING:
                $errorType = "WARNING";
                break;
            case E_PARSE:
                $errorType = "PARSE ERROR";
                break;
            case E_NOTICE:
                $errorType = "NOTICE";
                break;
            case E_CORE_ERROR:
                $errorType = "CORE ERROR";
                break;
            case E_CORE_WARNING:
                $errorType = "CORE WARNING";
                break;
            case E_COMPILE_ERROR:
                $errorType = "COMPILE TIME ERROR";
                break;
            case E_USER_ERROR:
                $errorType = "USER GENERATED ERROR";
                break;
            case E_USER_WARNING:
                $errorType = "USER GENERATED WARNING";
                break;
            case E_USER_NOTICE:
                $errorType = "USER GENERATED NOTICE";
                break;
            case E_STRICT:
                $errorType = "RUNTIME NOTICE";
                break;
            case E_RECOVERABLE_ERROR:
                $errorType = "CATCHABLE FATAL ERROR";
                break;
            case E_DEPRECATED:
                $errorType = "DEPRECATED CALL ERROR";
                break;
            default:
                $errorType = "UNKNOWN ERROR";
                break;
        }

        $message = "$errorType on line $errorLine in file $errorFile. Error says: $errorString";

        $trace['error_details'] = [
            "line" => $errorLine,
            "file" => $errorFile,
            "error_string"  => $errorString,
            "error_number" => $errorNumber
        ];

        $trace['error_details']['message'] = $message;
        $trace['error_details']['Log_Level'] = $errorType ;

        // here we can log the errors to file or db

        return true;

    }

    /**
     * Function to handle exceptions
     * @param App $app
     * @param $ex
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function exceptionHandler(App $app, $ex) {

        $trace = $ex->getTrace();

        $message = "Exception on line {$ex->getLine()} in file {$ex->getFile()}. Error says: {$ex->getMessage()}";

        $log_data['Log_Level'] = 'EXCEPTION';

        $log_data['error_details'] = [
            "line" => $ex->getLine(),
            "file" => $ex->getFile(),
            "trace" => $ex->getTrace(),
            "message"  => $ex->getMessage(),
            "code" => $ex->getCode(),
            "previous" => $ex->getPrevious(),
            "string_version" => $ex->getTraceAsString()
        ];

         // we could log the errors to file or db

        if ($app->requests->isJsonRequest()) {

            $response = new \Kuza\Krypton\Framework\JsonResponse();

            $response->message = $message;

            if ($this->requests->backtrace == 1) {

                $response->errors = $trace;
            }

            $app->response->status_code(500)->json($response->toArray());

        } else {
            $app->view("500", [], compact('message', 'trace'));
        }
    }
}



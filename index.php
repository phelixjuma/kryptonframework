<?php

use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Exceptions\UnauthenticatedException;
use Kuza\Krypton\Exceptions\UnauthorizedException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Framework\App;

session_start();
session_regenerate_id();

//require the composer vendor libraries autoloader
require "vendor/autoload.php";

require_once "App.php";

$app = new App();


try {

    $app->init();

    $app->run();

} catch (UnauthenticatedException $ex) {

    $app->requests->apiData = [
        "success" => false,
        "message" => "Unauthorized: " . $ex->getMessage(),
        "code" => Requests::RESPONSE_UNAUTHORIZED
    ];

    if ($app->requests->backtrace == 1) {
        $app->requests->apiData['data'] = $ex->getTrace();
    }

    $app->requests->sendResponse();

} catch (UnauthorizedException $ex) {

    $app->requests->apiData = [
        "success" => false,
        "message" => "Forbidden: " . $ex->getMessage(),
        "code" => Requests::RESPONSE_FORBIDDEN
    ];

    if ($app->requests->backtrace == 1) {
        $app->requests->apiData['data'] = $ex->getTrace();
    }

    $app->requests->sendResponse();

} catch (CustomException $ex) {

    $app->requests->apiData = [
        "success" => false,
        "message" => "Custom Exception: " . $ex->getMessage(),
        "code" => Requests::RESPONSE_INTERNAL_SERVER_ERROR
    ];

    if ($app->requests->backtrace == 1) {
        $app->requests->apiData['data'] = $ex->getTrace();
    }

    $app->requests->sendResponse();

}  catch (Exception $ex) {

    $app->requests->apiData = [
        "success" => false,
        "message" => "Unknown Exception: " . $ex->getMessage(),
        "code" => Requests::RESPONSE_INTERNAL_SERVER_ERROR
    ];

    if ($app->requests->backtrace == 1) {

        $app->requests->apiData['data'] = $ex->getTrace();
    }

    $app->requests->sendResponse();

}
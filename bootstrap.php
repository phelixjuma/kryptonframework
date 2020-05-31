<?php



use Kuza\Krypton\App;
use Kuza\Krypton\Framework\JsonResponse;
use Kuza\Krypton\Exceptions\UnauthenticatedException;
use Kuza\Krypton\Exceptions\UnauthorizedException;

session_start();
session_regenerate_id();

//require the composer vendor libraries autoloader
require "vendor/autoload.php";

$app = new App();

try {

    $app
        ->setControllersDirectory("Controllers")
        ->setViewsDirectory("Views")
        ->setLayoutsDirectory("Layouts")
        ->setLogsDirectory("Logs")
        ->setRoutesFile("routes")
        ->setExceptionHandler(["\Kuza\Krypton\Framework\Helpers\ErrorHandler", "exceptionHandler"])
        ->setErrorHandler(["\Kuza\Krypton\Framework\Helpers\ErrorHandler", "errorHandler"])
        ->init();

} catch (Exception $ex) {

    $response = new JsonResponse();

    if ($ex instanceof UnauthenticatedException) {
        $code = 401;
    } elseif ($ex instanceof UnauthorizedException) {
        $code = 403;
    } else {
        $code = 500;
    }

    if ($app->requests->isJsonRequest()) {

        $response->message = $ex->getMessage();

        if ($app->requests->backtrace == 1) {

            $response->errors = $ex->getTrace();
        }

        $app->response->status_code($code)->json($response->toArray());

    } else {
        // web access. redirect to error pages
        $message = $ex->getMessage();
        $trace = $ex->getTrace();

        $app->view($code, [], compact('message', 'trace'));
    }

    $app->view($code, [], compact('message', 'trace'));

}
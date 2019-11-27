<?php


/**
 * This is main app class file
 * @author Phelix Juma <jumaphelix@kuzalab.co.ke>
 * @copyright (c) 2018, Kuza Lab
 * @package Kuzalab
 */

namespace Kuza\Krypton\Framework;

use DI\Container;
use Dotenv\Dotenv;
use Kuza\Krypton\Framework\Framework\RouterResolver;
use Kuza\Krypton\Framework\Models\User;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;

use Kuza\Krypton\Classes\Benchmark;
use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Config\Config;


/**
 * Main application class.
 */
final class App {

    public $isLoggedIn = false; //checks if user is logged in

    /**
     * @var User $currentUser
     */
    public $currentUser;   //details of the current user

    public $currentRole;

    /**
     * Holds all details of a received request.
     * @var Requests $requests
     */
    public $requests;

    /**
     * Holds the routing information from alto router library
     * @var RouteCollector $router
     */
    public $router;

    /**
     * Dependency Injection Service Container
     * @var Container $DIContainer
     */
    public $DIContainer;

    /**
     * @var Benchmark $benchmark the benchmark handler
     */
    public $benchmark;

    /**
     * @var \PDO $pdoConnection the database connection object
     */
    public $pdoConnection;

    /**
     * The current view to display
     * @var $view
     */
    public $view;

    /**
     * The data to be displayed in view.
     * @var array $viewData
     */
    public $viewData = [];

    /**
     * The errors to be displayed should there be any.
     * @var array
     */
    public $viewErrors = [];

    /**
     * Initialize the system
     * @throws \Exception
     */
    public function init() {

        //set exception handler
        set_exception_handler([$this, 'handleException']);

        // set error handler
        set_error_handler([$this, 'handleErrors']);

        //set spl autoload
        spl_autoload_register([$this, 'loadClass']);

        // we start the benchmark
        $this->benchmark = new Benchmark();
        $this->benchmark->start();

        // error reporting - all errors for development. Works when display_errors = On in php.ini file
        error_reporting(E_ALL | E_STRICT);
        ini_set("display_errors",1);
        ini_set("html_errors", 1);
        ini_set("display_startup_errors", 1);
        ini_set("log_errors", 1);
        ini_set("error_log", Config::LOGS_DIR . "php-mt-error.log");
        ini_set("ignore_repeated_errors", 1);
        ini_set('memory_limit', '1024M');
        ini_set('upload_max_filesize', '1024M');
        ini_set('post_max_size', '1024M');

        mb_internal_encoding('UTF-8');

        // load the environment file
        try {
            $dotenv = new Dotenv(__DIR__);
            $dotenv->load();
        } catch (\Exception $e) {
//            print_r($e->getMessage());
        }

        //set the php-di container
        $builder = new \DI\ContainerBuilder();
        //$builder->useAnnotations(true);

        $this->DIContainer = $builder->build();

        $this->requests = new Requests();

        $this->router = new RouteCollector();

        // we show errors when in backtrace mode.
        if ($this->requests->backtrace == 1) {
            ini_set("display_errors",1);
        }
    }

    /**
     * Run the application!
     *
     * @throws CustomException
     */
    public function run() {

        //When CORS is enabled, a preflight is always used by Firefox and other browsers. All preflights are thus handled here.
        $this->router->options($this->requests->module,function (){});

        // $this->runDisplayPage($this->getDisplayPage());

        require $this->getRouteDefinitions();

        $this->dispatchRequests();
    }

    /**
     * Exception handler.
     * @param mixed $ex
     * @throws \Exception
     */
    public function handleException($ex) {
        $code = 500;
        $description = "Exception on line {$ex->getLine()} in file {$ex->getFile()}. Error says: {$ex->getMessage()}";
        if ($ex instanceof CustomException) {
            $code = $ex->getCode();
        }

        $debug_trace['Log_Level'] = 'EXCEPTION';

        $debug_trace['error_details'] = [
            "line" => $ex->getLine(),
            "file" => $ex->getFile(),
            "trace" => $ex->getTrace(),
            "message"  => $ex->getMessage(),
            "description" => $description,
            "code" => $ex->getCode(),
            "previous" => $ex->getPrevious(),
            "string_version" => $ex->getTraceAsString()
        ];

        // remove sensitive data from the requests
        $cleanedRequests = $this->removeSensitiveDataFromStackTraceRequests();

        $debug_trace['received_request'] = $cleanedRequests;

        $this->requests->apiData = [
            "message" => $description,
            "code"  => $code
        ];

        // we add the trace to the response when in debug mode. This is achieved by setting the backtrace filter to the endpoint when testing.
        if (isset($this->requests->backtrace) && $this->requests->backtrace == 1) {
            $this->requests->apiData['trace'] = $debug_trace;
        }

        $this->requests->sendResponse();

        // work on error logging.
    }

    /**
     * Handle errors arising in the system
     * @param $errorNumber
     * @param $errorString
     * @param $errorFile
     * @param $errorLine
     * @return bool
     */
    public function handleErrors($errorNumber, $errorString, $errorFile, $errorLine) {
        if (!(error_reporting() & $errorNumber)) {
            // This error code is not included in error_reporting, so let it fall through to the standard PHP error handler
            return false;
        }

        $debug_trace['error_details'] = [
            "line" => $errorLine,
            "file" => $errorFile,
            "error_string"  => $errorString,
            "error_number" => $errorNumber
        ];

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

        $debug_trace['error_details']['message'] = "$errorType on line $errorLine in file $errorFile. Error says: $errorString";
        $debug_trace['error_details']['Log_Level'] = $errorType ;

        // remove sensitive data from the requests
        $cleanedRequests = $this->removeSensitiveDataFromStackTraceRequests();

        $debug_trace['received_request'] = $cleanedRequests;

        return true;
    }

    /**
     * Removes sensitive data from the error stack trace
     * @return mixed
     */
    private function removeSensitiveDataFromStackTraceRequests() {

        $requests = $this->requests;

        // check if there is a password in the body and replace it
        if (isset($requests->body['password'])) {
            $requests->body['password'] = "*******";
        }
        // eliminate jwt secret
        if (isset($requests->headers->jwt_secret)) {
            $requests->headers->jwt_secret = '*****';
        }
        // eliminate db user
        if (isset($requests->headers->db_user)) {
            $requests->headers->db_user = '*****';
        }
        // eliminate db password
        if (isset($requests->headers->db_password)) {
            $requests->headers->db_password = '*****';
        }
        // eliminate mail details
        if (isset($requests->headers->mail_username)) {
            $requests->headers->mail_username = '*****';
        }
        if (isset($requests->headers->mail_password)) {
            $requests->headers->mail_password = '*****';
        }
        if (isset($requests->headers->mail_port)) {
            $requests->headers->mail_port = '*****';
        }
        // eliminate sensitive AWS details
        if (isset($requests->headers->aws_access_key)) {
            $requests->headers->aws_access_key = '*****';
        }
        if (isset($requests->headers->aws_secret_key)) {
            $requests->headers->aws_secret_key = '*****';
        }
        if (isset($requests->headers->cloudfront_key_pair_id)) {
            $requests->headers->cloudfront_key_pair_id = '*****';
        }

        return $requests;
    }

    /**
     * Class loader.
     */
    public function loadClass($name) {
        //we eliminate the root namespace. What remains is of the form: Models\Users
        $namespace = str_ireplace("Kuza\Krypton\\Framework\\","", $name);

        //we define the directory seperator
        $directorySeperator = "\\"; // Windows-based systems
        if(DIRECTORY_SEPARATOR == "/"){
            $directorySeperator = "/";//Unix systems
        }

        //we replace the backslash in the namespace with the directory seperator and add the class extension
        $classFile = str_ireplace("\\",$directorySeperator, $namespace).".php";

//        die($classFile);

        if(is_file($classFile)){
            require_once  $classFile;
        }
    }

    /**
     * Dispatches requests
     */
    private function dispatchRequests() {

        try {

            // instantiate the resolver.
            $resolver = new RouterResolver($this->DIContainer);

            //instantiate the dispatcher
            $dispatcher =  new Dispatcher($this->router->getData(),$resolver);

            //dispatch the data. We don't capture the response because our controllers set the requests api data directly

            $dispatcher->dispatch($this->requests->method, $this->requests->uri);

        } catch (HttpMethodNotAllowedException $e) {
            echo $e->getMessage();
        } catch (HttpRouteNotFoundException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get the routes definition file
     * @return string
     */
    private function getRouteDefinitions() {
        return "routes.php";
    }

    /**
     * Gets the layout file
     * @return string
     */
    public function getLayout() {
        return "Layouts/layout.php";
    }
}
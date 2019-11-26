<?php

/**
 * This is the Users Controller Class
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Framework;


use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Config\Config;
use Kuza\Krypton\Framework\App;

class Controller {

    /**
     * @var App
     */
    protected $app;

    protected $requests;

    /**
     * UserController constructor.
     */
    public function __construct() {
        global $app;

        $this->app = $app;
        $this->requests = $app->requests;
    }

    /**
     * Handle API response
     * @param int $code
     * @param bool $success
     * @param string $message
     * @param array $data
     * @param int $totalRecords
     */
    public function apiResponse(int $code = Requests::RESPONSE_OK, bool $success = true, string $message = "", $data = [], int $totalRecords = 0) {

        $this->requests->apiData = [
            "success"   => $success,
            "data"      => $data,
            "total_records"     =>  $totalRecords,
            "code"      =>  $code
        ];

        //Send response
        $this->requests->sendResponse();
    }

    /**
     * Render a view
     * @param $view
     * @param array $data
     */
    public function view($view, $data = []) {

        // we set the view details.
        $this->app->view = $this->getViewTemplate($view);

        $this->app->viewData = $data;

        // we require the layout file
        require $this->app->getLayout();
    }

    /**
     * Get the phtml template page for the page
     * @param $page
     * @return string
     */
    private function getViewTemplate($page) {
        return Config::VIEWS_DIR . $page . '.phtml';
    }

    /**
     * Checks if a page has a template
     * @param $page
     * @return bool
     */
    private function hasViewTemplate($page) {
        return file_exists($this->getViewTemplate($page));
    }

}
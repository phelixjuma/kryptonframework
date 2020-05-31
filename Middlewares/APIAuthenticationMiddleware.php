<?php
/**
 * Created by PhpStorm.
 * User: phelix
 * Date: 11/27/19
 * Time: 2:27 PM
 */

namespace Kuza\Krypton\Framework\Middlewares;

use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Exceptions\UnauthenticatedException;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;


class APIAuthenticationMiddleware implements IMiddleware {

    /**
     * @param Request $request
     * @throws UnauthenticatedException
     */
    public function handle(Request $request): void
    {
        global $app;

        // Authenticate user, will be available using request()->user
        $app->currentUser = $_SESSION['user'];

        // If authentication failed, redirect request to user-login page.
        if($app->currentUser === null) {
            throw new UnauthenticatedException("Unauthorized access", Requests::RESPONSE_UNAUTHORIZED);
        }
    }
}
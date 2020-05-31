<?php
/**
 * Created by PhpStorm.
 * User: phelix
 * Date: 11/27/19
 * Time: 2:27 PM
 */

namespace Kuza\Krypton\Framework\Middlewares;

use Kuza\Krypton\Framework\Repository\SessionAuthentication;
use Kuza\Krypton\Framework\RoutesHelper;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;


class AuthenticationMiddleware implements IMiddleware {

    /**
     * @param Request $request
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Kuza\Krypton\Exceptions\CustomException
     */
    public function handle(Request $request): void
    {
        global $app;

        /**
         * @var SessionAuthentication $sessionAuth
         */
        $sessionAuth = $app->DIContainer->get("\Kuza\Krypton\Framework\Repository\SessionAuthentication");

        // Authenticate user, will be available using request()->user
        $request->user = $sessionAuth->authenticate();

        // If authentication failed, redirect request to user-login page.
        if($request->user->id === null) {
            RoutesHelper::redirect(RoutesHelper::url("auth/login"));
        }
    }
}
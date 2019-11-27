<?php

/**
 * This is Route Resolver for php-di
 *
 * This custom resolver is built to support dependency injection which is not handled by the default phproute resolver.
 *
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Framework;

use DI\Container;
use Phroute\Phroute\HandlerResolverInterface;

class RouterResolver implements HandlerResolverInterface
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function resolve($handler)
    {
        /*
         * Only attempt resolve uninstantiated objects which will be in the form:
         *
         *      $handler = ['App\Controllers\Home', 'method'];
         */
        if(is_array($handler) and is_string($handler[0]))
        {
            $handler[0] = $this->container->get($handler[0]);
        }

        return $handler;
    }
}
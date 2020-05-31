<?php

use Kuza\Krypton\Framework\App;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;


require_once "../bootstrap.php";

// we get the name of the task
$options  = getopt(null,["name:"]);
$taskName = $options['name'];

// we set the task class
$taskClass = "Kuza\Krypton\Framework\TaskControllers\\".$taskName."TaskController";

// we use DI to get the task class instance
$task = isset($app) ? $app->DIContainer->get($taskClass): null;

// we run the task
$task->runTask();
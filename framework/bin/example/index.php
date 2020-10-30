<?php

use App\Core\App;
use App\Core\Route;
use App\Controllers\ExampleController;

require_once('vendor/autoload.php');

// Create our application instance.
(new App())
    ->getRouter()
    ->addRoutes([
        '/' => new Route(ExampleController::class, 'welcome')
    ])->getResponse();

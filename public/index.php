<?php
include_once "/Users/mustafaculban/Desktop/BackboneFramework/config/Constants.php";

use AppInit\App;
use AppInit\Constants;
use Controllers\UtilsController as Utils;

require Constants::get("BASEFOLDER") . '/vendor/autoload.php';

// Instantiate app
$app = App::init(true);


// Include all routes
SlimRouteInit\Router::getAllRoutes($app);


// Adding cors headers
Utils::CORS($app);

// Run application
App::run();

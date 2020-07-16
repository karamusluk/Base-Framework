<?php
include_once "/Users/mustafaculban/Desktop/BackboneFramework/config/Constants.php";
use AppInit\Constants;
require Constants::get("BASEFOLDER") . '/vendor/autoload.php';

include_once Constants::get("BASEFOLDER")."/opt/workers/ProxyChecker.php";


$instance = new ProxyChecker();

$instance->checkFileAndInsert();
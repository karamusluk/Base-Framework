<?php
set_time_limit(0);
use AppInit\Constants;
require Constants::get("BASEFOLDER") . '/vendor/autoload.php';

include_once Constants::get("BASEFOLDER")."/opt/workers/ProxyChecker.php";

(new ProxyChecker())->checkDatabaseAndModify();
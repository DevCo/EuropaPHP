<?php

use Europa\Di\Container;

define('EUROPA_START_TIME', microtime());

require_once __DIR__ . '/../app/bootstrap.php';

Container::europa()->app->run();
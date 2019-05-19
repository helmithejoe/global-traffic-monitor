<?php

use WebSocket\Server;

require(__DIR__ . '/lib/SplClassLoader.php');
$classLoader = new SplClassLoader('WebSocket', __DIR__ . '/lib');
$classLoader->register();

//configure server parameters
$server = new WebSocket\Server('127.0.0.1', 8000, false);

//server's ability to capture traffic from everywhere
$server->setCheckOrigin(false);

// Hint: Status application should not be removed as it displays usefull server informations:
$server->registerApplication('status', \WebSocket\Application\StatusApplication::getInstance());
//here is our traffic application
$server->registerApplication('traffic', \WebSocket\Application\TrafficApplication::getInstance());

$server->run();
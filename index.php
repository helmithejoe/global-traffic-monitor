<?php
require_once('vendor/autoload.php');
$f3 = \Base::instance();
$f3->config('app/config/config.ini');
$f3->set('AUTOLOAD','app/;app/models/');
$f3->route('GET /@controller/@action','Controllers\@controller->@action');
$f3->route('POST /@controller/@action','Controllers\@controller->@action');

$traffic = new \TrafficModel($f3);
$traffic->log;

$f3->set('DEBUG', 3);
//run the app
$f3->run();

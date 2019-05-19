<?php
require_once('vendor/autoload.php');

//framework instance
$f3 = \Base::instance();

//load config file
$f3->config('app/config/config.ini');

//autoload our models
$f3->set('AUTOLOAD','app/;app/models/');

//framework routing
$f3->route('GET /@controller/@action','Controllers\@controller->@action');
$f3->route('POST /@controller/@action','Controllers\@controller->@action');

$f3->set('DEBUG', 3);
//run the app
$f3->run();

<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Lib\Listener;

require dirname(__DIR__) . '/vendor/autoload.php';

$f3 = \Base::instance();
$f3->config('app/config/config.ini');

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Listener()
        )
    ),
    $f3->get('websocket_port')
);

$server->run();

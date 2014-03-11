<?php
use Elfet\Chat\Server;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;

require __DIR__ . '/vendor/autoload.php';

$config = include __DIR__ . '/config.php';

$memcache = new Memcache;
$memcache->connect('localhost', 11211);

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SessionProvider(
                new Server(),
                new MemcacheSessionHandler($memcache)
            )
        )
    ),
    $config['server.port'],
    $config['server.host']
);

$server->run();
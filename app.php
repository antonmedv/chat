<?php
require __DIR__ . '/vendor/autoload.php';

$app = new Elfet\Chat\Application(include __DIR__ . '/config.php');

$app->get('/', function () use ($app) {
    return $app->render('chat.phtml', [
        'user' => $app['user'],
    ]);
})->bind('index');

$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    $app['facebook']->destroySession();
    return $app->redirect($app->url('index'));
})->bind('logout');

$app->run();
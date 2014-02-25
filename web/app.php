<?php
require __DIR__ . '/../vendor/autoload.php';

$app = new Elfet\Chat\Application(include __DIR__ . '/../config.php');

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

$app['facebook'] = $app->share(function () use ($app) {
    return new Facebook([
        'appId' => $app['facebook.app_id'],
        'secret' => $app['facebook.secret'],
        'allowSignedRequest' => false
    ]);
});

$app['user'] = function () use ($app) {
    return $app['session']->get('user');
};

$app->before(function ($request) use ($app) {
    $user = $app['user'];

    if (null === $user) {
        $facebook = $app['facebook'];
        $result = $facebook->api(array(
            'method' => 'fql.query',
            'query' => 'SELECT uid, name, pic_square FROM user WHERE uid = me()',
        ));

        if (!empty($result)) {
            $app['session']->set('user', $result[0]);
            return;
        }

        return $app->render('login.phtml', [
            'loginUrl' => $facebook->getLoginUrl(),
        ]);
    }
});

$app->get('/', function () use ($app) {
    return $app->render('chat.phtml', [
        'user' => $app['user'],
    ]);
})->bind('index');

$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    $app['facebook']->destroySession();
    return $app->redirect($app['url_generator']->generate('index'));
})->bind('logout');

$app->run();
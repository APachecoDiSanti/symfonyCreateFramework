<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Simplex\StringResponseListener;

function render_template(Request $request) {
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/../src/pages/%s.php', $_route);
    return new Response(ob_get_clean());
}

$routes = include __DIR__.'/../src/app.php';
$container = include __DIR__.'/../src/container.php';

$container->setParameter('debug', true);
$container->setParameter('charset', 'UTF-8');
$container->setParameter('routes', $routes);

$container->register('listener.string_response', StringResponseListener::class);

$container->getDefinition('dispatcher')
    ->addMethodCall(
        'addSubscriber', 
        [
            new Reference('listener.string_response')
        ]
);

$request = Request::createFromGlobals();

$response = $container->get('framework')->handle($request);

$response->send();

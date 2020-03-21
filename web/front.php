<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing;

$request = Request::createFromGlobals();
$requestStack = new RequestStack();
$routes = include __DIR__.'/../src/app.php';

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$errorHandler = function (FlattenException $exception) {
    $msg = 'Something went wrong! ('.$exception->getMessage().')';
    return new Response($msg, $exception->getStatusCode());
};

$listener = new ErrorListener(
    'Calendar\Controller\ErrorController::exception'
);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new ErrorListener($errorHandler));
$dispatcher->addSubscriber(new ResponseListener('UTF-8'));
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));
$dispatcher->addSubscriber(new Simplex\StringResponseListener());
$dispatcher->addSubscriber($listener);

$framework = new Simplex\Framework(
    $dispatcher,
    $controllerResolver,
    $requestStack,
    $argumentResolver
);

$framework->handle($request)->send();

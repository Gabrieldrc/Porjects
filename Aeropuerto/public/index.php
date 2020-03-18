<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {

        return false;

    }
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$conn = new \MongoDB\Client("mongodb://localhost");
$registroDeVuelosServicio = new \Src\Servicios\RegistroDeVuelosServicio($conn->registroDeVuelosServicios->registroDeVuelos);
$vuelosService = new \Src\Servicios\VuelosServicio($conn->vuelosServicios->aviones, $registroDeVuelosServicio);

$app = AppFactory::create();

$app->add(function($serverRequest, $requestHandler)
            use ($twig, $vuelosService, $registroDeVuelosServicio) {

    $serverRequest = $serverRequest->withAttribute('twig', $twig);
    $serverRequest = $serverRequest->withAttribute('registroDeVuelosServicio', $registroDeVuelosServicio);
    $serverRequest = $serverRequest->withAttribute('vuelosService', $vuelosService);

    return $requestHandler->handle($serverRequest);

});

$controllerService = new \Src\Servicios\ControllerServicio();
$controllerService->setup($app, __DIR__ . '/../src/Controllers/' );

$app->run();
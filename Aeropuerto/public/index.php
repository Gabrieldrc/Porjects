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

$app->get('/home', function (Request $request, Response $response, $args) use ($twig) {
    $template = $twig->load('index.html');
    $response->getBody()->write(
        $template->render(['Titulo' => 'Gabriel Airlines'])
    );

    return $response;

});

$app->get('/registro', function (Request $request, Response $response, $args) use ($twig, $registroDeVuelosServicio) {
    $template = $twig->load('registro.html');
    $response->getBody()->write(
        $template->render([
            'Titulo' => 'Gabriel Airlines',
            'listaVuelos' => $registroDeVuelosServicio->mostrarVuelos(),
            ])
    );

    return $response;

});

$app->get('/registro/nuevoVuelo/{error}', function (Request $request, Response $response, $args) use ($twig) {
    $template = $twig->load('formularioNuevoVuelo.html');
    $response->getBody()->write(
        $template->render([
            'Titulo' => 'Gabriel Airlines',
            'Error' => ($args['error'] == 'error') ? true : false,
        ])
    );

    return $response;

});

$app->post('/nuevoVuelo', function (Request $request, Response $response, $args) use ($twig, $registroDeVuelosServicio) {
    $lista = $request->getParsedBody();
    $result = $registroDeVuelosServicio->registrarVuelo($lista['origen'], $lista['destino']);
    if ($result == false) {
        $response = $response->withStatus(302);
        $response = $response->withHeader('Location', '/registro/nuevoVuelo/error');

        return $response;

    }
    $response = $response->withStatus(302);
    $response = $response->withHeader('Location', '/registro');

    return $response;

});

$app->get('/aviones', function (Request $request, Response $response, $args) use ($twig, $vuelosService) {
    $template = $twig->load('aviones.html');
    $response->getBody()->write(
        $template->render([
            'Titulo' => 'Gabriel Airlines',
            'listaVuelos' => $vuelosService->buscarAviones(),
            ])
    );

    return $response;

});

$app->get('/aviones/nuevoAvion/{error}', function (Request $request, Response $response, $args) use ($twig) {
    $template = $twig->load('formularioNuevoAvion.html');
    $response->getBody()->write(
        $template->render([
            'Titulo' => 'Gabriel Airlines',
            'Error' => ($args['error'] == 'error') ? true : false,
        ])
    );

    return $response;

});

$app->post('/nuevoAvion', function (Request $request, Response $response, $args) use ($twig, $vuelosService) {
    $lista = $request->getParsedBody();
    $result = $vuelosService->habilitarNuevoAvion($lista['puestos'], $lista['ubicacion']);
    
    if ($result == false) {
        $response = $response->withStatus(302);
        $response = $response->withHeader('Location', '/aviones/nuevoAvion/error');

        return $response;

    }
    $response = $response->withStatus(302);
    $response = $response->withHeader('Location', '/aviones');

    return $response;

});

$app->get('/aviones/asignarVuelo/{error}', function (Request $request, Response $response, $args) use ($twig, $vuelosService) {
    $template = $twig->load('formularioAsignarAvionAVuelo.html');
    $response->getBody()->write(
        $template->render([
            'Titulo' => 'Gabriel Airlines',
            'Error' => ($args['error'] == 'error') ? true : false,
        ])
    );

    return $response;

});

$app->post('/asignarVuelo', function (Request $request, Response $response, $args) use ($twig, $vuelosService, $registroDeVuelosServicio) {
    $lista = $request->getParsedBody();
    $avion = $vuelosService->asignarVuelo($lista['avionId'], $lista['idVuelo']);
    $result = $registroDeVuelosServicio->asignarAvionEnRegistro($avion);
    if (! $result) {
        $response = $response->withStatus(302);
        $response = $response->withHeader('Location', '/aviones/asignarVuelo/error');

        return $response;

    }
    $response = $response->withStatus(302);
    $response = $response->withHeader('Location', '/aviones');

    return $response;

});

$app->run();
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
$vuelosService = new \Src\Servicios\VuelosServicio($conn->vuelosServicios->aviones);
$registroDeVuelosServicio = new \Src\Servicios\RegistroDeVuelosServicio($conn->registroDeVuelosServicios->registroDeVuelos);

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
            'Error' => ($args['error'] == 'error') ? True : False,
            ])
    );
    return $response;
});

$app->post('/nuevoVuelo', function (Request $request, Response $response, $args) use ($twig, $vuelosService, $registroDeVuelosServicio) {
    $result = $registroDeVuelosServicio->registrarVuelo($_POST['origen'], $_POST['destino']);
    if ($result == False) {
        $response = $response->withStatus(302);
        $response = $response->withHeader("Location","/registro/nuevoVuelo/error");
        return $response;  
    } 
    $response = $response->withStatus(302);
    $response = $response->withHeader("Location","/registro");
    return $response; 
});

$app->run();

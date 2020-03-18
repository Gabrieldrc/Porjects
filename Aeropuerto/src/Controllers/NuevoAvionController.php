<?php

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NuevoAvionController implements \Src\Interfaces\Controller {
    public function config($app){
        $app->get('/aviones/nuevoAvion/{error}', function (Request $request, Response $response, $args) {
            $template = $request->getAttribute('twig')->load('formularioNuevoAvion.html');
            $response->getBody()->write(
                $template->render([
                    'Titulo' => 'Gabriel Airlines',
                    'Error' => ($args['error'] == 'error') ? true : false,
                ])
            );

            return $response;

        });

        $app->post('/nuevoAvion', function (Request $request, Response $response, $args) {
            $lista = $request->getParsedBody();
            $result = $request->getAttribute('vuelosService')->habilitarNuevoAvion($lista['puestos'], $lista['ubicacion']);

            if ($result == false) {
                $response = $response->withStatus(302);
                $response = $response->withHeader('Location', '/aviones/nuevoAvion/error');

                return $response;

            }
            $response = $response->withStatus(302);
            $response = $response->withHeader('Location', '/aviones');

            return $response;

        });
    }
}
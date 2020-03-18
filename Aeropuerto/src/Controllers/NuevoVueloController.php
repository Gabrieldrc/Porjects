<?php

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NuevoVueloController implements \Src\Interfaces\Controller {
    public function config($app){
        $app->get('/registro/nuevoVuelo/{error}', function (Request $request, Response $response, $args) {
            $template = $request->getAttribute('twig')->load('formularioNuevoVuelo.html');
            $response->getBody()->write(
                $template->render([
                    'Titulo' => 'Gabriel Airlines',
                    'Error' => ($args['error'] == 'error') ? true : false,
                ])
            );

            return $response;

        });

        $app->post('/nuevoVuelo', function (Request $request, Response $response, $args) {
            $lista = $request->getParsedBody();
            $result = $request->getAttribute('registroDeVuelosServicio')->registrarVuelo($lista['origen'], $lista['destino']);
            if ($result == false) {
                $response = $response->withStatus(302);
                $response = $response->withHeader('Location', '/registro/nuevoVuelo/error');

                return $response;

            }
            $response = $response->withStatus(302);
            $response = $response->withHeader('Location', '/registro');

            return $response;

        });
    }
}
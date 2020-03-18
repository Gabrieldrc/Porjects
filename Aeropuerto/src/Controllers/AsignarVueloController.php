<?php

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AsignarVueloController implements \Src\Interfaces\Controller {
    public function config($app){
        $app->get('/aviones/asignarVuelo/{error}', function (Request $request, Response $response, $args) {
            $template = $request->getAttribute('twig')->load('formularioAsignarAvionAVuelo.html');
            $response->getBody()->write(
                $template->render([
                    'Titulo' => 'Gabriel Airlines',
                    'Error' => ($args['error'] == 'error') ? true : false,
                ])
            );

            return $response;

        });

        $app->post('/asignarVuelo', function (Request $request, Response $response, $args) {
            $lista = $request->getParsedBody();
            $avion = $request->getAttribute('vuelosService')->asignarVuelo($lista['avionId'], $lista['idVuelo']);
            $result = $request->getAttribute('registroDeVuelosServicio')->asignarAvionEnRegistro($avion);
            if (! $result) {
                $response = $response->withStatus(302);
                $response = $response->withHeader('Location', '/aviones/asignarVuelo/error');

                return $response;

            }
            $response = $response->withStatus(302);
            $response = $response->withHeader('Location', '/aviones');

            return $response;

        });
    }
}
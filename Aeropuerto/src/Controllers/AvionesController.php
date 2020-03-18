<?php

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AvionesController implements \Src\Interfaces\Controller {
    public function config($app){
        $app->get('/aviones', function (Request $request, Response $response, $args) {
            $template = $request->getAttribute('twig')->load('aviones.html');
            $response->getBody()->write(
                $template->render([
                    'Titulo' => 'Gabriel Airlines',
                    'listaVuelos' => $request->getAttribute('vuelosService')->buscarAviones(),
                ])
            );

            return $response;

        });
    }
}
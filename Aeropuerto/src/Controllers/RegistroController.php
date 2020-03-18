<?php

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegistroController implements \Src\Interfaces\Controller {
    public function config($app){
        $app->get('/registro', function (Request $request, Response $response, $args) {
            $template = $request->getAttribute('twig')->load('registro.html');
            $response->getBody()->write(
                $template->render([
                    'Titulo' => 'Gabriel Airlines',
                    'listaVuelos' => $request->getAttribute('registroDeVuelosServicio')->mostrarVuelos(),
                    ])
            );

            return $response;

        });
    }
}
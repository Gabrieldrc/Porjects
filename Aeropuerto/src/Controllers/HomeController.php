<?php

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController implements \Src\Interfaces\Controller {
    public function config($app){
        $app->get('/home', function (Request $request, Response $response, $args) {
            $template = $request->getAttribute('twig')->load('index.html');
            $response->getBody()->write(
                $template->render(['Titulo' => 'Gabriel Airlines'])
            );

            return $response;

        });
    }
}
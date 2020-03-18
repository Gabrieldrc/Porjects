<?php

namespace Src\Servicios;

class ControllerServicio {

    public function setup($app, $controllerFolder) {
        $files = array();

        $dh = opendir($controllerFolder);
        while (false !== ($entry = readdir($dh))) {
            if (strlen($entry) > 4) {
                $files[] = $entry;
            }
        }

        foreach ($files as $filename) {
            $info = explode('.', $filename);
            $filename = '\Src\Controllers\\'.$info[0];
            
            $controller = new $filename;
            $controller->config($app);
        }

    }

}
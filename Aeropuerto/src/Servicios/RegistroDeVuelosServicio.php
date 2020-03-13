<?php

namespace Src\Servicios;

use \Src\Modelos;

class RegistroDeVuelosServicio
{
    private $collection;
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function registrarVuelo(
        String $origen,
        String $destino
    ) {
        if ($this->verificarSiElVueloYaFueRegistrado($origen, $destino)) {

            return False;

        }
        $vuelo =
        [
            'idVuelo' => md5(microtime()),
            'origen' => $origen,
            'destino' => $destino,
            'done' => false,
            'avionAsignado' => 'No Asignado',
        ];
        $insertOneResult = $this->collection->insertOne($vuelo);

        if ($insertOneResult->getInsertedCount() > 0) {

            return True;

        }

        return False;

    }

    public function verificarSiElVueloYaFueRegistrado(
        String $origen,
        String $destino
    ) {
        $vuelo = $this->collection->findOne(
            [
                'origen' => $origen,
                'destino' => $destino,
                'done' => false,
                'avionAsignado' => 'No Asignado',
            ]
        );
        if (is_null($vuelo)) {

            return false;

        } else {

            return true;

        }
    }

    public function mostrarVuelos(String $origen = null, String $destino = null)
    {
        if (is_null($origen)) {
            $datos = $this->collection->find([]);
        } elseif (is_null($destino)) {
            $datos = $this->collection->find(
                [
                    'origen' => $origen,
                ]
            );
        } else {
            $datos = $this->collection->find(
                [
                    'origen' => $origen,
                    'destino' => $destino,
                ]
            );
        }
        $vuelos = [];
        foreach ($datos as $dato) {
            $vuelo =
            [
                'idVuelo' => $dato['idVuelo'],
                'origen' => $dato['origen'],
                'destino' => $dato['destino'],
                'done' => $dato['done'],
                'avionAsignado' => $dato['avionAsignado'],
            ];
            $vuelos[] = $vuelo;
        }
    
        return $vuelos;

    }

    public function asignarAvionEnRegistro(\Src\Modelos\Avion $avion)
    {
        if (! is_subclass_of($avion, '\Src\Modelos\Avion')) {
            $updateResult = $this->collection->updateOne(
                ['idVuelo' => $avion->getIdVuelo()],
                ['$set' => ['avionAsignado' => $avion->getAvionId()]]
            );
            if ($updateResult->getModifiedCount() == 1) {

                return true;

            }
        }

        return false;

    }

    public function culminarVuelo(Modelos\Avion $avion)
    {
        if (! is_subclass_of($avion, '\Src\Modelos\Avion')) {
            $updateResult = $this->collection->updateOne(
                ['avionAsignado' => $avion->getAvionId(), 'done' => false],
                ['$set' => ['done' => true]]
            );
            if ($updateResult->getModifiedCount() == 1) {
                return true;
            }
        }
        return False;
        
    }
}

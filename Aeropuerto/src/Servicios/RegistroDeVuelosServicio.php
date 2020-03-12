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
            return 0;
        } else {
            $vuelo =
            [
                'idVuelo' => md5(microtime()),
                'origen' => $origen,
                'destino' => $destino,
                'done' => false,
                'avionAsignado' => 'No Asignado',
            ];
            $insertOneResult = $this->collection->insertOne($vuelo);

            return $insertOneResult->getInsertedCount();
        }
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
    public function mostrarVuelos(String $origen, $destino = null)
    {
        if (is_null($destino)) {
            $datos = $this->collection->find(
                [
                    'origen' => $origen,
                    'done' => false,
                ]
            );
        } else {
            $datos = $this->collection->find(
                [
                    'origen' => $origen,
                    'destino' => $destino,
                    'done' => false,
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

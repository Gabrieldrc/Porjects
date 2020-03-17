<?php

namespace Src\Servicios;

use Src\Modelos\Avion;
use Src\Modelos\AvionFalse;

class VuelosServicio
{
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function habilitarNuevoAvion(int $puestos, string $ubicacion)
    {
        $insertOneResult = $this->collection->insertOne([
           'avionId' => md5(microtime()),
           'puestos' => $puestos,
           'ubicacion' => $ubicacion,
           'idVuelo' => 'No Asignado',
           'destino' => 'No Asignado',
       ]);

        return $insertOneResult->getInsertedCount();

    }

    // public function buscarAvionSinVuelo($ubicacion)
    // {
    //     $avionData = $this->collection->findOne([
    //        'ubicacion' => $ubicacion,
    //        'idVuelo' => 'No Asignado',
    //    ]);
    //     if (! is_null($avionData)) {
    //         $avion = new Avion(
    //             $avionData['avionId'],
    //             $avionData['puestos'],
    //             $avionData['ubicacion'],
    //             $avionData['idVuelo'],
    //             $avionData['destino']
    //         );

    //         return $avion;

    //     }

    //     return new AvionFalse();

    // }

    public function buscarAvionPorIdAvion($avionId)
    {
        $avionData = $this->collection->findOne([
           'avionId' => $avionId,
       ]);
        if (! is_null($avionData)) {
            $avion = new Avion(
                $avionData['avionId'],
                $avionData['puestos'],
                $avionData['ubicacion'],
                $avionData['idVuelo'],
                $avionData['destino']
            );

            return $avion;

        }

        return new AvionFalse();

    }

    public function buscarAviones()
    {
        $avionesData = $this->collection->find([]);
        $listaAviones = [];
        if (is_null($avionesData)) {

            return $listaAviones;

        }
        $listaAviones = [];
        foreach ($avionesData as $avionData) {
            $avion = [
                'avionId' => $avionData['avionId'],
                'puestos' => $avionData['puestos'],
                'ubicacion' => $avionData['ubicacion'],
                'idVuelo' => $avionData['idVuelo'],
                'destino' => $avionData['destino'],
            ];
            $listaAviones [] = $avion;
        }

        return $listaAviones;

    }

    public function asignarVuelo($avionId, $idVuelo)
    {
        $avion = $this->buscarAvionPorIdAvion($avionId);
        if ($avion instanceof Avion) {
            $avion->asignarIdVuelo($idVuelo);
            $updateResult = $this->collection->updateOne(
                ['avionId' => $avion->getAvionId(), 'ubicacion' => $avion->get],
                [
                    '$set' => ['idVuelo' => $idVuelo, 'destino' => $destino],
                ]
            );
            if($updateResult->getModifiedCount() >0){
                return $avion;
            } else {

                return new AvionFalse();

            }
        } else {

            return new AvionFalse();

        }
    }

    public function realizarVuelo($idVuelo)
    {
        $avion = $this->buscarAvionPorVueloId($idVuelo);
        $avion->realizarVuelo();
        if (! ( is_subclass_of($avion, '\Src\Modelos\Avion')) && $avion instanceof Avion) {
            $updateResult = $this->collection->updateOne(
                ['avionId' => $avion->getAvionId()],
                ['$set' =>
                    [
                    'ubicacion' => $avion->getUbicacion(),
                    'idVuelo' => $avion->getIdVuelo(),
                    'destino' => $avion->getDestino(),
                    ]
                ]
            );

            return $avion;

        } else {

            return new AvionFalse();

        }
    }
}

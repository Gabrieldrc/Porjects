<?php

namespace Src\Servicios;

use Src\Modelos\Avion;
use Src\Modelos\AvionFalse;
use Src\Servicios\RegistroDeVuelosServicio;

class VuelosServicio
{
    public function __construct($collection, RegistroDeVuelosServicio $rServicio)
    {
        $this->collection = $collection;
        $this->rServicio = $rServicio;
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
        $vuelo = $this->rServicio->mostrarVuelosIdVuelo($idVuelo);
        if ($avion instanceof Avion
        && ! is_subclass_of($avion, '\Src\Modelos\Avion')
        && ! is_null($vuelo)) {
            // var_dump($vuelo);die();
            $avion->asignarIdVuelo($idVuelo);
            $updateResult = $this->collection->updateOne(
                ['avionId' => $avion->getAvionId(), 'ubicacion' => $avion->getUbicacion()],
                [
                    '$set' => ['idVuelo' => $idVuelo, 'destino' => $vuelo['destino']],
                ]
            );
            if($updateResult->getModifiedCount() >0){
                return true;
            } else {

                return false;

            }
        } else {

            return false;

        }
    }

    public function realizarVuelo($avionId)
    {
        $avion = $this->buscarAvionPorIdAvion($avionId);
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

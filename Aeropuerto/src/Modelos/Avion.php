<?php

namespace Src\Modelos;

class Avion
{
    protected $avionId;
    protected $puestos;
    protected $puestosDisponibles;
    protected $idVuelo;
    protected $ubicacion;
    protected $destino;

    public function __construct($avionId, int $puestos, string $ubicacion, $idVuelo, String $destino)
    {
        $this->avionId = $avionId;
        $this->puestos = $puestos;
        $this->puestosDisponibles = $puestos;
        $this->ubicacion = $ubicacion;
        $this->idVuelo = $idVuelo;
        $this->destino = $destino;
    }

    public function getPuestosDisponibles(): int
    {
        return $this->puestosDisponibles;
    }

    public function getAvionId()
    {
        return $this->avionId;
    }

    public function asignarIdVuelo($idVuelo): Bool
    {
        $this->idVuelo = $idVuelo;
        return true;
    }

    public function getUbicacion(): string
    {
        return $this->ubicacion;
    }

    public function getDestino(): string
    {
        return $this->destino;
    }

    public function getIdVuelo()
    {
        return $this->idVuelo;
    }

    public function realizarVuelo()
    {
        if ($this->getIdVuelo() != 'No Asignado') {
            $this->puestosDisponibles = $this->puestos;
            $this->ubicacion = $this->destino;
            $this->idVuelo = 'No Asignado';
            $this->destino = 'No Asignado';

            return true;

        } else {

            return false;

        }
    }
}

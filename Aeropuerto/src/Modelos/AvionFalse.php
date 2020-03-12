<?php

namespace Src\Modelos;

class AvionFalse extends Avion
{
    public function __construct()
    {
    }

    public function getPuestosDisponibles(): int
    {
        return 0;
    }

    public function getAvionId()
    {
        return 0;
    }

    public function asignarIdVuelo($idVuelo): Bool
    {
        return false;
    }

    public function getUbicacion(): string
    {
        return '';
    }

    public function getIdVuelo()
    {
        return '';
    }

    public function realizarVuelo()
    {
        return false;
    }
}

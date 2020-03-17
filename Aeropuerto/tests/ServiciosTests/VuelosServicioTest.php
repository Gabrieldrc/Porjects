<?php

namespace Tests\ServiciosTests;

use Src\Modelos\Avion;
use \Src\Servicios\VuelosServicio;
use \Src\Servicios\RegistroDeVuelosServicio;

final class VuelosServicioTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() :void
    {
        $conn = new \MongoDB\Client("mongodb://localhost");
        $this->VuelosCollection = $conn->testVuelosServicio->aviones;
        $this->RegistroCollection = $conn->testRegistroDeVuelo->registros;
        $this->rService = new RegistroDeVuelosServicio($this->RegistroCollection);
        $this->VuelosCollection->drop();
        $this->vService = new VuelosServicio($this->VuelosCollection, $this->rService);
    }

    public function testHabilitarNuevoAvion()
    {
        $return = $this->vService->habilitarNuevoAvion(100,'CABA');
        $vuelo = $this->VuelosCollection->findOne(['idVuelo' => 'No Asignado', 'puestos' => 100]); 
        $this->assertEquals(1, $return);
        $this->assertFalse(is_null($vuelo));
    }

    public function testBuscarAvionesLista()
    {
        $result = $this->vService->buscarAviones();
        $this->assertEquals(0, count($result));
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $this->vService->habilitarNuevoAvion(100,'CABE');
        $this->vService->habilitarNuevoAvion(100,'CABI');
        $result = $this->vService->buscarAviones();
        $this->assertEquals(3, count($result));
        $this->assertEquals('CABA', $result[0]['ubicacion']);
        $this->assertEquals('CABE', $result[1]['ubicacion']);
        $this->assertEquals('CABI', $result[2]['ubicacion']);
    }

    public function testBuscarAvionesPorId()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $this->vService->habilitarNuevoAvion(100,'CABE');
        $this->vService->habilitarNuevoAvion(100,'CABI');
        $result = $this->vService->buscarAviones();
        $vueloUno = $this->vService->buscarAvionPorIdAvion($result[0]['avionId']);
        $vueloDos = $this->vService->buscarAvionPorIdAvion($result[1]['avionId']);
        $vueloTres = $this->vService->buscarAvionPorIdAvion($result[2]['avionId']);
        $this->assertEquals($vueloUno->getAvionId(), $result[0]['avionId']);
        $this->assertEquals($vueloDos->getAvionId(), $result[1]['avionId']);
        $this->assertEquals($vueloTres->getAvionId(), $result[2]['avionId']);
        $this->assertTrue( is_subclass_of(
            $this->vService->buscarAvionPorIdAvion('qefw2f'),
            '\Src\Modelos\Avion')
        );
    }

    public function testAsignarVueloFalse()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $this->vService->habilitarNuevoAvion(100,'CABE');
        $result = $this->vService->buscarAviones();
        $result = $this->vService->asignarVuelo($result[0]['avionId'], 'blabla');
        $this->assertFalse($result);
    }

    public function testAsignarVueloTrue()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $this->vService->habilitarNuevoAvion(100,'CABE');
        $this->rService->registrarVuelo('CABA','VACA');
        $result = $this->vService->buscarAviones();
        $vuelos = $this->rService->mostrarVuelos('CABA');
        $result = $this->vService->asignarVuelo($result[0]['avionId'], $vuelos[0]['idVuelo']);
        $this->assertTrue($result);
    }

    // public function testRealizarVueloFalse()

}

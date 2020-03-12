<?php

namespace Tests\ServiciosTests;

use Src\Modelos\Avion;
use \Src\Servicios\VuelosServicio;

final class VuelosServicioTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() :void
    {
        $conn = new \MongoDB\Client("mongodb://localhost");
        $this->VuelosCollection = $conn->testVuelosServicio->aviones;
        $this->VuelosCollection->drop();
        $this->vService = new VuelosServicio($this->VuelosCollection);
    }

    public function testHabilitarNuevoAvion()
    {
        $return = $this->vService->habilitarNuevoAvion(100,'CABA');
        $vuelo = $this->VuelosCollection->findOne(['idVuelo' => 'No Asignado', 'puestos' => 100]); 
        $this->assertEquals(1, $return);
        $this->assertFalse(is_null($vuelo));
    }

    public function testBuscarAvion()
    {
        $return = $this->vService->habilitarNuevoAvion(100,'CABA');
        $avion = $this->vService->buscarAvionSinVuelo('CABA');
        $this->assertTrue($avion instanceof \Src\Modelos\Avion);
    }

    public function testNoSeEncuentraAvion()
    {
        $return = $this->vService->habilitarNuevoAvion(100,'CABA');
        $avion = $this->vService->buscarAvionSinVuelo('Blabla');
        $this->assertTrue($avion instanceof \Src\Modelos\AvionFalse);
    }

    public function testAsignarVuelo()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $result = $this->vService->asignarVuelo('a1b2c3d4','CABA', 'Madrid');
        $this->assertTrue(! is_subclass_of($result, '\Src\Modelos\Avion'));
        $this->assertTrue($result instanceof Avion);
    }

    public function testNoAsignarVuelo()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $result = $this->vService->asignarVuelo('a1b2c3d4','CABA', 'Madrid');
        $this->assertTrue(! is_subclass_of($result, '\Src\Modelos\Avion'));
        $this->assertTrue($result instanceof Avion);
        $result = $this->vService->asignarVuelo('a1b2c3d4','CABA', 'Madrid');
        $this->assertTrue(is_subclass_of($result, '\Src\Modelos\Avion'));
    }

    public function testBuscarAvionPorVueloId()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $this->vService->asignarVuelo('a1b2c3d4','CABA', 'Madrid');
        $avion = $this->vService->buscarAvionPorVueloId('a1b2c3d4');
        $this->assertFalse($avion instanceof \Src\Modelos\AvionFalse);
    }

    public function testNoSeEncuentraAvionPorVueloId()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $avion = $this->vService->buscarAvionPorVueloId('Blabla');
        $this->assertTrue($avion instanceof \Src\Modelos\AvionFalse);
    }

    public function testRealizarVueloFalse()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $this->vService->asignarVuelo('a1b2c3d4','CABA', 'Madrid');
        $avion = $this->vService->realizarVuelo('111111');
        $this->assertTrue($avion instanceof \Src\Modelos\AvionFalse);
    }
    
    public function testRealizarVuelo()
    {
        $this->vService->habilitarNuevoAvion(100,'CABA');
        $this->vService->asignarVuelo('a1b2c3d4','CABA', 'Madrid');
        $avion = $this->vService->realizarVuelo('a1b2c3d4');
        $this->assertFalse($avion instanceof \Src\Modelos\AvionFalse);
    }

}

<?php

namespace Tests\ServiciosTests;

use \Src\Servicios\RegistroDeVuelosServicio;
use \Src\Modelos;

final class RegistroDeVuelosTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() :void
    {
        $conn = new \MongoDB\Client("mongodb://localhost");
        $this->RegistroCollection = $conn->testRegistroDeVuelo->registros;
        $this->RegistroCollection->drop();
        $this->rService = new RegistroDeVuelosServicio($this->RegistroCollection);
    }

    public function testRegistrarVuelo()
    {
        $return = $this->rService->registrarVuelo('CABA', 'Madrid');
        $vuelo = $this->RegistroCollection->findOne(['origen' => 'CABA']);
        $this->assertTrue($return);
        $this->assertFalse(is_null($vuelo));
    }

    public function testNoRegistrarElMismoVueloMasDeUnaVez()
    {
        $this->rService->registrarVuelo('CABA', 'Madrid');
        $return = $this->rService->registrarVuelo('CABA', 'Madrid');
        $this->assertFalse($return);
    }

    public function testMostrarVuelosSinDestino()
    {
        $this->rService->registrarVuelo('CABA, Argentina', 'Madrid, España');
        $this->rService->registrarVuelo('CABA, Argentina', 'Los Angeles, Estados Unidos');
        $this->rService->registrarVuelo('CABA, Argentina', 'Estocolmo, Suecia');
        $vuelosSinDestino = $this->rService->mostrarVuelos('CABA, Argentina');
        $this->assertEquals(3, count($vuelosSinDestino));
        $this->assertEquals('Madrid, España', $vuelosSinDestino[0]['destino']);
        $this->assertEquals('Los Angeles, Estados Unidos', $vuelosSinDestino[1]['destino']);
        $this->assertEquals('Estocolmo, Suecia', $vuelosSinDestino[2]['destino']);
    }

    public function testMostrarVuelosConDestino()
    {
        $this->rService->registrarVuelo('CABA, Argentina', 'Madrid, España');
        $this->rService->registrarVuelo('CABA, Argentina', 'Los Angeles, Estados Unidos');
        $this->rService->registrarVuelo('CABA, Argentina', 'Estocolmo, Suecia');
        $vuelosConDestino = $this->rService->mostrarVuelos('CABA, Argentina', 'Madrid, España');
        $this->assertEquals(1, count($vuelosConDestino));
        $this->assertEquals('Madrid, España', $vuelosConDestino[0]['destino']);
    }

    public function testAsignarAvionEnRegistro()
    {
        $this->rService->registrarVuelo('CABA, Argentina', 'Madrid, España');
        $vuelos = $this->rService->mostrarVuelos('CABA, Argentina');
        $result = $this->rService->asignarAvionEnRegistro(
            new Modelos\Avion('asd', 100,'CABA, Argentina', $vuelos[0]['idVuelo'], 'ds'));
        $this->assertTrue($result);
    }

    public function testAsignarAvionEnRegistroFalse()
    {
        $this->rService->registrarVuelo('CABA, Argentina', 'Madrid, España');
        $result = $this->rService->asignarAvionEnRegistro(new Modelos\AvionFalse());
        $this->assertFalse($result);
    }

    public function testCulminarVuelo()
    {
        $this->rService->registrarVuelo('CABA, Argentina', 'Madrid, España');
        $vuelos = $this->rService->mostrarVuelos('CABA, Argentina');
        $this->rService->asignarAvionEnRegistro(
            new Modelos\Avion('asd', 100,'CABA, Argentina', $vuelos[0]['idVuelo'], 'ds'));
        $result = $this->rService->culminarVuelo(
            new Modelos\Avion('asd', 100,'CABA, Argentina', $vuelos[0]['idVuelo'], 'ds'));
        $this->assertTrue($result);
        $result = $this->rService->culminarVuelo(
            new Modelos\Avion('asd', 100,'CABA, Argentina', $vuelos[0]['idVuelo'], 'ds'));
        $this->assertFalse($result);
    }
}

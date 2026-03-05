<?php

namespace FacturaScripts\Plugins\FsResidentes\Model;

use FacturaScripts\Core\Template\ModelClass;
use FacturaScripts\Core\Template\ModelTrait;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Session;

class ResidenteVehiculo extends ModelClass
{
    use ModelTrait;

    /**
     * El Id del vehiculo
     * @var integer serial
     */
    public $idvehiculo;
    /**
     * El codigo del Residente o cliente al que pertenece el vehiculo
     * @var string
     */
    public $codcliente;
    /**
     * La marca del Vehiculo
     * @var string
     */
    public $vehiculo_marca;
    /**
     * El modelo del Vehiculo
     * @var string
     */
    public $vehiculo_modelo;
    /**
     * El color del Vehiculo
     * @var string
     */
    public $vehiculo_color;
    /**
     * La placa del Vehiculo
     * @var string
     */
    public $vehiculo_placa;
    /**
     * El tipo de Vehiculo
     * @var string
     */
    public $vehiculo_tipo;
    /**
     * Si posee una tarjeta de acceso asignada al vehiculo
     * este código se guarda aquí
     * @var string
     */
    public $codigo_tarjeta;
    public $cliente;

    public function clear(): void 
    {
        parent::clear();
    }

    public static function primaryColumn(): string
    {
        return 'idvehiculo';
    }

    public static function tableName(): string
    {
        return 'residentes_vehiculos';
    }

    public function test(): bool
    {

        return parent::test();
    }
}

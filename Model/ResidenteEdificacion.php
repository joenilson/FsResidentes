<?php

namespace FacturaScripts\Plugins\FsResidentes\Model;

use FacturaScripts\Core\Template\ModelClass;
use FacturaScripts\Core\Template\ModelTrait;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Session;
use FacturaScripts\Dinamic\Model\Cliente;

class ResidenteEdificacion extends ModelClass
{
    use ModelTrait;

    /** @var string */
    public $codcliente;

    /** @var string */
    public $codigo;

    /** @var string */
    public $codigo_interno;

    /** @var string */
    public $coordenadas;

    /** @var string */
    public $fecha_disponibilidad;

    /** @var string */
    public $fecha_ocupacion;

    /** @var int */
    public $id;

    /** @var int */
    public $id_edificacion;

    /** @var int */
    public $iddireccion;

    /** @var string */
    public $numero;

    /** @var int */
    public $ocupado;

    /** @var string */
    public $ubicacion;

    public function clear(): void 
    {
        parent::clear();
        $this->fecha_disponibilidad = Tools::date();
        $this->fecha_ocupacion = Tools::date();
        $this->id_edificacion = 0;
        $this->iddireccion = 0;
        $this->ocupado = 0;
    }

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public static function tableName(): string
    {
        return 'residentes_edificaciones';
    }

    public function getResidente(): Cliente|null
    {
        $cliente = Cliente::findWhereEq('codcliente', $this->codcliente);
        return $cliente ?: null;
    }

    /**
     * @throws \JsonException
     */
    public function codigoExterno(): string
    {
        $piezas = \json_decode($this->codigo_interno, true, 512, JSON_THROW_ON_ERROR);
        $codigo_externo = '';
        foreach ($piezas as $id => $data) {
            $codigo_externo .= ResidenteTipoEdificacion::find($id)->descripcion.' '.$data.' ';
        }
        return $codigo_externo;
    }

    public function info()
    {
        if ($this->codcliente) {
            $cliente = Cliente::find($this->codcliente);
            $this->nombre = $cliente->nombre;
            $this->telefono = $cliente->telefono1;
            $this->email = $cliente->email;
            $this->info = ResidenteInformacion::findWhere(['codcliente' => $this->codcliente],['id'=>'ASC']);
            $this->vehiculos = ResidenteVehiculo::findWhere(['codcliente' => $this->codcliente],['idvehiculo'=>'ASC']);
        }
    }

    public function test(): bool
    {
        $this->codcliente = Tools::noHtml($this->codcliente);
        $this->codigo = Tools::noHtml($this->codigo);
        $this->codigo_interno = Tools::fixHtml($this->codigo_interno);
        $this->coordenadas = Tools::noHtml($this->coordenadas);
        $this->numero = Tools::noHtml($this->numero);
        $this->ubicacion = Tools::noHtml($this->ubicacion);

        return parent::test();
    }
}

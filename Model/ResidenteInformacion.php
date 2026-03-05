<?php

namespace FacturaScripts\Plugins\FsResidentes\Model;

use FacturaScripts\Core\Template\ModelClass;
use FacturaScripts\Core\Template\ModelTrait;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Session;

class ResidenteInformacion extends ModelClass
{
    use ModelTrait;

    /** @var string */
    public $ca_apellidos;

    /** @var string */
    public $ca_email;

    /** @var string */
    public $ca_nombres;

    /** @var string */
    public $ca_telefono;

    /** @var string */
    public $ca_parentesco;

    /** @var string */
    public $ca_parentesco_obs;

    /** @var bool */
    public $ca_propietario;

    /** @var string */
    public $codcliente;

    /** @var string */
    public $codigo;

    /** @var string */
    public $informacion_discapacidad;

    /** @var int */
    public $id;

    /** @var string */
    public $ocupacion;

    /** @var int */
    public $ocupantes;

    /** @var int */
    public $ocupantes12anos;

    /** @var int */
    public $ocupantes18anos;

    /** @var int */
    public $ocupantes30anos;

    /** @var int */
    public $ocupantes50anos;

    /** @var int */
    public $ocupantes5anos;

    /** @var int */
    public $ocupantes70anos;

    /** @var int */
    public $ocupantes71anos;

    /** @var string */
    public $profesion;

    /** @var bool */
    public $propietario;

    /** @var int */
    public $vehiculos;

    public function clear(): void 
    {
        parent::clear();
        $this->ocupantes = 0;
        $this->ocupantes12anos = 0;
        $this->ocupantes18anos = 0;
        $this->ocupantes30anos = 0;
        $this->ocupantes50anos = 0;
        $this->ocupantes5anos = 0;
        $this->ocupantes70anos = 0;
        $this->ocupantes71anos = 0;
        $this->propietario = false;
        $this->ca_propietario = false;
        $this->vehiculos = 0;
    }

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public static function tableName(): string
    {
        return 'residentes_informacion';
    }

    public function test(): bool
    {
        $this->ca_apellidos = Tools::noHtml($this->ca_apellidos);
        $this->ca_email = Tools::noHtml($this->ca_email);
        $this->ca_telefono = Tools::noHtml($this->ca_telefono);
        $this->ca_nombres = Tools::noHtml($this->ca_nombres);
        $this->ca_parentesco = Tools::noHtml($this->ca_parentesco);
        $this->ca_parentesco_obs = Tools::noHtml($this->ca_parentesco_obs);
        $this->codcliente = Tools::noHtml($this->codcliente);
        $this->codigo = Tools::noHtml($this->codigo);
        $this->ocupacion = Tools::noHtml($this->ocupacion);
        $this->profesion = Tools::noHtml($this->profesion);
        $this->informacion_discapacidad = Tools::noHtml($this->informacion_discapacidad);

        return parent::test();
    }
}

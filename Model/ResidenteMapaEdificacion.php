<?php

namespace FacturaScripts\Plugins\FsResidentes\Model;

use FacturaScripts\Core\Template\ModelClass;
use FacturaScripts\Core\Template\ModelTrait;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Session;

class ResidenteMapaEdificacion extends ModelClass
{
    use ModelTrait;

    /** @var string */
    public $codigo_edificacion;

    /** @var string */
    public $codigo_padre;

    /** @var int */
    public $id;

    /** @var int */
    public $id_tipo;

    /** @var string */
    public $numero;

    /** @var int */
    public $padre_id;

    /** @var int */
    public $padre_tipo;

    public function clear(): void 
    {
        parent::clear();
        $this->id_tipo = 0;
        $this->padre_id = 0;
        $this->padre_tipo = 0;
    }

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public static function tableName(): string
    {
        return 'residentes_mapa_edificaciones';
    }

    public function test(): bool
    {
        $this->codigo_edificacion = Tools::noHtml($this->codigo_edificacion);
        $this->codigo_padre = Tools::noHtml($this->codigo_padre);
        $this->numero = Tools::noHtml($this->numero);

        return parent::test();
    }
}

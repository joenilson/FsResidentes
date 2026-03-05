<?php

namespace FacturaScripts\Plugins\FsResidentes\Model;

use FacturaScripts\Core\Template\ModelClass;
use FacturaScripts\Core\Template\ModelTrait;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Session;

class ResidenteTipoEdificacion extends ModelClass
{
    use ModelTrait;

    /** @var string */
    public $descripcion;

    /** @var int */
    public $id;

    /** @var int */
    public $padre;

    public function clear(): void 
    {
        parent::clear();
        $this->padre = 0;
    }

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public static function tableName(): string
    {
        return 'residentes_edificaciones_tipo';
    }

    public function test(): bool
    {
        $this->descripcion = Tools::noHtml($this->descripcion);

        return parent::test();
    }
}

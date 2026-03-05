<?php

namespace FacturaScripts\Plugins\FsResidentes\Model;

use FacturaScripts\Core\Template\ModelClass;
use FacturaScripts\Core\Template\ModelTrait;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Session;

class Residente extends ModelClass
{
    use ModelTrait;

    public function clear(): void 
    {
        parent::clear();
    }

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public static function tableName(): string
    {
        return 'residentes';
    }

    public function test(): bool
    {

        return parent::test();
    }
}

<?php

namespace FacturaScripts\Plugins\FsResidentes\Controller;

use FacturaScripts\Core\Lib\ExtendedController\EditController;

/**
 * Este es un controlador específico para ediciones. Permite una o varias pestañas.
 * Cada una con un xml y modelo diferente, puede ser de tipo edición, listado, html o panel.
 * Además, hace uso de archivos de XMLView para definir qué columnas mostrar y cómo.
 *
 * https://facturascripts.com/publicaciones/editcontroller-642
 */
class EditResidenteEdificacion extends EditController
{
    public function getModelClassName(): string
    {
        return 'ResidenteEdificacion';
    }

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['menu'] = 'Residentes';
        $data['title'] = 'edit-resident';
        $data['icon'] = 'fa-solid fa-person';
        return $data;
    }
}

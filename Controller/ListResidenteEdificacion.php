<?php

namespace FacturaScripts\Plugins\FsResidentes\Controller;

use FacturaScripts\Core\Lib\ExtendedController\ListController;

/**
 * Este es un controlador específico para listados. Permite una o varias pestañas.
 * Cada una con un listado de los registros de un modelo.
 * Además, hace uso de archivos de XMLView para definir qué columnas mostrar y cómo.
 *
 * https://facturascripts.com/publicaciones/listcontroller-232
 */
class ListResidenteEdificacion extends ListController
{
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['menu'] = 'Residentes';
        $data['title'] = 'list-residents';
        $data['icon'] = 'fa-solid fa-people-group';
        return $data;
    }

    protected function createViews(): void
    {
        $this->createViewsResidente();
    }

    protected function createViewsResidente(string $viewName = 'ListResidente'): void
    {
        // El listado base sale de ResidenteEdificacion y se enriquece en XMLView con values source=Cliente.
        $this->addView($viewName, 'ResidenteEdificacion', 'list-residents');
        $this->addOrderBy($viewName, ['id_edificacion', 'numero'], 'apartment', 1);
        $this->addOrderBy($viewName, ['fecha_ocupacion'], 'occupation-date', 2);

        $this->addSearchFields($viewName, ['codcliente', 'codigo', 'numero', 'ubicacion']);
    }
}

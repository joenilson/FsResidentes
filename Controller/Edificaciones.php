<?php

namespace FacturaScripts\Plugins\FsResidentes\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Html;
use FacturaScripts\Core\Lib\AssetManager;
use FacturaScripts\Core\Where;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Model\Cliente;
use FacturaScripts\Core\Model\Contacto;
use FacturaScripts\Plugins\FsResidentes\Model\ResidenteEdificacion;
use FacturaScripts\Plugins\FsResidentes\Model\ResidenteMapaEdificacion;

class Edificaciones extends Controller
{
    public $mapaEdificaciones;
    public $listaEdificaciones;
    public $idEdificacion;
    public $inmuebles;
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['menu'] = 'Residentes';
        $data['title'] = 'list-buildings';
        $data['icon'] = 'fa-solid fa-building';
        return $data;
    }

    /**
     * @throws \JsonException
     */
    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $route = Tools::config('route');
        AssetManager::addJs($route . '/Plugins/FsResidentes/Assets/JS/CommonModals.js');
        AssetManager::addCss($route . '/node_modules/select2/dist/css/select2.min.css?v=5');
        AssetManager::addCss($route . '/node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css?v=5');
        AssetManager::addJs($route . '/node_modules/select2/dist/js/select2.min.js?v=5', 2);
        $action = $this->request()->request->get('action', '');
        $action = (!$action) ? $this->request()->query('action', '') : $action;

        // AJAX: obtener inmuebles de un edificio (lista de puertas)
        if ($action === 'obtener-inmuebles') {
            $this->idEdificacion = (int)$this->request()->query('id_edificio', 0);
            $this->inmuebles = ResidenteEdificacion::all(
                [Where::eq('id_edificacion', $this->idEdificacion)],
                ['numero' => 'ASC']
            );
            $this->setTemplate(false);
            $response->json([
                'html' => Html::render('Twig/Templates/InmueblesWidget.html.twig', ['fsc' => $this]),
            ]);
            return;
        }

        // AJAX: cargar lista de edificaciones hijas para el panel derecho
        if ($action === 'cargar-inmuebles') {
            $this->idEdificacion = (int)$this->request()->query('id', 0);
            $this->inmuebles = ResidenteMapaEdificacion::all(
                [Where::eq('padre_id', $this->idEdificacion)],
                ['codigo_edificacion' => 'ASC']
            );
            $this->setTemplate(false);
            $response->json([
                'html' => Html::render('Twig/Templates/EdificacionesWidget.html.twig', ['fsc' => $this]),
            ]);
            return;
        }

        // AJAX: buscar clientes para select2
        if ($action === 'buscar-clientes') {
            $term = $this->request()->query('term', '');
            $where = [Where::like('upper(nombre)', '%' . \strtoupper($term) . '%')];
            $clientes = Cliente::all($where, ['upper(nombre)' => 'ASC'], 0, 20);

            $this->setTemplate(false);
            $results = [];
            foreach ($clientes as $cliente) {
                if (ResidenteEdificacion::findWhereEq('codcliente', $cliente->codcliente) === null) {
                    $results[] = [
                        'codcliente' => $cliente->codcliente,
                        'nombre' => $cliente->nombre
                    ];
                }
            }
            $response->json($results);
            return;
        }

        // AJAX: asignar residente a un inmueble
        if ($action === 'asignar-residente') {
            $idInmueble = (int)$this->request()->request->get('id_inmueble', 0);
            $codcliente = (string)$this->request()->request->get('codcliente', '');
            $direccion = (string)$this->request()->request->get('direccion', '');
            $fecha_ocupacion = (string)$this->request()->request->get('fecha_ocupacion', '');
            $fecha_disponibilidad = (string)$this->request()->request->get('fecha_disponibilidad', '');

            $inmueble = ResidenteEdificacion::find($idInmueble);
            if (!$inmueble) {
                $this->setTemplate(false);
                $response->json(['ok' => false, 'msg' => 'Inmueble no encontrado']);
                return;
            }

            $cliente = Cliente::find($codcliente);
            if ($cliente) {
                $address = $cliente->getDefaultAddress();
                $address->direccion = Tools::noHtml($direccion);
                if ($address->save()) {
                    $inmueble->iddireccion = $address->idcontacto;
                }
            }

            $inmueble->codcliente = Tools::noHtml($codcliente);
            $inmueble->ocupado = empty($codcliente) ? 0 : 1;
            $inmueble->fecha_ocupacion = empty($fecha_ocupacion) ? null : Tools::noHtml($fecha_ocupacion);
            $inmueble->fecha_disponibilidad = empty($fecha_disponibilidad) ? null : Tools::noHtml($fecha_disponibilidad);
            $ok = $inmueble->save();

            $this->idEdificacion = $inmueble->id_edificacion;
            $this->inmuebles = ResidenteEdificacion::all(
                [Where::eq('id_edificacion', $this->idEdificacion)],
                ['numero' => 'ASC']
            );
            $this->setTemplate(false);
            $response->json([
                'ok' => $ok,
                'idEdificio' => $this->idEdificacion,
                'html' => Html::render('Twig/Templates/InmueblesWidget.html.twig', ['fsc' => $this])
            ]);
            return;
        }

        // AJAX: quitar residente de un inmueble
        if ($action === 'quitar-residente') {
            $idInmueble = (int)$this->request()->request->get('id_inmueble', 0);
            $inmueble = ResidenteEdificacion::find($idInmueble);
            if (!$inmueble) {
                $this->setTemplate(false);
                $response->json(['ok' => false, 'msg' => 'Inmueble no encontrado']);
                return;
            }
            $inmueble->codcliente = null;
            $inmueble->fecha_ocupacion = null;
            $inmueble->fecha_disponibilidad = null;
            $inmueble->iddireccion = null;
            $inmueble->ocupado = 0;
            $ok = $inmueble->save();

            $this->idEdificacion = $inmueble->id_edificacion;
            $this->inmuebles = ResidenteEdificacion::all(
                [Where::eq('id_edificacion', $this->idEdificacion)],
                ['numero' => 'ASC']
            );
            $this->setTemplate(false);
            $response->json([
                'ok' => $ok,
                'idEdificio' => $this->idEdificacion,
                'html' => Html::render('Twig/Templates/InmueblesWidget.html.twig', ['fsc' => $this])
            ]);
            return;
        }

        // Carga normal de página
        $this->mapaEdificaciones = ResidenteMapaEdificacion::all([Where::eq('padre_id', 0)], ['codigo_edificacion' => 'ASC']);
        $this->idEdificacion = $this->request()->request->get('id', '');
        $this->inmuebles = [];

        if (!empty($this->idEdificacion)) {
            $this->inmuebles = ResidenteMapaEdificacion::all(
                [Where::eq('padre_id', $this->idEdificacion)],
                ['codigo_edificacion' => 'ASC']
            );
        }
        $this->setTemplate('Edificaciones');
    }
}

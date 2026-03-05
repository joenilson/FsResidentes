$(document).ready(function() {
    function cargarInmuebles(idEdificio, $triggerBtn = null, forzar = false) {
        const $contenedor = $('.container-inmuebles-' + idEdificio);
        const yaCargado = $triggerBtn ? $triggerBtn.data('loaded') : false;

        if (!forzar && yaCargado === true) {
            return; // usar cache simple
        }

        // Mostrar spinner básico durante la carga
        $contenedor.html('<div class="text-center py-3 text-muted">\n            <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>\n            <p class="small mt-2">Cargando inmuebles...</p>\n        </div>');

        $.ajax({
            url: 'Edificaciones',
            method: 'GET',
            dataType: 'json',
            data: { action: 'obtener-inmuebles', id_edificio: idEdificio },
            success: function(response) {
                if (response && response.html) {
                    $contenedor.html(response.html).hide().fadeIn('fast');
                } else {
                    $contenedor.html('<div class="text-danger text-center"><i class="fa-solid fa-triangle-exclamation"></i> Respuesta inválida.</div>');
                }
                if ($triggerBtn) {
                    $triggerBtn.data('loaded', true);
                }
            },
            error: function() {
                $contenedor.html('<div class="text-danger text-center"><i class="fa-solid fa-triangle-exclamation"></i> Error al cargar.</div>');
            }
        });
    }

    // Toggle para abrir/cargar la lista de inmuebles (primera carga con cache)
    $(document).on('click', '.btn-toggle-inmuebles', function() {
        const $btn = $(this);
        const idEdificio = $btn.data('id-edificio');
        const target = '#collapseInmuebles-' + idEdificio;
        const $chevron = $btn.find('.fa-chevron-down');
        if ($chevron.length) {
            $chevron.toggleClass('fa-rotate-180');
        }
        // Si el colapsable se está abriendo por primera vez, cargar
        const estaAbierto = $(target).hasClass('show');
        if (!estaAbierto) {
            // Bootstrap abrirá el colapsable; cargamos contenido si no estaba cargado
            cargarInmuebles(idEdificio, $btn, false);
        }
    });

    // Botón de refresco que fuerza recarga sin colapsar
    $(document).on('click', '.btn-refresh-inmuebles', function() {
        const idEdificio = $(this).data('id-edificio');
        const $toggleBtn = $('.btn-toggle-inmuebles[data-id-edificio="' + idEdificio + '"]');
        // Marcar como no cargado para invalidar cache
        $toggleBtn.data('loaded', false);
        // Asegurar que el collapse esté visible
        const target = '#collapseInmuebles-' + idEdificio;
        const $collapse = $(target);
        if (!$collapse.hasClass('show')) {
            $collapse.collapse('show');
        }
        cargarInmuebles(idEdificio, $toggleBtn, true);
    });

    // Intercepta el click en el menú de edificaciones para cargar vía AJAX el widget principal
    $(document).on('click', '.item-edificacion', function(e) {
        e.preventDefault();
        const $enlace = $(this);
        const url = $enlace.attr('href');
        const id = (new URL(url, window.location.origin)).searchParams.get('id');
        if (!id) return;

        $.ajax({
            url: 'Edificaciones',
            method: 'GET',
            dataType: 'json',
            data: { action: 'cargar-inmuebles', id: id },
            success: function(response) {
                if (response && response.html) {
                    $('#edificaciones-detalle').html(response.html);
                    // Actualiza activo en listado
                    $('.item-edificacion').removeClass('active');
                    $enlace.addClass('active');
                } else {
                    executeModal('avisoEdif', 'Aviso', 'No se pudo cargar la edificación seleccionada.', 'warning');
                }
            },
            error: function() {
                executeModal('errorEdif', 'Error', 'Error al cargar la edificación seleccionada.', 'warning');
            }
        });
    });

    // Quitar residente (con confirmación)
    window.confirmarQuitarResidente = function(btn) {
        const $modal = $(btn).closest('.modal');
        const idInmueble = $modal.data('id-inmueble');

        if (!idInmueble) {
            $modal.modal('hide');
            return;
        }

        $.ajax({
            url: 'Edificaciones',
            method: 'POST',
            dataType: 'json',
            data: { action: 'quitar-residente', id_inmueble: idInmueble },
            success: function(response) {
                if (response && response.ok) {
                    executeModal('okQuit', 'Residente quitado', 'Se quitó el residente correctamente.', 'warning');
                    if (response.html && response.idEdificio) {
                        $('.container-inmuebles-' + response.idEdificio).html(response.html);
                    }
                } else {
                    executeModal('warnQuit', 'Aviso', (response && response.msg) ? response.msg : 'No se pudo quitar.', 'warning');
                }
            },
            error: function() {
                executeModal('errQuit', 'Error', 'Error al procesar la solicitud.', 'warning');
            },
            complete: function() {
                $modal.modal('hide');
                $modal.removeData('id-inmueble');
            }
        });
    };

    $(document).on('click', '.btn-quitar-residente', function() {
        const modalId = 'confirmQuitResidente';
        const idInmueble = $(this).data('id-inmueble');

        executeModal(
            modalId,
            'Confirmar',
            '¿Está seguro de quitar al residente de este inmueble?',
            'confirm',
            'confirmarQuitarResidente'
        );

        $('#' + modalId)
            .data('id-inmueble', idInmueble)
            .off('hidden.bs.modal.confirmQuitResidente')
            .on('hidden.bs.modal.confirmQuitResidente', function() {
                $(this).removeData('id-inmueble');
            });
    });

    // Agregar residente (pide codcliente)
    $(document).on('click', '.btn-agregar-residente', function() {
        const idInmueble = $(this).data('id-inmueble');
        const codigoExterno = $(this).data('codigo-externo');
        const numero = $(this).data('numero');
        const direccionDefault = codigoExterno + ' - Apto. ' + numero;

        const modalId = 'addResidente';
        const contenido = '<div class="mb-3">' +
            '<label class="form-label">Residente (Cliente)</label>' +
            '<select class="form-select" id="codcliente-select" style="width: 100%">' +
            '<option value="">Seleccione un Residente...</option>' +
            '</select>' +
            '</div>' +
            '<div class="mb-3">' +
            '<label class="form-label">Dirección</label>' +
            '<input type="text" class="form-control" id="direccion-input" value="' + direccionDefault + '">' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-md-6 mb-3">' +
            '<label class="form-label">Fecha Ocupación</label>' +
            '<input type="date" class="form-control" id="fecha-ocupacion-input">' +
            '</div>' +
            '<div class="col-md-6 mb-3">' +
            '<label class="form-label">Fecha Disponibilidad</label>' +
            '<input type="date" class="form-control" id="fecha-disponibilidad-input">' +
            '</div>' +
            '</div>' +
            '<div class="mb-1"><span class="small text-muted">La fecha de Ocupaci&oacute;n es la fecha en la que el inquilino ocupó el Inmueble</span></div>' +
            '<div class="mb-3"><span class="small text-muted">La fecha de Disponibilidad es la fecha en la que termina el contrato del residente en caso de no ser propietario del Inmueble</span></div>';

        // Definir función global para usar desde el modal
        window.guardarAsignacionResidente = function() {
            const cod = $('#codcliente-select').val();
            const direccion = $('#direccion-input').val();
            const fechaOcupacion = $('#fecha-ocupacion-input').val();
            const fechaDisponibilidad = $('#fecha-disponibilidad-input').val();

            if (!cod) {
                executeModal('faltanDatos', 'Faltan datos', 'Debe seleccionar un cliente.', 'warning');
                return;
            }
            $.ajax({
                url: 'Edificaciones',
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'asignar-residente',
                    id_inmueble: idInmueble,
                    codcliente: cod,
                    direccion: direccion,
                    fecha_ocupacion: fechaOcupacion,
                    fecha_disponibilidad: fechaDisponibilidad
                },
                success: function(response) {
                    if (response && response.ok) {
                        executeModal('okAdd', 'Residente asignado', 'Se asignó el residente correctamente.', 'warning');
                        if (response.html && response.idEdificio) {
                            $('.container-inmuebles-' + response.idEdificio).html(response.html);
                        }
                        $('#addResidente').modal('hide');
                    } else {
                        executeModal('warnAdd', 'Aviso', (response && response.msg) ? response.msg : 'No se pudo asignar.', 'warning');
                    }
                },
                error: function() {
                    executeModal('errAdd', 'Error', 'Error al procesar la solicitud.', 'warning');
                }
            });
        };

        executeModal(modalId, 'Asignar residente', contenido, 'default', 'guardarAsignacionResidente', 'medium');

        // Inicializar select2 después de que el modal se muestre o se inserte el contenido
        // Como executeModal inserta el contenido inmediatamente, podemos intentarlo aquí
        const $select = $('#codcliente-select');
        $select.select2({
            theme: "bootstrap-5",
            dropdownParent: $('#' + modalId),
            placeholder: 'Buscar por nombre...',
            minimumInputLength: 2,
            ajax: {
                url: 'Edificaciones',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'buscar-clientes',
                        term: params.term || ''
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.codcliente,
                                text: item.nombre + ' (' + item.codcliente + ')'
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });
});

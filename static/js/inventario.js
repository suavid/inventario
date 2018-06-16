var AplicacionDeInventario = angular.module('ERPapp');

AplicacionDeInventario.controller('SegmentacionController', SegmentacionController);
AplicacionDeInventario.controller('PrincipalController', PrincipalController);
AplicacionDeInventario.controller('BodegaController', BodegaController);
AplicacionDeInventario.controller('CatalogoController', CatalogoController);
AplicacionDeInventario.controller('DocumentoController', DocumentoController);
AplicacionDeInventario.controller('TrasladoController', TrasladoController);

AplicacionDeInventario.directive('multiSelectChecker', function ($compile) {
    return {
        restrict: 'A',
        replace: false,
        terminal: true, 
        priority: 50000, 
        compile: function compile(element, attrs) {
            element.removeAttr("multi-select-checker");
            element.removeAttr("data-multi-select-checker");

            return {
                pre: function preLink(scope, iElement, iAttrs, controller) { },
                post: function postLink(scope, iElement, iAttrs, controller) {
                    if (scope.categoria.multilinea) {
                        iElement[0].setAttribute('multiple', '');
                    }
                    $compile(iElement)(scope);
                }
            };
        }
    };
});

notificationService.$inject = ['$http', '$sce', 'CONFIG'];

function SegmentacionController($http) {
    var vm = this;

    // Variables contenedoras de datos
    vm.Categorias = [];

    vm.NuevaCategoriaEspecifica = null;

    var DefaultHtml = '<p class = "text-center" ><br/><br/><br/><br/><br/><br/> Seleccione una de las categorías listadas en el panel de la izquierda para agregar, modificar o eliminar elementos.';

    vm.SegmentacionHTML = DefaultHtml;

    // Funciones publicas
    vm.CargarDetalleCategoria = CargarDetalleCategoria;
    vm.GuardarNuevaCategoriaEspecifica = GuardarNuevaCategoriaEspecifica;

    // Cargar las categorias registradas en el sistema
    $http.post('/inventario/inventario/ObtenerCategorias', {}, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function(data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function(response) {
        vm.Categorias = response.data;
    });


    // Obtener los items bajo una categoria
    function CargarDetalleCategoria(categoria, titulo) {
        $http.post('/inventario/inventario/ObtenerFormularioCategoria', { id: categoria, tituloFormulario: titulo }, {
            headers: {
                "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
            },
            transformRequest: [function(data) {
                return angular.isObject(data) ?
                    jQuery.param(data) :
                    data;
            }]
        }).then(function(response) {
            vm.SegmentacionHTML = response.data.html;
        });
    }

    function GuardarNuevaCategoriaEspecifica(categoria) {
        if (vm.NuevaCategoriaEspecifica !== null && vm.NuevaCategoriaEspecifica.trim() !== "") {
            $http.post('/inventario/inventario/GuardarNuevaCategoriaEspecifica', { id_grupo: categoria, nombre: vm.NuevaCategoriaEspecifica }, {
                headers: {
                    "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
                },
                transformRequest: [function (data) {
                    return angular.isObject(data) ?
                        jQuery.param(data) :
                        data;
                }]
            }).then(function (response) {
                if (response.data.success) {
                    alert("Solicitud procesada con éxito");
                    vm.NuevaCategoriaEspecifica = "";
                    var grid = Sigma.$grid("CategoriaDetalle_grid");
                    grid.loadURL = '/inventario/inventario/ObtenerDetalleCategoria/' + categoria;
                    grid.reload();
                } else {
                    alert("Ocurrió un error inesperado procesando su solicitud: " + response.data.message);
                }
            });
        } else {
            alert("Por favor indique un nombre");
        }
    }
}

function PrincipalController($http, $sce, CONFIG) {
    var vm = this;
    vm.Banners = [];
    vm.Organizacion = {};

    $http.post('/inventario/inventario/ObtenerBanner', { modulo: "INVENTARIO" }, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function(data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function(response) {
        vm.Banners = response.data;

        for (var i = 0; i < vm.Banners.length; i++) {
            vm.Banners[i].descripcion = $sce.trustAsHtml(vm.Banners[i].descripcion);
        }
    });

    $http.post('/inventario/inventario/ObtenerInformacionDelSistema', { }, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.Organizacion = response.data[0];
    });


}

function BodegaController($http) {
    var vm = this;

    vm.bodega = {
        manejaStock: true,
        reutilizarCorrelativos: false,
        id: 0,
        nombre: "",
        encargado: 0,
        descripcion: ""
    };


    vm.listaDeEmpleados = [];

    vm.GuardarBodega = GuardarBodega;

    $http.post('/inventario/inventario/ObtenerEmpleados', { }, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.listaDeEmpleados = response.data;
        if (vm.listaDeEmpleados.length > 0) {
            vm.bodega.encargado = String(vm.listaDeEmpleados[0].id);
        }
     });

    function GuardarBodega() {
        if (confirm("Por favor verifique que todos los datos sean correctos antes de guardar la información\n\n\n Está seguro que desea proseguir?")) {

            if (vm.bodega.encargado !== 0 && vm.bodega.nombre.trim() !== "" && vm.bodega.descripcion.trim() !== "") {
                $http.post('/inventario/inventario/GuardarBodega', vm.bodega, {
                    headers: {
                        "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
                    },
                    transformRequest: [function (data) {
                        return angular.isObject(data) ?
                            jQuery.param(data) :
                            data;
                    }]
                }).then(function (response) {
                    var grid = Sigma.$grid("bodega_grid");
                    grid.loadURL = '/inventario/inventario/ObtenerBodegas';
                    grid.reload();
                });
            } else {
                alert("Faltan datos requeridos");
            }
        }
    }
}

function CatalogoController($http) {
    var vm = this;
}

function DocumentoController($http, $scope) {
    var vm = this;

    vm.EstiloSugerido = null;
    vm.ListadoDeSugerencias = "";
    vm.ProductStep = 1;

    vm.ListaDeDocumentos = [];
    vm.ListaDeCatalogos = [];
    vm.ListaDeEstilos = [];

    vm.ListaDeProveedores = [];
    vm.ListaDeCategorias = [];
    vm.ListaDeCategoriasEspecificas = [];

    vm.TarjetaCosto = {
        documento: null,
        proveedor: null,
        estilo: null,
        codigo_origen: null,
        descripcion: null,
        dias_garantia: 0,
        catalogo: null,
        n_pagina: 1,
        propiedad: "2",
        categorias: {},
        observaciones: null,
        notas: null,
        corridaA: 0,
        corridaB: 0,
        fraccionCorrida: 0,
        categoriasArr: null,
        serialnumber: null
    };

    vm.Producto = {
        linea: null,
        estilo: null,
        color: null,
        tallaInferior: null,
        tallaSuperior: null
    };

    vm.DatosActuales = {
        precio: null,
        catalogo: null,
        n_pagina: null,
        propiedad: null
    };

    vm.DatosNuevos = {
        precio: null,
        catalogo: null,
        n_pagina: null,
        propiedad: null
    };

    vm.Modificar = {
        precio: false,
        catalogo: false,
        n_pagina: false,
        propiedad: false
    };

    vm.NuevoDocumento = NuevoDocumento;
    vm.ConfirmarCambios = ConfirmarCambios;
    vm.NextStep = NextStep;
    vm.PrevStep = PrevStep;
    vm.InsertarProducto = InsertarProducto; 
    vm.VerificarEntero = VerificarEntero;

    function NextStep() {

        switch (vm.ProductStep) {
            case 1:
                var missingData = false;

                missingData = vm.TarjetaCosto.proveedor === null || String(vm.TarjetaCosto.proveedor).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.categorias[29] === null || String(vm.TarjetaCosto.categorias[29]).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.estilo === null || String(vm.TarjetaCosto.estilo).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.codigo_origen === null || String(vm.TarjetaCosto.codigo_origen).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.descripcion === null || String(vm.TarjetaCosto.descripcion).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.dias_garantia === null || String(vm.TarjetaCosto.dias_garantia).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.catalogo === null || String(vm.TarjetaCosto.catalogo).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.n_pagina === null || String(vm.TarjetaCosto.n_pagina).trim() === '' || missingData;
                missingData = vm.TarjetaCosto.propiedad === null || String(vm.TarjetaCosto.propiedad).trim() === '' || missingData;

                if (missingData) {
                    alert("TODOS los datos son obligatorios");
                    return;
                }

                break;
            case 2:
                break;
        }

        vm.ProductStep++;
        $('html,body').scrollTop(0);
    }

    function PrevStep() {
        vm.ProductStep--;
        $('html,body').scrollTop(0);
    }

    function NuevoDocumento() {
        if (confirm("Está seguro que desea crear un nuevo documento?")) {
            $http.post('/inventario/inventario/GuardarDocumento', {}, {
                headers: {
                    "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
                },
                transformRequest: [function (data) {
                    return angular.isObject(data) ?
                        jQuery.param(data) :
                        data;
                }]
            }).then(function (response) {
                CargarDocumentos();
            });
        }
    }

    function InsertarProducto() {
        if (vm.TarjetaCosto.fraccionCorrida <= 0 || vm.TarjetaCosto.fraccionCorrida > 1) {
            alert('La fracción de corrida debe estar entre 0.1 a 1');
            return;
        }

        if (parseInt(vm.TarjetaCosto.corridaA) > parseInt(vm.TarjetaCosto.corridaB)) {
            alert('El límite inferior de la corrida debe ser menor que el límite superior');
            return;
        }

        // validar segmentación

        vm.TarjetaCosto.categoriasArr = $.map(vm.TarjetaCosto.categorias, function (value, index) {
            return [value];
        });

        for (var i = 0; i < vm.TarjetaCosto.categoriasArr.length; i++) {
            if (vm.TarjetaCosto.categoriasArr[i] instanceof Array) {
                vm.TarjetaCosto.categoriasArr[i] = vm.TarjetaCosto.categoriasArr[i].join(',');
            }
        }

        vm.TarjetaCosto.categoriasArr = vm.TarjetaCosto.categoriasArr.join(',');

        $http.post('/inventario/inventario/InsertarProducto', vm.TarjetaCosto, {
            headers: {
                "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
            },
            transformRequest: [function (data) {
                return angular.isObject(data) ?
                    jQuery.param(data) :
                    data;
            }]
        }).then(function (response) {
            // Refresh?
            location.href = location.href;
        });

    }

    function VerificarEntero(obj, key) {
        if (isNaN(obj[key])) {
            obj[key] = 0;
        }
    }

    function ConfirmarCambios(idDocumento) {
        if (confirm("Desea aplicar los cambios? Serán permanentes y no podrán deshacerse.")) {
            $http.post('/inventario/inventario/AplicarDocumento', { id: idDocumento }, {
                headers: {
                    "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
                },
                transformRequest: [function (data) {
                    return angular.isObject(data) ?
                        jQuery.param(data) :
                        data;
                }]
            }).then(function (response) {
                // Refresh?
                location.href = location.href;
            });
        }
    }

    function IsMultiple(id) {
        for (var i = 0; i < vm.ListaDeCategorias.length; i++) {
            if (vm.ListaDeCategorias[i].id_grupo === id) {
                return vm.ListaDeCategorias[i].multilinea;
            }
        }

        return false;
    }

    function CargarDocumentos() {
        $http.post('/inventario/inventario/ObtenerDocumentosSinAplicar', {}, {
            headers: {
                "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
            },
            transformRequest: [function (data) {
                return angular.isObject(data) ?
                    jQuery.param(data) :
                    data;
            }]
        }).then(function (response) {
            vm.ListaDeDocumentos = response.data;
        });
    }


    $http.post('/inventario/inventario/ObtenerCategorias', {}, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.ListaDeCategorias = response.data;
    });

    $http.post('/inventario/inventario/ObtenerProveedores', {}, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.ListaDeProveedores = response.data;
        if (response.data.length > 0) {
            vm.TarjetaCosto.proveedor = String(response.data[0].id);
        }
    });

    $http.post('/inventario/inventario/ObtenerListaDeCatalogos', {}, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.ListaDeCatalogos = response.data;
        if (response.data.length > 0) {
            vm.TarjetaCosto.catalogo = String(response.data[0].id);
        }
    });


    $scope.$watch("vm.ListaDeCategorias", function (newValue, oldValue) {

        if (newValue === oldValue) {
            return;
        }

        for (x in newValue) {
            $http.post('/inventario/inventario/ObtenerCategoriaEspecifica/' + newValue[x].id_grupo, {}, {
                headers: {
                    "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
                },
                transformRequest: [function (data) {
                    return angular.isObject(data) ?
                        jQuery.param(data) :
                        data;
                }]
            }).then(function (response) {
                vm.ListaDeCategoriasEspecificas[response.data[0].id_categoria] = response.data;

                if (response.data.length > 0) {
                    if (IsMultiple(response.data[0].id_categoria)) {
                        vm.TarjetaCosto.categorias[response.data[0].id_categoria] = [];
                        vm.TarjetaCosto.categorias[response.data[0].id_categoria].push(response.data[0].id_categoria_especifica);
                    } else {
                        vm.TarjetaCosto.categorias[response.data[0].id_categoria] = response.data[0].id_categoria_especifica;
                    }
                    
                }

            });
        }
    });

    CargarDocumentos();
}

function TrasladoController($http, $scope) {
    var vm = this;

    vm.ListaDeTransacciones = [];
    vm.ListaDeBodegas = [];
    vm.ListaDeProveedores = [];
    vm.ListaDeRetaceos = [];
    vm.DatosTraslado = {};

    vm.IdTraslado = 0;

    vm.InsertarTraslado = InsertarTraslado;
    vm.CargarTraslado = CargarTraslado;
    vm.ProcesarTraslado = ProcesarTraslado;

    vm.Traslado = {
        tipoTransaccion: null,
        proveedorOrigen: null,
        proveedorNacional: null,
        bodegaOrigen: null,
        bodegaDestino: null,
        conceptoTransaccion: null,
        hojaRetaceo: null,
        cliente: 0
    };

    $http.post('/inventario/inventario/ObtenerProveedores', {}, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.ListaDeProveedores = response.data;
        if (response.data.length > 0) {
            vm.Traslado.proveedorOrigen = String(response.data[0].id);
            vm.Traslado.proveedorNacional = String(response.data[0].id);
        }
    });

    $http.post('/inventario/inventario/ListaBodegas', {}, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.ListaDeBodegas = response.data;
        if (response.data.length > 0) {
            vm.Traslado.bodegaOrigen = String(response.data[0].id);
            vm.Traslado.bodegaDestino = String(response.data[0].id);
        }
    });

    $http.post('/inventario/inventario/ObtenerTipoTransacciones', {}, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function (data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function (response) {
        vm.ListaDeTransacciones = response.data;
        if (response.data.length > 0) {
            vm.Traslado.tipoTransaccion = String(response.data[0].cod);
        }
    });

    function InsertarTraslado() {
        $http.post('/inventario/inventario/InsertarTraslado', vm.Traslado, {
            headers: {
                "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
            },
            transformRequest: [function (data) {
                return angular.isObject(data) ?
                    jQuery.param(data) :
                    data;
            }]
        }).then(function (response) {
            location.href = location.href;
        });
    }

    function ProcesarTraslado()
    {
        if (confirm("Esta seguro que desea confirmar la transacción?")) {
            $http.post('/inventario/inventario/ProcesarTraslado', { id: vm.IdTraslado }, {
                headers: {
                    "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
                },
                transformRequest: [function (data) {
                    return angular.isObject(data) ?
                        jQuery.param(data) :
                        data;
                }]
            }).then(function (response) {
                location.href = '/inventario/inventario/traslados';
            });
        }
    }

    function CargarTraslado(IdTraslado) {
        $http.post('/inventario/inventario/VerTraslado', { id: IdTraslado }, {
            headers: {
                "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
            },
            transformRequest: [function (data) {
                return angular.isObject(data) ?
                    jQuery.param(data) :
                    data;
            }]
        }).then(function (response) {
            vm.DatosTraslado = response.data[0];
            vm.DatosTraslado.bodega_origen = String(vm.DatosTraslado.bodega_origen);
            vm.DatosTraslado.bodega_destino = String(vm.DatosTraslado.bodega_destino);
            vm.DatosTraslado.proveedor_nacional = String(vm.DatosTraslado.proveedor_nacional);
            vm.DatosTraslado["linea"] = 0;
            vm.DatosTraslado["estilo"] = 0;
            vm.DatosTraslado["color"] = 0;
        });
    }

    $scope.$watch("vm.IdTraslado", function (newValue, oldValue) {

        if (newValue === oldValue) {
            return;
        }


        vm.CargarTraslado(newValue);
    });

    $scope.$watch("vm.DatosTraslado.linea", function (newValue, oldValue) {

        if (newValue === oldValue) {
            return;
        }

        AplicarFiltros();

    });

    $scope.$watch("vm.DatosTraslado.estilo", function (newValue, oldValue) {

        if (newValue === oldValue) {
            return;
        }

        AplicarFiltros();

    });

    $scope.$watch("vm.DatosTraslado.color", function (newValue, oldValue) {

        if (newValue === oldValue) {
            return;
        }

        AplicarFiltros();

    });


    function AplicarFiltros() {
        var grid = Sigma.$grid("grid_inventario");
        grid.loadURL = '/inventario/inventario/CargarEstadoInventario?linea=' + vm.DatosTraslado.linea + '&estilo=' + vm.DatosTraslado.estilo + '&color=' + vm.DatosTraslado.color + '&proveedor=' + vm.DatosTraslado.proveedor_nacional + '&bodega_origen=' + vm.DatosTraslado.bodega_origen + '&bodega_destino=' + vm.DatosTraslado.bodega_destino + '&cod=' + vm.DatosTraslado.transaccion;
        grid.reload();
    }
}
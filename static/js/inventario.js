var AplicacionDeInventario = angular.module('Inventario');

// Asignacion de controladores
AplicacionDeInventario.controller('SegmentacionController', SegmentacionController);
AplicacionDeInventario.controller('PrincipalController', PrincipalController);


// ************************************************************************************
// Definicion de controladores
// ************************************************************************************

function SegmentacionController($http) {
    var vm = this;

    // Variables contenedoras de datos
    vm.Categorias = [];

    // Funciones publicas
    vm.CargarDetalleCategoria = CargarDetalleCategoria;


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
            alert(response);
        });
    }

}

// *****************************************************************************************

function PrincipalController($http, $sce) {
    var vm = this;

    // Informacion de banners de la pantalla principal
    vm.Banners = {
        "b1": {},
        "b2": {}
    }

    // Obtener los banners para mostrar en la pantalla principal
    $http.post('/inventario/inventario/ObtenerBanner', { id: 1 }, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function(data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function(response) {
        vm.Banners.b1 = response.data[0];
        vm.Banners.b1.descripcion = $sce.trustAsHtml(vm.Banners.b1.descripcion);
    });

    // Obtener los banners para mostrar en la pantalla principal
    $http.post('/inventario/inventario/ObtenerBanner', { id: 2 }, {
        headers: {
            "Content-Type": 'application/x-www-form-urlencoded;charset=utf-8'
        },
        transformRequest: [function(data) {
            return angular.isObject(data) ?
                jQuery.param(data) :
                data;
        }]
    }).then(function(response) {
        vm.Banners.b2 = response.data[0];
        vm.Banners.b2.descripcion = $sce.trustAsHtml(vm.Banners.b2.descripcion);
    });
}

// *****************************************************************************************
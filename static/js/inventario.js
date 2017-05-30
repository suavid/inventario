var AplicacionDeInventario = angular.module('Inventario');

AplicacionDeInventario.controller('SegmentacionController', SegmentacionController);
AplicacionDeInventario.controller('PrincipalController', PrincipalController);

function SegmentacionController($http) {
    var vm = this;

    vm.Categorias = [];

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

}

function PrincipalController($http, $sce) {
    var vm = this;

    vm.Banners = {
        "b1": {},
        "b2": {}
    }

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
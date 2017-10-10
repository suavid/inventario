<?php

class inventarioView {

    public function principal()
    {
        template()->buildFromTemplates(DEFAULT_LAYOUT);

        page()->setTitle('Módulo de control de inventario');
        template()->addTemplateBit('content', 'inventario/principal.html');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("TITULO", "Página principal");
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        template()->parseOutput();
        template()->parseExtras();

        print page()->getContent();
    }

    public function segmentacion()
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "segmentacion", Session::singleton()->getUser()))
        {

            template()->buildFromTemplates(DEFAULT_LAYOUT);

            page()->setTitle('Control de inventario');
            page()->addEstigma("username", Session::singleton()->getUser());
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            page()->addEstigma("TITULO", 'Segmentación de productos');
            template()->addTemplateBit('content', 'inventario/menu/segmentacion.html');
            template()->parseOutput();
            template()->parseExtras();

            print page()->getContent();
        }
        else
        {
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function ObtenerFormularioCategoria($id,$tituloFormulario)
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "segmentacion", Session::singleton()->getUser()))
        {
            template()->buildFromTemplates('inventario/FormularioCategoria.html');

            page()->addEstigma("seg_TituloFormulario", $tituloFormulario);
            page()->addEstigma("seg_IdGrupo", $id);
            template()->parseOutput();

            return page()->getContent();
        }
        else
        {
            return "";
        }
    }

    public function mantenimiento_de_bodegas($user)
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "bodegas", Session::singleton()->getUser()))
        {
            template()->buildFromTemplates(DEFAULT_LAYOUT);

            page()->setTitle('Mantenimiento de bodegas');
            page()->addEstigma("username", $user);
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            page()->addEstigma("TITULO","Administración de bodegas virtuales");
            template()->addTemplateBit('content', 'inventario/bodegas.html');
            template()->parseOutput();
            template()->parseExtras();

            print page()->getContent();
        }
        else
        {
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function nuevo_producto($user)
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "nuevo_producto", $user))
        {
            template()->buildFromTemplates(DEFAULT_LAYOUT);

            page()->setTitle('Mantenimiento de productos');
            page()->addEstigma("TITULO", 'Registro de productos');
            page()->addEstigma("username", $user);
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            template()->addTemplateBit('content', 'inventario/documentoProducto.html');
            template()->parseOutput();
            template()->parseExtras();

            print page()->getContent();
        }
        else
        {
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function cambiarPrecios($user)
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "cambiarPrecios", $user))
        {
            template()->buildFromTemplates(DEFAULT_LAYOUT);

            page()->addEstigma("TITULO",'Actualización de productos');
            page()->setTitle('Actualización de productos');
            page()->addEstigma("username", $user);
            page()->addEstigma("back_url", '/inventario/inventario/nuevo_producto');
            template()->addTemplateBit('content', 'inventario/cambiarPrecios.html');
            template()->parseOutput();
            template()->parseExtras();

            print page()->getContent();
        }
        else
        {
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function actualizarFoto()
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "actualizarFoto", Session::singleton()->getUser()))
        {
            template()->buildFromTemplates(DEFAULT_LAYOUT);

            page()->setTitle('Control de inventario');
            page()->addEstigma("username", Session::singleton()->getUser());
            page()->addEstigma("back_url", '/inventario/inventario/nuevo_producto');
            page()->addEstigma("TITULO", 'Actualizar imagen del producto');
            template()->addTemplateBit('content', 'inventario/actualizarFoto.html');
            template()->parseOutput();
            template()->parseExtras();

            print page()->getContent();
        }
        else
        {
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function productosSugeridos()
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "productosSugeridos", Session::singleton()->getUser()))
        {
            template()->buildFromTemplates(DEFAULT_LAYOUT);

            page()->setTitle('Productos sugeridos');
            page()->addEstigma("username", Session::singleton()->getUser());
            page()->addEstigma("back_url", '/inventario/inventario/nuevo_producto');
            page()->addEstigma("TITULO", 'Asociación de productos sugeridos');
            template()->addTemplateBit('content', 'inventario/productoSugerido.html');
            template()->parseOutput();
            template()->parseExtras();

            print page()->getContent();
        }
        else
        {
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function traslados($user)
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "traslados", $user))
        {
            template()->buildFromTemplates(DEFAULT_LAYOUT);

            template()->addTemplateBit('content', 'inventario/traslados.html');
            page()->setTitle('Traslados');
            page()->addEstigma("username", $user);
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            page()->addEstigma("TITULO", 'Traslados');
            page()->addEstigma("fecha", date("Y-m-d"));
            template()->parseOutput();
            template()->parseExtras();

            print page()->getContent();
        }
        else
        {
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function doc_mantenimiento_de_productos($doc, $user) 
    {
        import('scripts.secure');

        if(verifyAccess("inventario", "inventario", "doc_productos", $user))
        {
            template()->buildFromTemplates(DEFAULT_LAYOUT);
            page()->setTitle('Productos');
            template()->addTemplateBit('content', 'inventario/doc_productos.html');
            page()->addEstigma("back_url", '/inventario/inventario/nuevo_producto');
            page()->addEstigma("TITULO", 'Registrar producto');
            page()->addEstigma("username", $user);
            page()->addEstigma("documento", $doc);
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }
        else
        {

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function detalle_traslado($user, $id) 
    {
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "detalle_traslado", Session::singleton()->getUser()))
        {
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Traslado No.' . $id);
            page()->addEstigma("username", $user);
            page()->addEstigma("idencabezado", $id);
            page()->addEstigma("back_url", '/inventario/inventario/traslados');
            page()->addEstigma("TITULO", 'Detalle de traslado');
            template()->addTemplateBit('content', 'inventario/traslado_detalle.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }
        else
        {

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    // no validado

    public function revision_de_salida($series){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Revisión de salida');
        template()->addTemplateBit('content', 'inventario/revision_de_salida.html');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("TITULO", "Revisión de salida");
        page()->addEstigma("series", array('SQL', $series));
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function construir_plantilla($user, $admin) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Control de inventario');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/modulo/destruirSesion/inventario');
        page()->addEstigma("TITULO", 'Inventario');
        page()->addEstigma("acceso", $admin);
        template()->addTemplateBit('content', 'inventario/inventario.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function recibir_oc($detalle, $id, $orden, $proveedor, $anexos){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Control de inventario');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/orden_compra');
        page()->addEstigma("TITULO", 'Inventario');
        page()->addEstigma("detalle", array('SQL', $detalle));
        page()->addEstigma("anexos", array('SQL', $anexos));
        page()->addEstigma("norden", $id);
        page()->addEstigma("orden_estado", $orden->estado);
        page()->addEstigma("orden_proveedor", $orden->proveedor);
        page()->addEstigma("orden_total", $orden->total);
        page()->addEstigma("orden_total_costo", $orden->total_costo);
        page()->addEstigma("codprov", $orden->proveedor);
        page()->addEstigma("proveedor_nombre", $proveedor->nombre);
        page()->addEstigma("proveedor_telefono", $proveedor->telefono);
        page()->addEstigma("proveedor_direccion", $proveedor->direccion);
        page()->addEstigma("proveedor_contacto", $proveedor->nombre_contacto);
        page()->addEstigma("proveedor_telefono_contacto", $proveedor->telefono_contacto);
        if(empty($orden->concepto)) $orden->concepto = "ND";
        page()->addEstigma("orden_concepto", $orden->concepto);
        template()->addTemplateBit('content', 'inventario/orden_compra/recepcion.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function operaciones_oc($operaciones){
        template()->buildFromTemplates('inventario/orden_compra/operaciones_oc.html');
        page()->addEstigma("operaciones", array('SQL', $operaciones));
        template()->parseOutput();
        return page()->getContent();
    }

    public function kits(){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Creación de kits');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        template()->addTemplateBit('content', 'inventario/kits.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function hoja_retaceo(){
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "hoja_retaceo", Session::singleton()->getUser())){
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Creación de hojas de retaceo');
            page()->addEstigma("username", Session::singleton()->getUser());
            page()->addEstigma("TITULO", "Crear hoja de retaceo");
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            template()->addTemplateBit('content', 'inventario/hoja_retaceo.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }else{

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function editar_hoja_retaceo($id_hoja, $total, $detalle){
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "hoja_retaceo", Session::singleton()->getUser())){
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Editar hoja de retaceo');
            page()->addEstigma("username", Session::singleton()->getUser());
            page()->addEstigma("id_hoja", $id_hoja);
            page()->addEstigma("total", $total);
            page()->addEstigma("TITULO","Gastos por adquisición de producto");
            page()->addEstigma("detalle", array('SQL', $detalle));
            page()->addEstigma("back_url", '/inventario/inventario/hoja_retaceo');
            template()->addTemplateBit('content', 'inventario/editar_hoja_retaceo.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }else{

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function ver_hoja_retaceo($id_hoja, $total, $detalle){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Ver hoja de retaceo');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("id_hoja", $id_hoja);
        page()->addEstigma("TITULO","Gastos por adquisición de producto");
        page()->addEstigma("total", $total);
        page()->addEstigma("detalle", array('SQL', $detalle));
        page()->addEstigma("back_url", '/inventario/inventario/hoja_retaceo');
        template()->addTemplateBit('content', 'inventario/ver_hoja_retaceo.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function movKardex(){
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "movKardex", Session::singleton()->getUser())){
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Kardex');
            page()->addEstigma("username", Session::singleton()->getUser());
            page()->addEstigma("TITULO", "Consulta rápida de kardex");
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            template()->addTemplateBit('content', 'inventario/mov_kardex.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }else{

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function noKardex(){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Kardex');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        template()->addTemplateBit('content', 'inventario/no_kardex.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }


    public function detalle_de_producto($estilo, $general, $n, $lineas, $cache_stock, $cache_transito, $cache_sugeridos){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Detalle de producto ');
        template()->addTemplateBit('content', 'inventario/detalle_producto.html');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/modulo/listar');
        page()->addEstigma("TITULO", "Resumen de estado del producto");
        page()->addEstigma("general", array('SQL', $general));
        page()->addEstigma("estilo", $estilo);
        page()->addEstigma("n", $n);


        foreach ($lineas as $linea) {
            page()->addEstigma("estado_bodega_".$linea, array('SQL', $cache_stock[$linea]));
        }

        foreach ($lineas as $linea) {
            page()->addEstigma("producto_transito_".$linea, array('SQL', $cache_transito[$linea]));
        }

        foreach ($lineas as $linea) {
            page()->addEstigma("producto_sugerido_".$linea, array('SQL', $cache_sugeridos[$linea]));
        }

        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function alertasDeStock($cache){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Alertas de stock');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/modulo/alerta');
        page()->addEstigma("TITULO", 'Inventario');
        page()->addEstigma("detalle", array('SQL', $cache));
        template()->addTemplateBit('content', 'inventario/alertas_de_stock.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function cambio_de_linea($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Cambio de linea');
        page()->addEstigma("linea01", array('SQL', $cache[0]));
        page()->addEstigma("linea02", array('SQL', $cache[1]));
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("username", $user);
        template()->addTemplateBit('content', 'inventario/cambio_de_linea.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function cambio_de_grupo($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Cambio de grupo');
        page()->addEstigma("grupo01", array('SQL', $cache[0]));
        page()->addEstigma("grupo02", array('SQL', $cache[1]));
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("username", $user);
        template()->addTemplateBit('content', 'inventario/cambio_de_grupo.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function listadoTraslado($user) {
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "listadoTraslado", Session::singleton()->getUser())){
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Traslados');
            page()->addEstigma("username", $user);
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            page()->addEstigma("TITULO", 'Historial de traslados realizados');
            template()->addTemplateBit('content', 'inventario/traslado_listado.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }else{

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function listadoConsigna($user) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Consignas');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/consigna');
        page()->addEstigma("TITULO", 'Consignas');
        template()->addTemplateBit('content', 'inventario/consigna_listado.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function promociones($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Promociones');
        page()->addEstigma("username", $user);
        page()->addEstigma("lineas", array('SQL', $cache[0]));
        page()->addEstigma("fecha", date("Y/m/d"));
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Promociones');
        template()->addTemplateBit('content', 'inventario/promociones.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function consigna($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('consigna');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Consignas');
        page()->addEstigma("bodega", array('SQL', $cache[0]));
        page()->addEstigma("proveedor1", array('SQL', $cache[1]));
        page()->addEstigma("proveedor2", array('SQL', $cache[2]));
        template()->addTemplateBit('content', 'inventario/consigna.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function imprimirTraslado($id, $user, $cache, $transaccion, $nt, $fecha, $concepto, $total_pares, $total_costo, $ntdoc) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('inventario/trasladoExport.html');
        page()->addEstigma('usuario', $user);
        page()->addEstigma('t_pares', $total_pares);
        page()->addEstigma('t_costo', $total_costo);
        page()->addEstigma('ntdoc', $ntdoc);
        page()->addEstigma('timestamp', date("d/m/Y h:m:s A"));
        page()->addEstigma('cod', $transaccion);
        page()->addEstigma('ncod', $nt);
        page()->addEstigma('documento', $id);
        page()->addEstigma('fecha', $fecha);
        page()->addEstigma('concepto', $concepto);
        page()->addEstigma("traslado", array('SQL', $cache[0]));
        template()->parseOutput();
        $fp = fopen(APP_PATH."/temp/".Session::singleton()->getUser()."_traslado.html", "w");
        fputs($fp, page()->getContent());
        fclose($fp);
        $str = APP_PATH.'common\plugins\phantomjs\bin\phantomjs '.APP_PATH.'static\js\html2pdf.js file:///'.APP_PATH.'temp\\'.Session::singleton()->getUser().'_traslado.html '.APP_PATH.'temp\\'.Session::singleton()->getUser().'_traslado.pdf';
        system($str);
        $file = APP_PATH.'temp\\'.Session::singleton()->getUser().'_traslado.pdf';
        $filename = Session::singleton()->getUser().'_traslado.pdf';

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');

        @readfile($file);
    }

    public function reporteDocumentoPr($detalle, $id, $system) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('report/detalle_documento.html');

        page()->addEstigma('timestamp', date("d/m/Y h:m:s A"));
        page()->addEstigma('nodoc', $id);

        page()->addEstigma('nombre_empresa', $system->nombre_comercial);
        page()->addEstigma('direccion_empresa', $system->direccion);

        page()->addEstigma("detalle", array('SQL', $detalle));
        template()->parseOutput();
        template()->parseExtras();
        $html2pdf = new HTML2PDF('L', 'letter', 'es');
        $html2pdf->WriteHTML(page()->getContent());
        $html2pdf->Output('traslado.pdf');
    }

    public function cargar_tabla($tipoQuery, $cache) {
        $ret = array();
        switch ($tipoQuery) {
            case 1:
                template()->buildFromTemplates('inventario/comparativos/c1.html');
                break;
            case 2:
                template()->buildFromTemplates('inventario/comparativos/c2.html');
                break;
            case 3:
                template()->buildFromTemplates('inventario/comparativos/c3.html');
                break;
            case 4:
                template()->buildFromTemplates('inventario/comparativos/c4.html');
                break;
            case 5:
                template()->buildFromTemplates('inventario/comparativos/c5.html');
                break;
            default:
                template()->buildFromTemplates('inventario/comparativos/c1.html');
                break;
        }

        page()->addEstigma("inventario", array('SQL', $cache[0]));
        template()->parseOutput();
        $ret['html'] = page()->getContent();
        echo json_encode($ret);
    }

    public function p_transito($cache, $paginacion_str) {
        template()->buildFromTemplates('inventario/orden_compra/resumen.html');
        page()->addEstigma("listado", array('SQL', $cache[0]));
        page()->addEstigma("paginacion", $paginacion_str);
        template()->parseOutput();
        return page()->getContent();
    }

    public function edicion_oc($cache) {
        template()->buildFromTemplates('inventario/orden_compra/editar.html');
        page()->addEstigma("general", array('SQL', $cache[0]));
        page()->addEstigma("detalle", array('SQL', $cache[1]));
        page()->addEstigma("linea", array('SQL', $cache[2]));
        template()->parseOutput();
        return page()->getContent();
    }

    public function oc_data($cache) {
        $ret = array();
        template()->buildFromTemplates('inventario/orden_compra/oc_detalle.html');
        page()->addEstigma("general", array('SQL', $cache[0]));
        template()->parseOutput();
        $ret['html'] = page()->getContent();
        echo json_encode($ret);
    }

    public function orden_compra($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Orden de compra');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Orden de compra');
        page()->addEstigma("proveedores", array('SQL', $cache[0]));
        page()->addEstigma("colores", array('SQL', $cache[1]));
        page()->addEstigma("fecha", date("Y-m-d"));
        template()->addTemplateBit('content', 'inventario/orden_compra.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function ver_producto_en_transito($cache, $paginacion_str){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Producto en tránsito');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("paginacion", $paginacion_str);
        page()->addEstigma("back_url", '/inventario/inventario/orden_compra');
        page()->addEstigma("detalle", array('SQL', $cache));
        template()->addTemplateBit('content', 'inventario/orden_compra/producto_en_transito.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function imprimirReportesOC($id_orden, $traslado){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Orden de compra');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Orden de compra');
        page()->addEstigma("id_orden", $id_orden);
        page()->addEstigma("traslado", $traslado);
        template()->addTemplateBit('content', 'inventario/orden_compra/orden_compra_report.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function comparativo_fisico_teorico($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Comparativo');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Comparativo');
        page()->addEstigma("bodegas", array('SQL', $cache[0]));
        template()->addTemplateBit('content', 'inventario/comparativo.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function creacion_comparativo($user) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Comparativo');
        page()->addEstigma("username", $user);
        $this->load_settings();
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Comparativo');
        template()->addTemplateBit('content', 'inventario/creacion_comparativo.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function ofertas($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Ofertas');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Preparación de ofertas');
        page()->addEstigma("ofertas", array('SQL', $cache[0]));
        page()->addEstigma("aOferta", array('SQL', $cache[1]));
        page()->addEstigma("genero", array('SQL', $cache[2]));
        page()->addEstigma("vOferta", array('SQL', $cache[3]));
        template()->addTemplateBit('content', 'inventario/ofertas.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function reporte_inventario() {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Reporte - inventarios');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Reporte - inventarios');
        template()->addTemplateBit('content', 'inventario/reporte_inventarios.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function reporte_kardex() {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Reporte - kardex');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Reporte - kardex');
        template()->addTemplateBit('content', 'inventario/reporte_kardex.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function productoEntrante(){
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Producto en tránsito');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Producto en tránsito');
        template()->addTemplateBit('content', 'inventario/transito.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function imprimir_reporteComparativo($cache, $tipo, $system, $total, $data) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('report/template.html');

        switch($tipo){
            case 1:
                template()->addTemplateBit('contenido_reporte', 'report/reporteComparativoLinea.html');
                page()->addEstigma('comparativo', array('SQL', $cache[0]));
                //page()->addEstigma('total', $total);
                page()->addEstigma('titulo_reporte', 'Comparativo - lineas');
                break;
            case 2:
                template()->addTemplateBit('contenido_reporte', 'report/reporteComparativoProveedor.html');
                page()->addEstigma('comparativo', array('SQL', $cache[0]));
                page()->addEstigma('titulo_reporte', 'Comparativo - proveedor');
                break;
            case 3:
                template()->addTemplateBit('contenido_reporte', 'report/reporteComparativoEstilo.html');
                page()->addEstigma('proveedores', array('SQL', $cache['proveedores']));
                foreach($data['proveedores'] as $proveedor){
                    page()->addEstigma('comparativo_'.$proveedor['id_proveedor'], array('SQL',$cache['proveedor_'.$proveedor['id_proveedor']]));
                }
                page()->addEstigma('titulo_reporte', 'Comparativo - estilo');
                break;
            case 4:
                template()->addTemplateBit('contenido_reporte', 'report/reporteComparativoColor.html');
                page()->addEstigma('proveedores', array('SQL', $cache['proveedores']));
                foreach($data['proveedores'] as $proveedor){
                    page()->addEstigma('comparativo_'.$proveedor['id_proveedor'], array('SQL',$cache['proveedor_'.$proveedor['id_proveedor']]));
                }
                page()->addEstigma('titulo_reporte', 'Comparativo - color');
                break;
            case 5:
                template()->addTemplateBit('contenido_reporte', 'report/reporteComparativoTalla.html');
                page()->addEstigma('proveedores', array('SQL', $cache['proveedores']));
                foreach($data['proveedores'] as $proveedor){
                    page()->addEstigma('comparativo_'.$proveedor['id_proveedor'], array('SQL',$cache['proveedor_'.$proveedor['id_proveedor']]));
                }
                page()->addEstigma('titulo_reporte', 'Comparativo - talla');
                break;
        }


        page()->addEstigma('razon_social', $system->razon_social);
        page()->addEstigma('telefono', $system->telefono);
        page()->addEstigma('direccion', $system->direccion);
        page()->addEstigma('usuario', Session::singleton()->getUser());
        page()->addEstigma('fecha', date("d/m/Y"));
        page()->addEstigma('hora', date("h:i:s A"));

        template()->parseOutput();
        //template()->parseExtras();

        //$html2pdf = new HTML2PDF('P', 'letter', 'es');
        //$html2pdf->WriteHTML(page()->getContent());
        //$html2pdf->Output('comparativo.pdf');
        $fp = fopen(APP_PATH."/temp/".Session::singleton()->getUser()."_comparativo.html", "w");
        fputs($fp, page()->getContent());
        fclose($fp);
        $str = APP_PATH.'common\plugins\phantomjs\bin\phantomjs '.APP_PATH.'static\js\html2pdf.js file:///'.APP_PATH.'temp\\'.Session::singleton()->getUser().'_comparativo.html '.APP_PATH.'temp\\'.Session::singleton()->getUser().'_comparativo.pdf';
        system($str);
        $file = APP_PATH.'temp\\'.Session::singleton()->getUser().'_comparativo.pdf';
        $filename = Session::singleton()->getUser().'_traslado.pdf';

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');

        @readfile($file);
    }

    public function imprimir_reporteInventario($cache, $tipo, $system, $data, $fecha) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('report/template.html');

        switch($tipo){
            case "bodega":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioBodega.html');
                page()->addEstigma('titulo_reporte', "Inventario x bodega");
                page()->addEstigma('contenido', array('SQL', $cache[0]));
                break;
            case "linea":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioLinea.html');
                page()->addEstigma('titulo_reporte', "Inventario x linea");
                page()->addEstigma('bodegas', array('SQL', $cache['bodegas']));
                foreach($data['bodegas'] as $bodega){
                    page()->addEstigma('contenido_b'.$bodega, array('SQL', $cache['bodega_'.$bodega]));
                }

                break;
            case "proveedor":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioProveedor.html');
                page()->addEstigma('titulo_reporte', "Inventario x proveedor");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']]));
                }

                break;

            case "estilo":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioEstilo.html');
                page()->addEstigma('titulo_reporte', "Inventario x estilo");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
            case "color":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioColor.html');
                page()->addEstigma('titulo_reporte', "Inventario x color");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
            case "talla":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioTalla.html');
                page()->addEstigma('titulo_reporte', "Inventario x talla");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
            case "provmar":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioMarca.html');
                page()->addEstigma('titulo_reporte', "Inventario x marca");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
        }


        page()->addEstigma('razon_social', $system->razon_social);
        page()->addEstigma('telefono', $system->telefono);
        page()->addEstigma('direccion', $system->direccion);
        page()->addEstigma('usuario', Session::singleton()->getUser());
        page()->addEstigma('fecha', date("d/m/Y"));
        page()->addEstigma('fecha_saldos', $fecha);
        page()->addEstigma('hora', date("h:i:s A"));

        template()->parseOutput();
        template()->parseExtras();

        $html2pdf = new HTML2PDF('P', 'letter', 'es');
        $html2pdf->WriteHTML(page()->getContent());
        $html2pdf->Output('inventario.pdf');
    }

    public function imprimir_reporteKardex($cache, $tipo, $system, $data, $fecha, $fecha2) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('report/template.html');

        switch($tipo){
            case "bodega":
                template()->addTemplateBit('contenido_reporte', 'report/reporteKardexGeneral.html');
                page()->addEstigma('titulo_reporte', "Registro de control de inventario <br/> Saldos generales");
                page()->addEstigma('contenido', array('SQL', $cache[0]));
                break;
            case "linea":
                template()->addTemplateBit('contenido_reporte', 'report/reporteKardexProveedor.html');
                page()->addEstigma('titulo_reporte', "Registro de control de inventario <br/> Saldos por proveedor");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
            case "proveedor":
                template()->addTemplateBit('contenido_reporte', 'report/reporteKardexEstilo.html');
                page()->addEstigma('titulo_reporte', "Registro de control de inventario <br/> Saldos por estilo");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;

            case "estilo":
                template()->addTemplateBit('contenido_reporte', 'report/reporteKardexColor.html');
                page()->addEstigma('titulo_reporte', "Registro de control de inventario <br/> Saldos por color");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
            case "color":
                template()->addTemplateBit('contenido_reporte', 'report/reporteKardexTalla.html');
                page()->addEstigma('titulo_reporte', "Registro de control de inventario <br/> Saldos por talla");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
            case "talla":
                template()->addTemplateBit('contenido_reporte', 'report/reporteInventarioTalla.html');
                page()->addEstigma('titulo_reporte', "Registro de control de inventario <br/> Saldos por linea");
                page()->addEstigma('bodegasylineas', array('SQL', $cache['bodegasylineas']));
                foreach($data['bodegasylineas'] as $data){
                    page()->addEstigma('contenido_bl'.$data['bodega'].'_'.$data['linea'].'_'.$data['proveedor'], array('SQL', $cache['bodega_'.$data['bodega']."_".$data['linea']."_".$data['proveedor']]));
                }

                break;
            case "provmar":
                template()->addTemplateBit('contenido_reporte', 'report/reporteKardexResumen.html');
                page()->addEstigma('titulo_reporte', "Kardex - Resumen");
                page()->addEstigma('contenido', array('SQL', $cache[0]));
                break;
        }


        page()->addEstigma('razon_social', $system->razon_social);
        page()->addEstigma('nombre_empresa', $system->razon_social);
        page()->addEstigma('nit', $system->nit);
        page()->addEstigma('nrc', $system->nrc);
        page()->addEstigma('telefono', $system->telefono);
        page()->addEstigma('direccion', $system->direccion);
        page()->addEstigma('usuario', Session::singleton()->getUser());
        page()->addEstigma('fecha', date("d/m/Y"));
        page()->addEstigma('fecha_1', $fecha);
        page()->addEstigma('fecha_2', $fecha2);
        page()->addEstigma('hora', date("h:i:s A"));

        template()->parseOutput();
        //template()->parseExtras();

        //$html2pdf = new HTML2PDF('P', 'letter', 'es');
        //$html2pdf->WriteHTML(page()->getContent());
        //$html2pdf->Output('inventario.pdf');
        $fp = fopen(APP_PATH."/temp/".Session::singleton()->getUser()."_kardex.html", "w");
        fputs($fp, page()->getContent());
        fclose($fp);
        $str = APP_PATH.'common\plugins\phantomjs\bin\phantomjs '.APP_PATH.'static\js\html2pdf.js file:///'.APP_PATH.'temp\\'.Session::singleton()->getUser().'_kardex.html '.APP_PATH.'temp\\'.Session::singleton()->getUser().'_kardex.pdf';
        system($str);
        $file = APP_PATH.'temp\\'.Session::singleton()->getUser().'_kardex.pdf';
        $filename = Session::singleton()->getUser().'_kardex.pdf';

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');

        @readfile($file);
    }

    public function catalogos($user) {
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "catalogos", Session::singleton()->getUser())){
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Catalogos');
            page()->addEstigma("username", $user);
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            page()->addEstigma("TITULO", 'Administración de campañas');
            template()->addTemplateBit('content', 'inventario/catalogo.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }else{

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function resumenGeneralProducto($user) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Productos');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Consulta de productos registrados');
        template()->addTemplateBit('content', 'inventario/resumenGeneralProducto.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function resumenGeneralStock($user) {
        template()->buildFromTemplates('template_table.html');
        page()->setTitle('Stock');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/stock_documentos');
        page()->addEstigma("TITULO", 'Resumen general');
        template()->addTemplateBit('content', 'inventario/resumenGeneralStock.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function exportarEstadoBodegaImg($cache, $user) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('inventario/bodegaExportImg.html');
        page()->addEstigma('resumen', array('SQL', $cache[0]));
        page()->addEstigma('usuario', $user);
        page()->addEstigma('resource', APP_PATH);
        page()->addEstigma('fecha', date("d/m/y - h:m:s a"));
        template()->parseOutput();
        template()->parseExtras();
        $html2pdf = new HTML2PDF('P', 'A4', 'es');
        $html2pdf->WriteHTML(page()->getContent());
        $html2pdf->Output('exemple.pdf');
    }

    public function exportarResumenProducto($cache, $user) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('inventario/productoExport.html');
        page()->addEstigma('resumen', array('SQL', $cache[0]));
        page()->addEstigma('usuario', $user);
        page()->addEstigma('fecha', date("d/m/y - h:m:s a"));
        template()->parseOutput();
        template()->parseExtras();
        $html2pdf = new HTML2PDF('P', 'A4', 'es');
        $html2pdf->WriteHTML(page()->getContent());
        $html2pdf->Output('exemple.pdf');
    }

    public function exportarEstadoBodega($cache, $user) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('inventario/bodegaExport.html');
        page()->addEstigma('resumen', array('SQL', $cache[0]));
        page()->addEstigma('usuario', $user);
        page()->addEstigma('fecha', date("d/m/y - h:m:s a"));
        template()->parseOutput();
        template()->parseExtras();
        $html2pdf = new HTML2PDF('P', 'A4', 'es');
        $html2pdf->WriteHTML(page()->getContent());
        $html2pdf->Output('exemple.pdf');
    }

    public function exportarHistorial($cache, $user) {
        require_once(APP_PATH . 'common/plugins/sigma/demos/export_php/html2pdf/html2pdf.class.php');
        template()->buildFromTemplates('inventario/historialExport.html');
        page()->addEstigma('historial', array('SQL', $cache[0]));
        page()->addEstigma('usuario', $user);
        page()->addEstigma('fecha', date("d/m/y - h:m:s a"));
        template()->parseOutput();
        template()->parseExtras();
        $html2pdf = new HTML2PDF('P', 'A4', 'es');
        $html2pdf->WriteHTML(page()->getContent());
        $html2pdf->Output('exemple.pdf');
    }

    public function inventarioHistorial($user, $cache, $pag) {
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "inventarioHistorial", Session::singleton()->getUser())){
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Historial');
            page()->addEstigma("username", $user);
            page()->addEstigma("paginacion", $pag);
            page()->addEstigma("modulo", "inventario");
            page()->addEstigma("back_url", '/inventario/inventario/nuevo_producto');
            page()->addEstigma("TITULO", 'Historial de documentos');
            template()->addTemplateBit('content', 'inventario/historial.html');
            page()->addEstigma('historial', array('SQL', $cache[0]));
            page()->addEstigma('1', 'Aplicado');
            page()->addEstigma('0', 'Pendiente');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }else{
            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function stockHistorial($user, $cache, $pag) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Historial');
        page()->addEstigma("username", $user);
        page()->addEstigma("paginacion", $pag);
        page()->addEstigma("modulo", "stock");
        page()->addEstigma("back_url", '/inventario/inventario/stock_documentos');
        page()->addEstigma("TITULO", 'Historial de eventos');
        template()->addTemplateBit('content', 'inventario/historial.html');
        page()->addEstigma('historial', array('SQL', $cache[0]));
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function passProveedor() {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Proveedores');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/modulo/listar');
        page()->addEstigma("TITULO", 'Acceso restringido');
        template()->addTemplateBit('content', 'inventario/verificar.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function passPrecio() {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Cambios');
        page()->addEstigma("username", Session::singleton()->getUser());
        page()->addEstigma("back_url", '/inventario/inventario/nuevo_producto');
        page()->addEstigma("TITULO", 'Acceso restringido');
        template()->addTemplateBit('content', 'inventario/verificarPrecio.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function mantenimiento_de_colores($user) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Regitro de colores');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Colores');
        template()->addTemplateBit('content', 'inventario/colores.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function mantenimiento_de_lineas($user) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Mantenimiento de lineas');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Lineas');
        template()->addTemplateBit('content', 'inventario/lineas.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function mantenimiento_de_marcas($user) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Mantenimiento de marcas');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Marcas');
        template()->addTemplateBit('content', 'inventario/marcas.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function mantenimiento_de_proveedores($user, $paises) {
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "proveedores", Session::singleton()->getUser())){
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Mantenimiento de proveedores');
            page()->addEstigma("username", $user);
            page()->addEstigma("paises", array('SQL', $paises));
            page()->addEstigma("back_url", '/inventario/inventario/destroyProv');
            page()->addEstigma("TITULO", 'Proveedores');
            template()->addTemplateBit('content', 'inventario/proveedores.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        }else{

            HttpHandler::redirect('/inventario/error/e403');
        }
    }

    public function mantenimiento_de_generos($user) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->setTitle('Mantenimiento de generos');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("TITULO", 'Generos');
        template()->addTemplateBit('content', 'inventario/generos.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function historial($cache) {
        template()->buildFromTemplates('inventario/historial.html');
        page()->addEstigma('historial', array('SQL', $cache[0]));
        template()->parseOutput();
        print page()->getContent();
    }

    public function actualizarStock($user, $doc) {
        template()->buildFromTemplates('template_table.html');
        page()->setTitle('Control de stock');
        page()->addEstigma('documento', $doc);
        template()->addTemplateBit('content', 'inventario/stock.html');
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        page()->addEstigma("username", $user);
        page()->addEstigma("TITULO", 'Stock');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function administrar($doc, $user) {
        template()->buildFromTemplates('template.html');
        page()->setTitle('Administracion de documento - ' . $doc);
        page()->addEstigma("TITULO", 'Administracion');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/seleccion_documento?documento=' . $doc);
        template()->addTemplateBit('content', 'inventario/administracion.html');
        page()->addEstigma("documento", $doc);
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function vista_documento($user, $doc) {
        template()->buildFromTemplates('template.html');
        page()->setTitle('Control de Inventario - ' . $user);
        page()->addEstigma("TITULO", 'Opciones');
        page()->addEstigma("username", $user);
        page()->addEstigma("back_url", '/inventario/inventario/nuevo_producto');
        template()->addTemplateBit('content', 'inventario/doc_inventario.html');
        template()->addTemplateBit('footer', 'footer.html');
        page()->addEstigma("usuario", $user);
        page()->addEstigma("documento", $doc);
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

    public function abrir_documento($user, $cache) {
        template()->buildFromTemplates('inventario/doc_existentes.html');
        page()->addEstigma("usuario", $user);
        page()->addEstigma("documentos", array('SQL', $cache[0]));
        template()->parseOutput();
        print page()->getContent();
    }

    public function mantenimiento_de_productos($cache) {
        template()->buildFromTemplates('template_table.html');
        page()->setTitle('Productos');
        page()->addEstigma("menu", '');
        template()->addTemplateBit('content', 'inventario/productos.html');
        template()->addTemplateBit('footer', 'footer.html');
        page()->addEstigma('linea', array('SQL', $cache[0]));
        page()->addEstigma('marca', array('SQL', $cache[1]));
        page()->addEstigma('proveedor', array('SQL', $cache[2]));
        page()->addEstigma('color', array('SQL', $cache[3]));
        page()->addEstigma('genero', array('SQL', $cache[4]));
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }


    public function nuevo_documento($user) {
        template()->buildFromTemplates('inventario/nuevo_documento.html');
        page()->addEstigma("usuario", $user);
        template()->parseOutput();
        print page()->getContent();
    }

    public function stock_documentos($user, $cache) {
        template()->buildFromTemplates('template_nofixed.html');
        page()->addEstigma("TITULO", 'Stock');
        page()->addEstigma("username", $user);
        page()->addEstigma("documentos", array('SQL', $cache[0]));
        page()->addEstigma("back_url", '/inventario/inventario/principal');
        template()->addTemplateBit('content', 'inventario/documentoStock.html');
        template()->parseOutput();
        template()->parseExtras();
        print page()->getContent();
    }

}

?>
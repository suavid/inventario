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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { return ""; }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
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
        } else { HttpHandler::redirect('/inventario/error/e403'); }
    }

    public function catalogos($user) 
    {
        import('scripts.secure');
        if(verifyAccess("inventario", "inventario", "catalogos", Session::singleton()->getUser()))
        {
            template()->buildFromTemplates('template_nofixed.html');
            page()->setTitle('Catalogos');
            page()->addEstigma("username", $user);
            page()->addEstigma("back_url", '/inventario/inventario/principal');
            page()->addEstigma("TITULO", 'Administración de campañas');
            template()->addTemplateBit('content', 'inventario/catalogo.html');
            template()->parseOutput();
            template()->parseExtras();
            print page()->getContent();
        } else { HttpHandler::redirect('/inventario/error/e403'); }
    }
}
?>
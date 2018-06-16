<?php

import('mdl.view.inventario');
import('mdl.model.inventario');

class inventarioController extends controller
{
    private static $SOAP_OPTIONS = array("trace" => 1, "exception" => true, "soap_version"=>SOAP_1_1);

    private function getDateFromString($String)
    {
        $DateArray = explode(".", $String);
        return $DateArray[2] . "-" . $DateArray[1] . "-" . $DateArray[0];
    }

    public function principal()
    {
        $this->ValidateSession();
        $this->view->principal();
    }

    private function ValidateSession()
    {
        if (!Session::ValidateSession())
            HttpHandler::redirect(DEFAULT_DIR);
    }

    public function segmentacion()
    {
        $this->ValidateSession();
        $this->view->segmentacion();
    }

    public function ObtenerCategorias()
    {
        $this->ValidateSession();
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array('username'=>Session::singleton()->getUser());
        $result = $client->VerCategorias($params);

        echo  $result->{"VerCategoriasResult"};
    }

    public function ObtenerBanner()
    {
        $this->ValidateSession();
        if(isset($_POST) && !empty($_POST))
        {
            $modulo = $_POST['modulo'];
            $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
            $params = array('modulo'=>$modulo, 'username'=>Session::singleton()->getUser());
            $result = $client->VerMensajesBienvenida($params);

            echo  $result->{"VerMensajesBienvenidaResult"};
        }
    }

    public function ObtenerInformacionDelSistema()
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array('username'=>Session::singleton()->getUser());
        $result = $client->GetOrganizationInformation($params);

        echo  $result->{"GetOrganizationInformationResult"};
    }

    public function ObtenerFormularioCategoria()
    {
        $this->ValidateSession();
        if(isset($_POST) && !empty($_POST))
        {
            $tituloFormulario = (isset($_POST['tituloFormulario']))?$_POST['tituloFormulario']:"";
            $id = (isset($_POST['id']))?$_POST['id']:0;

            echo json_encode(array('html'=>$this->view->ObtenerFormularioCategoria($id,$tituloFormulario)));
        }
        else
        {
            echo json_encode(array('html'=>""));
        }
    }

    public function ObtenerDetalleCategoria($idGrupo)
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->VerDetalleCategoria(array("id"=>$idGrupo));
        $data = json_decode($result->{"VerDetalleCategoriaResult"});

        $ret = "{data:" . $result->{"VerDetalleCategoriaResult"} . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . count($data) . "},\n";
        $ret .= "recordType : 'object'}";

        echo  $ret;
    }

    public function ObtenerCategoriaEspecifica($idGrupo)
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->VerDetalleCategoria(array("id"=>$idGrupo));

        echo  $result->{"VerDetalleCategoriaResult"};
    }

    public function ObtenerBodegas()
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array("tipo_vista"=>0, "username"=>Session::singleton()->getUser());
        $result = $client->ObtenerBodegas($params);
        $data = json_decode($result->{"ObtenerBodegasResult"});

        $ret = "{data:" . $result->{"ObtenerBodegasResult"} . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . count($data) . "},\n";
        $ret .= "recordType : 'object'}";

        echo  $ret;
    }

    public function ListaBodegas()
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array("tipo_vista"=>0, "username"=>Session::singleton()->getUser());
        $result = $client->ObtenerBodegas($params);
        echo $result->{"ObtenerBodegasResult"};
    }

    public function ObtenerCatalogos()
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array('username'=>Session::singleton()->getUser());
        $result = $client->ListadoCatalogos($params);
        $data = json_decode($result->{"ListadoCatalogosResult"});

        $ret = "{data:" . $result->{"ListadoCatalogosResult"} . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . count($data) . "},\n";
        $ret .= "recordType : 'object'}";

        echo  $ret;
    }

    public function HistorialTraslados()
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array("username"=>Session::singleton()->getUser());
        $result = $client->ObtenerHistorialTransacciones($params);
        $data = json_decode($result->{"ObtenerHistorialTransaccionesResult"});

        $ret = "{data:" . $result->{"ObtenerHistorialTransaccionesResult"} . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . count($data) . "},\n";
        $ret .= "recordType : 'object'}";

        echo  $ret;
    }

    public function ObtenerDocumentoDetalle($idDocumento)
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->VerDocumentoDetalle(array("id"=>$idDocumento));
        $data = json_decode($result->{"VerDocumentoDetalleResult"});

        $ret = "{data:" . $result->{"VerDocumentoDetalleResult"} . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . count($data) . "},\n";
        $ret .= "recordType : 'object'}";

        echo  $ret;
    }

    public function ObtenerListaDeCatalogos()
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array("username"=>Session::singleton()->getUser());
        $result = $client->ListadoCatalogos($params);

        echo $result->{"ListadoCatalogosResult"};
    }

    public function ObtenerTipoTransacciones()
    {
        $this->ValidateSession();

        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array("username"=>Session::singleton()->getUser());
        $result = $client->ObtenerTipoTransacciones($params);

        echo $result->{"ObtenerTipoTransaccionesResult"};
    }

    public function GuardarNuevaCategoriaEspecifica()
    {
        $this->ValidateSession();
        if(isset($_POST) && !empty($_POST["id_grupo"]) && !empty($_POST["nombre"]))
        {
            $idGrupo = $_POST["id_grupo"];
            $nombre = $_POST["nombre"];
            $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
            $result = array("success"=>false, "message"=>"");

            try
            {
                $client->InsertarDetalleCategoria(array(
                        "idCategoria"=>$idGrupo
                    ,   "descripcion"=>$nombre
                    ,   "usuario"=>Session::singleton()->getUser()
                ));

                $result["success"] = true;
                $result["message"] = "OK";
            }
            catch(Exception $e)
            {
                $result["message"] = $e->getMessage();
            }

            echo json_encode($result);
        }
    }

    public function ObtenerEmpleados()
    {
        $this->ValidateSession();
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array("username"=>Session::singleton()->getUser());
        $result = $client->ObtenerEmpleados($params);

        echo  $result->{"ObtenerEmpleadosResult"};
    }

    public function ObtenerProveedores()
    {
        $this->ValidateSession();
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $params = array("username"=>Session::singleton()->getUser());
        $result = $client->ObtenerListadoProveedores($params);
        echo  $result->{"ObtenerListadoProveedoresResult"};
    }

    public function GuardarBodega()
    {
        $this->ValidateSession();

        if(isset($_POST) && !empty($_POST))
        {
            $nombre = $_POST["nombre"];
            $encargado = $_POST["encargado"];
            $descripcion = $_POST["descripcion"];
            $tiene_stock = ($_POST["manejaStock"])? "SI":"NO";
            $reutilizar_id = $_POST["reutilizarCorrelativos"];

            $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
            $result = array("success"=>false, "message"=>"");

            try
            {
                $client->InsertarBodega(array(
                        "nombre"=>$nombre
                    ,   "encargado"=>$encargado
                    ,   "descripcion"=>$descripcion
                    ,   "tiene_stock"=>$tiene_stock
                    ,   "reutilizar_id"=>$reutilizar_id
                    ,   "username" => Session::singleton()->getUser()
                ));

                $result["success"] = true;
                $result["message"] = "OK";
            }
            catch(Exception $e)
            {
                $result["message"] = $e->getMessage();
            }

            echo json_encode($result);
        }
    }

    public function bodegas()
    {
        $this->ValidateSession();
        $this->view->mantenimiento_de_bodegas(Session::getUser());
    }

    public function nuevo_producto()
    {
        $this->ValidateSession();
        $usuario = Session::getUser();
        $this->view->nuevo_producto($usuario);
    }

    public function cambiarPrecios()
    {
        $this->ValidateSession();
        $this->view->cambiarPrecios(Session::getUser());
    }

    public function actualizarFoto()
    {
        $this->ValidateSession();
        $this->view->actualizarFoto();
    }

    public function actualizarFotoProducto()
    {
        $this->ValidateSession();

        if(isset($_POST['linea']) && !empty($_POST['linea']) && isset($_POST['estilo']) && !empty($_POST['estilo']))
        {
            upload_image(APP_PATH.'static/img/productos', 'archivo', $_POST['linea'].'_'.$_POST['estilo'].".jpg");

            httpHandler::redirect('/inventario/inventario/actualizarFoto?success=true');
        }
        else
        {
            httpHandler::redirect('/inventario/inventario/actualizarFoto?success=false');
        }
    }

    public function productosSugeridos()
    {
        $this->ValidateSession();
        $this->view->productosSugeridos();
    }

    public function traslados()
    {
        $this->ValidateSession();
        $this->view->traslados(Session::getUser());
    }

    public function salvarCatalogo()
    {
        $this->ValidateSession();
        $data = $_POST;

        $data['inicio'] = $this->getDateFromString($_POST['inicio']);
        $data['final'] = $this->getDateFromString($_POST['final']);

        $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);

        try
        {
            $result = $client->InsertarCatalogo(array(
                    "nombre"=>$data["nombre"]
                ,   "descripcion"=>$data["descripcion"]
                ,   "inicio"=>$data["inicio"]
                ,   "final"=>$data["final"]
                ,   "username"=>Session::singleton()->getUser()
            ));

            $data = json_decode($result->{"InsertarCatalogoResult"});

            upload_image(APP_PATH . 'static/img/catalogo', 'archivo', $data[0]->{"ID"}.".jpg");

            HttpHandler::redirect('/inventario/inventario/catalogos?result=200');
        }
        catch(Exception $e)
        {
            $result["message"] = $e->getMessage();
            HttpHandler::redirect('/inventario/inventario/catalogos?result=500');
        }
    }
    
    public function ObtenerDocumentosSinAplicar()
    {
        $this->ValidateSession();
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->ObtenerDocumentosSinAplicar(array("username"=>Session::getUser()));

        echo  $result->{"ObtenerDocumentosSinAplicarResult"};
    }

    public function GuardarDocumento()
    {
        $this->ValidateSession();
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->InsertarDocumento(array("propietario"=>Session::singleton()->getUser()));

        echo  $result->{"InsertarDocumentoResult"};
    }

    public function doc_productos()
    {
        $this->ValidateSession();
        $usuario = Session::singleton()->getUser();
        $doc = (isset($_GET) && !empty($_GET)) ? $_GET['documento'] : 0;
        $sn = (isset($_GET) && !empty($_GET)) ? $_GET['serialnumber'] : 0;
        $this->view->doc_mantenimiento_de_productos($doc, $usuario, $sn);
    }

    public function InsertarProducto()
    {
        $this->ValidateSession();
        $data = $_POST;

        $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);

        try
        {
            $result = $client->InsertarDocumentoDetalle(array(
                    "id_documento"=>$data["documento"]
                    , "estilo"=>$data["estilo"]
                    , "codigo_origen"=>$data["codigo_origen"]
                    , "descripcion"=>$data["descripcion"]
                    , "proveedor"=>$data["proveedor"]
                    , "catalogo"=>$data["catalogo"]
                    , "n_pagina"=>$data["n_pagina"]
                    , "minimo_stock"=>0
                    , "corrida_a"=>$data["corridaA"]
                    , "corrida_b"=>$data["corridaB"]
                    , "fraccion_corrida"=>$data["fraccionCorrida"]
                    , "categorias_especificas"=>$data["categoriasArr"]
                    , "observacion"=>$data["observaciones"]
                    , "nota"=>$data["notas"]
            ));

            $data = json_decode($result->{"InsertarDocumentoDetalleResult"});

        }
        catch(Exception $e)
        {
            $result["message"] = $e->getMessage();
        }

        echo json_encode($result);
    }

    public function AplicarDocumento()
    {
        $this->ValidateSession();
        $data = $_POST;

        $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);

        try
        {
            $result = $client->AplicarDocumento(array(
                    "id"=>$data["id"]
            ));

            $data = json_decode($result->{"AplicarDocumentoResult"});

        }
        catch(Exception $e)
        {
            $result["message"] = $e->getMessage();
        }

        echo json_encode($result);
    }

    public function InsertarTraslado()
    {
        $this->ValidateSession();
        $data = $_POST;

        $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);

        try
        {
            $result = $client->InsertarTraslado(array(
                    "proveedor_origen" => $data["proveedorOrigen"]
                    , "proveedor_nacional" => $data["proveedorNacional"]
                    , "bodega_origen" => $data["bodegaOrigen"]
                    , "bodega_destino" => $data["bodegaDestino"]
                    , "concepto" => $data["conceptoTransaccion"]
                    , "transaccion" => $data["tipoTransaccion"]
                    , "usuario" => Session::singleton()->getUser()
                    , "cliente" => $data["cliente"]
                    , "referencia_retaceo" => $data["hojaRetaceo"]
            ));

            $data = json_decode($result->{"InsertarTrasladoResult"});

        }
        catch(Exception $e)
        {
            $result["message"] = $e->getMessage();
        }

        echo json_encode($result);
     }

     public function detalle_traslado()
     {
        $this->ValidateSession();
        $id = $_GET['id'];
        $this->view->detalle_traslado(Session::getUser(), $id);
     }

    public function VerTraslado()
    {
        $this->ValidateSession();
        $data = $_POST;
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->VerTraslado(array("id"=>$data['id']));
        echo  $result->{"VerTrasladoResult"};
    }

    public function CargarEstadoInventario()
    {
        $this->ValidateSession();
        $_data = $_POST;
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->VerEstadoInventario(array("proveedor"=>$_data["proveedor"], "linea"=>$_data["linea"], "estilo"=>$_data["estilo"], "color"=>$_data["color"], "talla"=>null, "bodega_origen"=>$_data["bodega_origen"], "bodega_destino"=>$_data["bodega_destino"], "cod"=>$_data["cod"], "username"=>Session::singleton()->getUser()));
        $data = json_decode($result->{"VerEstadoInventarioResult"});
        $ret = "{data:" . $result->{"VerEstadoInventarioResult"} . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . count($data) . "},\n";
        $ret .= "recordType : 'object'}";

        echo  $ret;

    }

    public function salvarTrasladoDetalle()
    {
        $this->ValidateSession();

        $json = json_decode(stripslashes($_POST["productos"]));

        $result = array();

        $result["error"] = false;

        $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);

        foreach ($json as $item)
        {
            $estilo = $item->{"estilo"};
            $linea = $item->{"linea"};
            $color = $item->{"color"};
            $talla = $item->{"talla"};
            $cantidad = $item->{"cantidad"};
            $costo = $item->{"costo"};
            $id_ref = $item->{"id_ref"};

            try
            {
                $result = $client->InsertarDetalleTraslado(array(
                        "estilo"=>$estilo, "linea"=>$linea, "talla"=>$talla, "color"=>$color, "id_ref"=>$id_ref, "cantidad"=>$cantidad, "costo"=>$costo
                ));

            }
            catch(Exception $e)
            {
                $result["error"] = true;
                $result["message"] = $e->getMessage();
            }

        }

        echo json_encode($result);
    }

    public function VerDetalleTraslado()
    {
        $this->ValidateSession();
        $_data = $_POST;
        $client  = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);
        $result = $client->VerTrasladoDetalle(array("id"=>$_data["id"]));
        $data = json_decode($result->{"VerTrasladoDetalleResult"});

        $ret = "{data:" . $result->{"VerTrasladoDetalleResult"} . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . count($data) . "},\n";
        $ret .= "recordType : 'object'}";

        echo  $ret;
    }

    public function EliminarDetalleTraslado()
    {
        $this->ValidateSession();
        $data = $_POST;

        $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);

        try
        {
            $result = $client->EliminarDetalleTraslado(array(
                    "id"=>$data["id"]
            ));

            $data = json_decode($result->{"EliminarDetalleTrasladoResult"});

        }
        catch(Exception $e)
        {
            $result["message"] = $e->getMessage();
        }

        echo json_encode($result);
    }

    public function ProcesarTraslado()
    {
        $this->ValidateSession();
        $data = $_POST;

        $client = new SoapClient(SERVICE_URL, self::$SOAP_OPTIONS);

        try
        {
            $result = $client->ProcesarTraslado(array(
                    "id"=>$data["id"]
            ));

            $data = json_decode($result->{"ProcesarTrasladoResult"});

        }
        catch(Exception $e)
        {
            $result["message"] = $e->getMessage();
        }

        echo json_encode($result);
    }

    public function catalogos()
    {
        $this->ValidateSession();
        $this->view->catalogos(Session::singleton()->getUser());
    }
}

?>
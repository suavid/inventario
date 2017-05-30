<?php

import('mdl.view.login');
import('mdl.model.login');

class LoginController extends controller 
{
    // mostrar formulario de inicio de sesion
    public function form() 
    {
        // si no hay sesion activa mostrar formulario de inicio de sesion
        if (!Session::singleton()->ValidateSession()) 
        {
            $this->view->show_form();
        } else 
        {
            // si hay una sesion activa redirigir a la pagina principal del modulo
            HttpHandler::redirect('/'.MODULE.'/inventario/principal');
        }
    }

    // validar credenciales de acceso
    public function login() 
    {
        // verificar que el request provenga de un formulario
        if (empty($_POST)) 
        {
            HttpHandler::redirect('/'.MODULE.'/login/form');
        } 
        else 
        {
            // creacion de cliente soap
            $client  = new SoapClient(SERVICE_URL, BM::getSetting("SOAP_OPTIONS"));
            // obtener las credenciales proporcionadas
            $usuario = (isset($_POST['usuario']))? $_POST['usuario']:'';
            $clave   = (isset($_POST['clave']))? cifrar_RIJNDAEL_256($_POST['clave']):'';     
            // establecer parametros para llamada del servicio
            $params = array(
                'Usuario' => $usuario,
                'Clave'   => $clave
            );
            // llamar al metodo de autenticacion
            $result = $client->Autenticar($params); 
            // validar resultado de autenticacion, 0 == exitoso
            if( $result->{"AutenticarResult"} == 0 )
            {
                // crear sesion del usuario
                 Session::singleton()->NewSession($usuario);
                 HttpHandler::redirect('/'.MODULE.'/login/form');
            } 
            else
            {
                HttpHandler::redirect('/'.MODULE.'/login/form?error_id=2');
            }
        }
    }
}

?>
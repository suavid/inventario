<?php

function verifyAccess($system, $view, $resource, $user){
	// creacion de cliente soap
    $client  = new SoapClient(SERVICE_URL, array("trace" => 1, "exception" => true, "soap_version"=>SOAP_1_1));
    // establecer parametros para llamada del servicio
    $params = array(
    	'Sistema' => $system,
        'Vista'   => $view,
		'Recurso' => $resource,
		'Usuario' => $user
    );

    $result = $client->ValidarAcceso($params);

    if( $result->{"ValidarAccesoResult"} == 0 )
    {
       return true;
    }
    else
    {
    	return false;
    }
}

?>
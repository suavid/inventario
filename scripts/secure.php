<?php

function verifyAccess($system, $view, $resource, $user){
	// creacion de cliente soap
    $client  = new SoapClient(SERVICE_URL, BM::getSetting("SOAP_OPTIONS"));     
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
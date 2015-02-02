<?php

class documento_productoModel extends object {
    public function getDocument($estilo){
    	$query = "SELECT numero_documento FROM documento_producto WHERE estilo=$estilo";

    	data_model()->executeQuery($query);

    	$res = data_model()->getResult()->fetch_assoc();

    	return $res['numero_documento']; 
    }
}

?>
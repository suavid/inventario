<?php

class talla_productoModel extends object {
    
    public function tallas($linea, $estilo, $color){
    	$query = "SELECT * FROM talla_producto WHERE linea = $linea AND talla_estilo_producto='{$estilo}' AND color=$color";
    	data_model()->executeQuery($query);
        $resp = array();
        while($res = data_model()->getResult()->fetch_assoc()){
            $resp[] = $res;
        } 
        
        return $resp;
    }
}

?>
<?php

class suelaModel extends object {
    public function ultimo_en_blanco() {
        $query = "select id from suela WHERE nombre is null or nombre ='' ";
        data_model()->executeQuery($query);
        if (data_model()->getNumRows() > 0) {
            $ret = data_model()->getResult()->fetch_assoc();
            return $ret['id'];
        } else {
            return -1;
        }
    }
}

?>
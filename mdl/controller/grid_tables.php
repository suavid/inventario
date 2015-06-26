<?php

/**
 * 
 * Esta clase provee una interfaz para unificar todas las peticiones de carga
 * y actualizacion de datos para los grid presentes en la aplicacion y reducir la 
 * carga de otros ficheros
 * 
 */
import('mdl.view.grid_tables');
import('mdl.model.grid_tables');

class grid_tablesController extends controller {

    public function test_resource() {
        echo "This resource works!";
    }

    /* grid para resumen de bodegas omitiendo espacios en blanco */

    public function bodega_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (bodega.nombre LIKE '%{$term}') OR (bodega.nombre LIKE '{$term}%') OR (bodega.nombre LIKE '%{$term}%')  OR bodega.id = '{$term}' OR  empleado.usuario = '{$term}' )";
        }
        
        $sql = "select count(DISTINCT bodega.id) as cnt from bodega LEFT JOIN empleado on encargado = empleado.id_datos WHERE nombre is not null AND nombre !='' $condQ GROUP BY bodega.id";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = mysqli_num_rows($handle);
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * ,bodega.id as id from bodega LEFT JOIN empleado on encargado = empleado.id_datos WHERE nombre is not null AND nombre !='' $condQ GROUP BY bodega.id limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    /* grid para resumen de colores omitiendo espacios en blanco */

    public function color_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        
        $sql = "select count(*) as cnt from color WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from color WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function reporteInventario_bodega() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "WHERE bodega.tiene_stock='si' ";
        
        $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
        $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
        if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
        }
        
        if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
        }
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
            if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
            
            
            
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea) $condQ GROUP BY bodega.id ORDER BY no DESC";
        
        //echo $sql;
        
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares, $costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea) $condQ GROUP BY bodega.id ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            
           // echo $sql;
            
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteInventario_linea() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "WHERE bodega.tiene_stock='si' ";
         
         $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
         $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
         if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
         }
        
         if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
         }
        
         if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
            
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT  count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea) $condQ GROUP BY bodega.id, linea.id ORDER BY no DESC";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT  linea.id, linea.nombre as nombre_linea, bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares, $costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea) $condQ GROUP BY bodega.id, linea.id ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteInventario_proveedor() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "WHERE bodega.tiene_stock='si' ";
         
          $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
        $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
        if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
        }
        
        if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
        }
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
            
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT  count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea) LEFT JOIN proveedor ON (producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id)  $condQ GROUP BY bodega.id, linea.id ORDER BY no DESC";
        
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT  proveedor.id, proveedor.nombre as nombre_proveedor, linea.id, linea.nombre as nombre_linea, bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares,$costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON (producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id)  $condQ GROUP BY bodega.id, linea.id ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteInventario_estilo() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "WHERE bodega.tiene_stock='si' ";
         
          $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
        $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
        if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
        }
        
        if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
        }
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }

            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT  count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON (producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id)  $condQ GROUP BY bodega.id, linea.id, articulo.estilo ORDER BY no DESC";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT  exi_costo_unitario as costo_unitario, articulo.estilo as estilo, proveedor.id, proveedor.nombre as nombre_proveedor, linea.id, linea.nombre as nombre_linea, bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares, $costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON (producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id)  $condQ GROUP BY bodega.id, linea.id, articulo.estilo ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteInventario_color() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "WHERE bodega.tiene_stock='si' ";
         
        $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
        $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
        if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
        }
        
        if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
        }
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }

            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON (producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN color on (articulo.color = color.id)  $condQ GROUP BY bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT  color.id, color.nombre as nombre_color, exi_costo_unitario as costo_unitario, articulo.estilo as estilo, proveedor.id, proveedor.nombre as nombre_proveedor, linea.id, linea.nombre as nombre_linea, bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares, $costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON ( producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN color on (articulo.color = color.id) $condQ GROUP BY bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteInventario_talla() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "WHERE bodega.tiene_stock='si' ";
         
          $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
        $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
        if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
        }
        
        if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
        }
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }

            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON ( producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN color on (articulo.color = color.id) $condQ GROUP BY articulo.talla, bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT  articulo.talla as talla,color.id, color.nombre as nombre_color, exi_costo_unitario as costo_unitario, articulo.estilo as estilo, proveedor.id, proveedor.nombre as nombre_proveedor, linea.id, linea.nombre as nombre_linea, bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares, $costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON ( producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN color on (articulo.color = color.id)  $condQ GROUP BY articulo.talla, bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteInventario_provmar() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "WHERE bodega.tiene_stock='si' ";
         
          $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
        $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
        if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
        }
        
        if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
        }
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
 
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON ( producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN marca on (marca.id =  producto.marca) LEFT JOIN color on (articulo.color = color.id) $condQ GROUP BY marca.id, articulo.talla, bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT  marca.id, marca.nombre as nombre_marca, articulo.talla as talla,color.id, color.nombre as nombre_color, exi_costo_unitario as costo_unitario, articulo.estilo as estilo, proveedor.id, proveedor.nombre as nombre_proveedor, linea.id, linea.nombre as nombre_linea, bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares, $costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON ( producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN marca on (marca.id =  producto.marca) LEFT JOIN color on (articulo.color = color.id) $condQ GROUP BY marca.id, articulo.talla, bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function reporteKardex_bodega() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
            if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
            
            
            
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "select no from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod order by proveedor,no ";
        
        //echo $sql;
        
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select proveedor.nombre as proveedor, proveedor.id as id_proveedor, fecha, numero as documento, transacciones.nombre as concepto, ent_cantidad as entradas, sal_cantidad as salidas, (select sum(ent_cantidad - sal_cantidad) from kardex k WHERE k.no <= kardex.no order by no) as saldo, bodega.nombre as bodega from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod order by proveedor,no  limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            
           // echo $sql;
            
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteKardex_linea() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "";
         
        
         if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
            
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "select proveedor.nombre as proveedor, proveedor.id as id_proveedor, fecha, numero as documento, transacciones.nombre as concepto, ent_cantidad as entradas, sal_cantidad as salidas, (select sum(ent_cantidad - sal_cantidad) from kardex k where k.nombre_proveedor = kardex.nombre_proveedor and k.no <= kardex.no order by no) as saldo, bodega.nombre as bodega from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod order by proveedor,no ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select proveedor.nombre as proveedor, proveedor.id as id_proveedor, fecha, numero as documento, transacciones.nombre as concepto, ent_cantidad as entradas, sal_cantidad as salidas, (select sum(ent_cantidad - sal_cantidad) from kardex k where k.nombre_proveedor = kardex.nombre_proveedor and k.no <= kardex.no order by no) as saldo, bodega.nombre as bodega from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod order by proveedor,no  limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteKardex_proveedor() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "";
         
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
            
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "select no from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod join linea on articulo.linea = linea.id order by proveedor,no ";
        
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select CONCAT(linea.id,' ',linea.nombre) as linea, linea.id as id_linea, articulo.estilo as estilo, proveedor.nombre as proveedor, proveedor.id as id_proveedor, fecha, numero as documento, transacciones.nombre as concepto, ent_cantidad as entradas, sal_cantidad as salidas, (select sum(ent_cantidad - sal_cantidad) from kardex k join articulo ar on ar.id = k.codigo where ar.linea = articulo.linea and ar.estilo = articulo.estilo and k.no <= kardex.no order by no) as saldo, bodega.nombre as bodega from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod join linea on articulo.linea = linea.id order by proveedor,no  limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteKardex_estilo() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "";
         
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }

            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "select no from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod join linea on articulo.linea = linea.id order by no, articulo.linea, articulo.estilo";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select CONCAT(linea.id,' ',linea.nombre) as linea, linea.id as id_linea,articulo.color, articulo.estilo as estilo, proveedor.nombre as proveedor, proveedor.id as id_proveedor, fecha, numero as documento, transacciones.nombre as concepto, ent_cantidad as entradas, sal_cantidad as salidas, (select sum(ent_cantidad - sal_cantidad) from kardex k join articulo ar on ar.id = k.codigo where ar.linea = articulo.linea and ar.estilo = articulo.estilo and articulo.color = ar.color and k.no <= kardex.no order by no) as saldo, bodega.nombre as bodega from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod join linea on articulo.linea = linea.id order by no, articulo.linea, articulo.estilo limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteKardex_color() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }

            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "select no from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod join linea on articulo.linea = linea.id order by no, articulo.linea, articulo.estilo ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select CONCAT(linea.id,' ',linea.nombre) as linea, linea.id as id_linea,articulo.color, articulo.estilo as estilo,articulo.talla, proveedor.nombre as proveedor, proveedor.id as id_proveedor, fecha, numero as documento, transacciones.nombre as concepto, ent_cantidad as entradas, sal_cantidad as salidas, (select sum(ent_cantidad - sal_cantidad) from kardex k join articulo ar on ar.id = k.codigo where ar.linea = articulo.linea and ar.estilo = articulo.estilo and articulo.color = ar.color and ar.talla = articulo.talla and k.no <= kardex.no order by no) as saldo, bodega.nombre as bodega from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join transacciones on transaccion=transacciones.cod join linea on articulo.linea = linea.id order by no, articulo.linea, articulo.estilo limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteKardex_talla() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
         $condQ = "WHERE bodega.tiene_stock='si' ";
         
          $costoQ = " (SUM(ent_costo_total)-SUM(sal_costo_total)) ";
        $precioQ = " (SUM(precio * ent_cantidad) - SUM( precio * sal_cantidad)) ";
        
        if(isset($_POST['pc'])&&!empty($_POST['pc'])){
                $pc = $_POST['pc'];
                
                if($pc == "no"){
                    $costoQ = "''";            
                }
        
        }
        
        if(isset($_POST['pv'])&&!empty($_POST['pv'])){
                $pv = $_POST['pv'];
                
                if($pv == "no"){
                    $precioQ = "''";            
                }
        
        }
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }

            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "SELECT count(*) as cnt FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON ( producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN color on (articulo.color = color.id) $condQ GROUP BY articulo.talla, bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "SELECT  articulo.talla as talla,color.id, color.nombre as nombre_color, exi_costo_unitario as costo_unitario, articulo.estilo as estilo, proveedor.id, proveedor.nombre as nombre_proveedor, linea.id, linea.nombre as nombre_linea, bodega.nombre as nombre_bodega, bodega, (SUM(ent_cantidad) - SUM(sal_cantidad)) as pares, $costoQ as total_costo, $precioQ as total_precio FROM kardex LEFT JOIN articulo ON codigo=articulo.id LEFT JOIN bodega ON bodega.id = bodega LEFT JOIN control_precio c ON (c.control_estilo = articulo.estilo AND c.linea = articulo.linea AND c.color = articulo.color AND c.talla = articulo.talla) LEFT JOIN producto ON (producto.estilo = articulo.estilo AND producto.linea = articulo.linea)  LEFT JOIN proveedor ON ( producto.proveedor = proveedor.id) LEFT JOIN linea on (articulo.linea = linea.id) LEFT JOIN color on (articulo.color = color.id)  $condQ GROUP BY articulo.talla, bodega.id, linea.id, color.id, articulo.estilo ORDER BY no DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function reporteKardex_provmar() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['filtros'])&&!empty($_POST['filtros'])){
            $filtros = $_POST['filtros'];
            $campos = explode(',', $filtros);
            
             if(isset($_POST['suprimir'])&&!empty($_POST['suprimir'])){
                $suprimir = $_POST['suprimir'];
                
                if($suprimir == "si"){
                    $condQ .= " HAVING ((SUM(ent_cantidad) - SUM(sal_cantidad)) > 0 ) ";            
                }
        
            }
            
            $condQ = " WHERE bodega.tiene_stock='si' AND ";
            
            if(isset($_POST['fecha'])&&!empty($_POST['fecha'])){
                $fecha = $_POST['fecha'];
                $condQ .= " kardex.fecha <= '{$fecha}' AND ";
            }
 
            $temp = array();
            
            foreach($campos as $filtro){
                $partes = explode(';', $filtro);
           
                $p1 = $partes[0];
                $p2 = $partes[1];
                
                $p1 = explode(':', $p1);
                $p2 = explode(':', $p2);
                
                $f1 = $p1[0];
                $v1 = $p1[1];
                
                $f2 = $p2[0];
                $v2 = $p2[1];
                
                $temp[] = " ( $f1 >= '{$v1}' AND $f2 <= '{$v2}' ) ";
            }
            
            $condQ.= implode(' AND ', $temp);
           
        }
        
        $sql = "select no from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join linea on linea.id = articulo.linea join transacciones on transaccion=transacciones.cod join control_precio on articulo.estilo = control_estilo and articulo.linea = control_precio.linea and articulo.color = control_precio.color and articulo.talla = control_precio.talla group by transacciones.cod order by kardex.tipo asc";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $handle->num_rows;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select transacciones.nombre as concepto, SUM(ent_cantidad) as entradas, SUM(ent_cantidad * costo) as costo_ent, SUM(sal_cantidad) as salidas, SUM(sal_cantidad * costo) as costo_sal from kardex join bodega on bodega=bodega.id join articulo on codigo = articulo.id join producto on producto.estilo = articulo.estilo and producto.linea = articulo.linea join proveedor on producto.proveedor = proveedor.id join linea on linea.id = articulo.linea join transacciones on transaccion=transacciones.cod join control_precio on articulo.estilo = control_estilo and articulo.linea = control_precio.linea and articulo.color = control_precio.color and articulo.talla = control_precio.talla group by transacciones.cod order by kardex.tipo asc limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function oferta_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from oferta";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from oferta order by fin desc limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function grid_kit_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from producto where linea = 0";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from producto inner join estado_bodega on producto.linea = estado_bodega.linea and producto.estilo = estado_bodega.estilo inner join control_precio on control_estilo = producto.estilo and control_precio.linea = producto.linea where producto.linea = 0 order by fecha_ingreso desc limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function grid_kit_elementos_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
         $kit = $_POST['kit'];
        $sql = "select count(*) as cnt from elemento_kit WHERE kit = '{$kit}'";
       
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from elemento_kit WHERE kit = '{$kit}' limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function productos_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt  from estado_bodega inner join producto on producto.estilo = estado_bodega.estilo and producto.linea = estado_bodega.linea inner join color on color.id = estado_bodega.color inner join control_precio on estado_bodega.estilo = control_estilo and estado_bodega.linea = control_precio.linea where bodega = 1 and estado_bodega.linea != 0 and stock > 0 and precio > 0 group by estado_bodega.estilo, estado_bodega.linea order by fecha_ingreso desc";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select descripcion, estado_bodega.linea, estado_bodega.estilo, estado_bodega.color, estado_bodega.talla, color.nombre as color_nombre, SUM(stock) as stock, precio  from estado_bodega inner join producto on producto.estilo = estado_bodega.estilo and producto.linea = estado_bodega.linea inner join color on color.id = estado_bodega.color inner join control_precio on estado_bodega.estilo = control_estilo and estado_bodega.linea = control_precio.linea where bodega = 1 and estado_bodega.linea != 0 and stock > 0 and precio > 0 group by estado_bodega.estilo, estado_bodega.linea order by fecha_ingreso desc limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    /* grid para resumen de lineas omitiendo espacios en blanco */

    public function linea_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from linea WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from linea WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function grupo_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from grupo WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from grupo WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function concepto_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from concepto WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from concepto WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function grid_retaceo_pendiente_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from hoja_retaceo where aplicada = 0";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from hoja_retaceo where aplicada = 0 limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function grid_retaceo_aplicado_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from hoja_retaceo where aplicada = 1";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from hoja_retaceo where aplicada = 1 limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function suela_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from suela WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from suela WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function tacon_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from tacon WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from tacon WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function ofertados_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from estado_bodega where bodega = 3";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from estado_bodega where bodega = 3 limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function genero_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from genero WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from genero WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function material_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from material WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from material WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function proveedor_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from proveedor WHERE nombre is not null AND nombre != ''";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from proveedor WHERE nombre is not null AND nombre != '' limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function catalogo_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        //to get how many records totally.
        $sql = "select count(*) as cnt from catalogo WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from catalogo WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function marca_grid_1() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        
        $condQ = "";
        
        if(isset($_POST['term'])&&!empty($_POST['term'])){
            $term = $_POST['term'];
            $condQ = " AND ( (nombre LIKE '%{$term}') OR (nombre LIKE '{$term}%') OR (nombre LIKE '%{$term}%')  OR id = '{$term}' )";
        }
        
        $sql = "select count(*) as cnt from marca WHERE nombre is not null AND nombre != '' $condQ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from marca WHERE nombre is not null AND nombre != '' $condQ limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }

    public function promocion_detalle($id_promocion) {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from promocion_producto WHERE id_promocion=$id_promocion";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
            $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
            $sql = "select * from promocion_producto WHERE id_promocion=$id_promocion limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
            $handle = mysqli_query(conManager::getConnection(), $sql);
            $retArray = array();
            while ($row = mysqli_fetch_object($handle)):
                $retArray[] = array_map('utf8_encode', (array) $row);
            endwhile;
            $data = json_encode($retArray);
            $ret = "{data:" . $data . ",\n";
            $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
            $ret .= "recordType : 'object'}";
            echo $ret;
        endif;
    }
    
    public function dsvdiah() {
        header('Content-type:text/javascript;charset=UTF-8');
        $json = json_decode(stripslashes($_POST["_gt_json"]));
        $pageNo = $json->{'pageInfo'}->{'pageNum'};
        $pageSize = 10; //10 rows per page
        //to get how many records totally.
        $sql = "select count(*) as cnt from cambio ";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $row = mysqli_fetch_object($handle);
        $totalRec = $row->cnt;
        //make sure pageNo is inbound
        if ($pageNo < 1 || $pageNo > ceil(($totalRec / $pageSize))):
        $pageNo = 1;
        endif;
        if ($json->{'action'} == 'load'):
        $sql = "select * from cambio ORDER BY id DESC limit " . ($pageNo - 1) * $pageSize . ", " . $pageSize . ";";
        $handle = mysqli_query(conManager::getConnection(), $sql);
        $retArray = array();
        while ($row = mysqli_fetch_object($handle)):
            $retArray[] = array_map('utf8_encode', (array) $row);
        endwhile;
        $data = json_encode($retArray);
        $ret = "{data:" . $data . ",\n";
        $ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
        $ret .= "recordType : 'object'}";
        echo $ret;
        endif;
    }

}

?>
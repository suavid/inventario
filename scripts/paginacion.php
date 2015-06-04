<?php

function paginar($numeroRegistros, $url) {
    $tamPag = 10; # cantidad de registros por pagina 
    if (!isset($_GET["pag"]) || empty($_GET["pag"])) {
        $pagina = 1; # pagina por default 
        $inicio = 1; # pagina de inicio
        $final = $tamPag; # ultima pagina
    } else {
        $pagina = (is_numeric($_GET["pag"]) && (intval($_GET["pag"]) == $_GET["pag"] )) ? $_GET["pag"] : 1; # obtiene pagina de la url
    }

    $limitInf = ($pagina - 1) * $tamPag;
    $numPags = ceil($numeroRegistros / $tamPag);
    if (!isset($pagina)) {
        $pagina = 1;
        $inicio = 1;
        $final = $tamPag;
    } else {
        $seccionActual = intval(($pagina - 1) / $tamPag);
        $inicio = ($seccionActual * $tamPag) + 1;

        if ($pagina < $numPags) {
            $final = $inicio + $tamPag - 1;
        } else {
            $final = $numPags;
        }

        if ($final > $numPags) {
            $final = $numPags;
        }
    }

    $paginacion_str = '<div class="pagination"><ul>';

    /* genera una cadea con html para establecer los enlaces de la paginacion */
    if ($pagina > 1) {
        $paginacion_str.= "<li class='prev'><a href='{$url}pag=" . ($pagina - 1) . "'>";
        $paginacion_str.= "<i class='icon-previous'></i>";
        $paginacion_str.= "</a></li>";
    }

    for ($i = $inicio; $i <= $final; $i++) {
        if ($i == $pagina) {
            $paginacion_str.= "<li class='active'><a>" . $i . "</a></li>";
        } else {
            $paginacion_str.= "<li><a href='{$url}pag=" . $i . "'>";
            $paginacion_str.= "&nbsp;" . $i . "</a> </li>";
        }
    }
    if ($pagina < $numPags) {
        $paginacion_str.= " <li class='next'><a href='{$url}pag=" . ($pagina + 1) . "'>";
        $paginacion_str.= "<i class='icon-next'></i></a></li>";
    }

    $paginacion_str.="</ul></div>";

    # devuelve la cedena, el limite inferior y el tamaño de pagina para la consulta sql
    return array($paginacion_str, $limitInf, $tamPag);
}

?>

<?php

function verifyAccess($system, $view, $resource, $user){
	$query = "SELECT id FROM acceso_sistema WHERE sistema='{$system}' AND vista='{$view}' AND recurso='{$resource}' AND usuario='{$user}'";
	data_model()->executeQuery($query);

	if(data_model()->getNumRows()>0)
		return true;

	return false;
}

?>
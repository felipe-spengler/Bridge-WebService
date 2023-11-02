<?php
	function conectarAoBanco() {
	$db_host = "localhost";
	$db_usuario = "id21439924_fe";
	$db_senha = "Fe123....";
	$db_banco = "id21439924_fe";

	$conn = mysqli_connect($db_host, $db_usuario, $db_senha, $db_banco);

	if (!$conn) {
    		return null; 
	} else {
		return $conn; 
	}
}
?>
<?php
	function conectarAoBanco() {
	$db_host = "localhost";
	$db_usuario = "root";
	$db_senha = "";
	$db_banco = "motel";

	$conn = mysqli_connect($db_host, $db_usuario, $db_senha, $db_banco);

	if (!$conn) {
    		return null; // Retorna null em caso de erro
	} else {
		return $conn; // Retorna a conexão em caso de sucesso
	}
}
?>
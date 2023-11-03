<?php

function conectarAoBanco() {
    $db_host = "aws.connect.psdb.cloud";
    $db_usuario = "g890co67x4gp1px9uo6e";
    $db_senha = "pscale_pw_UWqMPVPRGLLNZy7NdTOTvxNpiiUBnDetocEi31ja0QO";
    $db_banco = "motelintensy";

    $mysqli = mysqli_init();

    try {
        $mysqli->ssl_set(NULL, NULL, "cacert-2023-08-22.pem", NULL, NULL);
        $mysqli->real_connect($db_host, $db_usuario, $db_senha, $db_banco);
    } catch (mysqli_sql_exception $e) {
        echo "Erro na conexão " . $e->getMessage();
    }

    return $mysqli;
}
?>

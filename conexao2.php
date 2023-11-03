<?php

function conectarAoBanco() {
    $db_host = "aws.connect.psdb.cloud";
    $db_usuario = "8v5ngjmuj4u37sjzyibd";
    $db_senha = "pscale_pw_bCBwHapiuLmgxm7c7VF4KHAGnmRgSRy13fzA6IbBEPL";
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

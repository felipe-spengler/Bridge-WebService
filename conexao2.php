<?php

function conectarAoBanco() {
    $db_host = "aws.connect.psdb.cloud";
    $db_usuario = "dz4svoedaob0tppbtrl6";
    $db_senha = "pscale_pw_rWhN9K1HOT2SCUpA3iRzXp1vU4KPr5zbqq3SvflO64T";
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

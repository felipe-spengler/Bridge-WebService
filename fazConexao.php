<?php
header('Content-Type: text/html; charset=utf-8');
include 'conexao2.php';

$resposta = array();
$resposta["erro"] = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_POST['login']) && isset($_POST['senha'])) {
        $login = $_POST['login'];
        $senha = $_POST['senha'];

        $conexao = conectarAoBanco();

        if ($conexao === null) {
            $resposta["mensagem"] = "Erro na conexão com o banco de dados.";
        } else {
            $sql = "SELECT cargofuncionario FROM funcionario WHERE loginfuncionario = ? AND senhafuncionario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ss", $login, $senha);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                $row = $result->fetch_assoc();
                $resposta["erro"] = false;
                $resposta["cargo"] = $row["cargofuncionario"];
            } else {
                $resposta["mensagem"] = "Erro na query: " . $conexao->error;
            }

            $stmt->close();
            $conexao->close();
        }
    } else {
        $resposta["mensagem"] = "Dados de login e senha não foram recebidos corretamente.";
    }
} else {
    $resposta["mensagem"] = "Metodo incorreto.";
}

echo json_encode($resposta);
?>

<?php
header('Content-Type: text/html; charset=utf-8');
include 'conexao2.php';

$resposta = array();
$resposta["erro"] = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];
    
    $conexao = conectarAoBanco();
    
    if ($conexao === null) {
        $resposta["mensagem"] = "Erro na conexão com o banco de dados.";
    } else {
        $sql = "SELECT cargo FROM funcionario WHERE loginfuncionario = ? AND senhafuncionario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $login, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            $resposta["erro"] = false;
            $resposta["cargo"] = $row["cargo"];
        } else {
            $resposta["mensagem"] = "Nada a mostrar";
        }

        $stmt->close();
        $conexao->close();
    }
} else {
    $resposta["mensagem"] = "Login e senha são obrigatórios.";
}

echo json_encode($resposta);
?>

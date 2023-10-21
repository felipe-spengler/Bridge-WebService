<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexao.php';

$resposta = array();
$resposta["erro"] = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    if (!empty($login) && !empty($senha)) {
        $conexao = conectarAoBanco();
        if ($conexao === null) {
            $resposta["mensagem"] = "Erro na conexão com o banco de dados.";
        } else {
            $login = $conexao->real_escape_string($login); // Para evitar SQL injection
            $senha = $conexao->real_escape_string($senha);

            $sql = "SELECT cargo FROM funcionario WHERE loginfuncionario='$login' AND senhafuncionario='$senha'";
            $result = $conexao->query($sql);

            if ($result) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $resposta["erro"] = false;
                    $resposta["cargo"] = $row["cargo"];
                } else {
                    $resposta["mensagem"] = "Nada a mostrar";
                }
            } else {
                $resposta["mensagem"] = "Erro na consulta SQL: " . $conexao->error;
            }

            $conexao->close();
        }
    } else {
        $resposta["mensagem"] = "Login e senha são obrigatórios.";
    }
} else {
    $resposta["mensagem"] = "Erro no método de requisição.";
}

echo json_encode($resposta);
?>

<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexao2.php';

function uploadQuartos($link) {
    $quartos = array();
    $numeroQuarto = 0;
    $data;
    $status;
    $tipo;

    $consultaSQL = "SELECT * FROM quartos ORDER BY numeroquarto";
    $resultado = $link->query($consultaSQL);

    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $numeroQuarto = $row["numeroquarto"];
            $status = $row["atualquarto"];
            $data = $row["horastatus"];
            $tipo = consultaTipoQuarto($numeroQuarto, $link);

            $quarto = array(
                'numeroQuarto' => $numeroQuarto,
                'tipoQuarto' => $tipo,
                'statusQuarto' => $status,
                'horaStatus' => $data
            );

            $quartos[] = $quarto;
        }
    }

    return $quartos;
}

function consultaTipoQuarto($numeroQuarto, $link) {
    $consultaTipo = "SELECT tipoquarto FROM quartos WHERE numeroquarto = $numeroQuarto";
    $resultadoTipo = $link->query($consultaTipo);

    if ($resultadoTipo->num_rows > 0) {
        $rowTipo = $resultadoTipo->fetch_assoc();
        return $rowTipo["tipoquarto"];
    }

    return null; // ou algum valor padrão se não encontrar o tipo
}

$resposta["erro"] = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    $conexao = conectarAoBanco();

    if ($conexao === null) {
        $resposta["mensagem"] = "Erro na conexão com o banco de dados.";
    } else {
        $resposta["erro"] = false;
        $resposta["quartos"] = uploadQuartos($conexao);
        $conexao->close(); // Feche a conexão aqui, dentro do escopo onde foi aberto.
    }
} else {
    $resposta["mensagem"] = "Altere o método de requisição.";
    $resposta["mensagem2"] = $_SERVER['REQUEST_METHOD'];
}

echo json_encode($resposta);
?>

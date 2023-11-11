<?php
header('Content-Type: text/html; charset=utf-8');
include 'conexao2.php';

$resposta = array();
$resposta["erro"] = true;

function obterIdCaixaAberto($conexao) {
    $sql = "SELECT id, horaabre FROM caixa WHERE horafecha IS NULL";
    $result = $conexao->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return null;
    }
}

function somarValorQuartoEConsumo($conexao, $idCaixa) {
    $sql = "SELECT SUM(valorquarto + valorconsumo) AS total FROM registralocado WHERE idcaixaatual = $idCaixa";
    $result = $conexao->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0;
    }
}


function obterSaldoAbre($conexao, $idCaixa) {
    $sql = "SELECT saldoabre FROM caixa WHERE id = $idCaixa";
    $result = $conexao->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['saldoabre'];
    } else {
        return 0;
    }
}
function obterStatusEContarRegistros($conexao, &$resposta) {
    $statusContagem = array(
        "limpeza" => 0,
        "livre" => 0,
        "manutencao" => 0,
        "reserva" => 0,
        "ocupados" => 0
    );

    $sql = "SELECT atualquarto FROM status";
    $result = $conexao->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $atualquarto = $row['atualquarto']; 

            switch ($atualquarto) {
                case "limpeza":
                    $statusContagem["limpeza"]++;
                    break;
                case "livre":
                    $statusContagem["livre"]++;
                    break;
                case "manutencao":
                    $statusContagem["manutencao"]++;
                    break;
                case "reserva":
                    $statusContagem["reservado"]++;
                    break;
                case "ocupados":
                    $statusContagem["ocupado"]++;
                    break;
                default:
                    // Se algum outro status for encontrado, você pode lidar com ele aqui.
                    break;
            }
        }
    }

    $resposta["limpeza"] = $statusContagem["limpeza"];
    $resposta["livre"] = $statusContagem["livre"];
    $resposta["manutencao"] = $statusContagem["manutencao"];
    $resposta["reserva"] = $statusContagem["reserva"];
    $resposta["ocupado"] = $statusContagem["ocupados"];
}
function contarRegistros($conexao, $idCaixa) {
    $sql = "SELECT COUNT(*) as total_registros FROM registralocado WHERE idcaixaatual = $idCaixa";
    $result = $conexao->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total_registros'];
    } else {
        return 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    $conexao = conectarAoBanco();
    if ($conexao === null) {
        $resposta["mensagem"] = "Erro na conexão com o banco de dados.";
    } else {
        $caixa = obterIdCaixaAberto($conexao);
        if ($caixa !== null) {
            $idCaixa = $caixa["id"];
            $resposta["erro"] = false;
            $resposta["data"] = $caixa["horaabre"];

            $total_registralocado = somarValorQuartoEConsumo($conexao, $idCaixa);
            $saldoabre = obterSaldoAbre($conexao, $idCaixa);

            $novoSaldo = $saldoabre + $total_registralocado;
            $resposta["saldo"] =   $novoSaldo ;
            $resposta["hospedagem"] = contarRegistros($conexao, $idCaixa);

             obterStatusEContarRegistros($conexao, $resposta);


        }
        $conexao->close();
    }
} else {
    $resposta["mensagem"] = "Altere o metodo de requisicao.";
    $resposta["mensagem2"] = $_SERVER['REQUEST_METHOD'];
}

echo json_encode($resposta);
?>

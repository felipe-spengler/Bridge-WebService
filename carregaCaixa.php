<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexao2.php';

$mesAtual = date('n');
$resposta = array();
$resposta["erro"] = true;

function obterIdCaixaMesAtual($conexao) {
    // Obter o primeiro dia do mês atual
    $primeiroDia = date('Y-m-01');
    // Obter o último dia do mês atual
    $ultimoDia = date('Y-m-t');

    $sql = "SELECT id FROM caixa WHERE horaabre >= '$primeiroDia' AND horaabre <= '$ultimoDia'";
    $result = $conexao->query($sql);

    if ($result) {
        $idCaixas = array();

        while ($row = $result->fetch_assoc()) {
            $idCaixas[] = $row['id'];
        }

        return $idCaixas;
    } else {
        return 0;
    }
}

function somarSaldosFechadosMesAtual($conexao, $idCaixasMesAtual) {
    if (empty($idCaixasMesAtual)) {
        return 0; // Retorna 0 se o array estiver vazio
    }

    $ids = implode(",", $idCaixasMesAtual);

    $sql = "SELECT SUM(saldofecha) AS total FROM caixa WHERE id IN ($ids)";
    $result = $conexao->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return ($row['total'] != null) ? $row['total'] : 0;
    } else {
        return 0;
    }
}

function obterTotalLocacoes($conexao, $idCaixasMesAtual) {
    if (empty($idCaixasMesAtual)) {
        return 0; // Retorna 0 se o array estiver vazio
    }

    $ids = implode(",", $idCaixasMesAtual);

    $sql = "SELECT SUM((SELECT COUNT(*) FROM registralocado WHERE idcaixaatual IN ($ids))) as totalLocacoes";
            
    $result = $conexao->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return ($row['totalLocacoes'] != null) ? $row['totalLocacoes'] : 0;
    } else {
        return 0;
    }
}

function calcularMedias($conexao, $idCaixasMesAtual) {
    if (empty($idCaixasMesAtual)) {
        return ["mediaValorConsumo" => 0, "mediaValorQuarto" => 0];
    }

    $ids = implode(",", $idCaixasMesAtual);
    $consulta = "SELECT valorconsumo, valorquarto FROM registralocado WHERE idcaixaatual IN ($ids)";
    $resultado = mysqli_query($conexao, $consulta);

    if ($resultado) {
        $somaValorConsumo = 0;
        $somaValorQuarto = 0;
        $somaLocacoes = 0;
        $numRegistros = 0;

        while ($registro = mysqli_fetch_assoc($resultado)) {
            $somaValorConsumo += $registro['valorconsumo'];
            $somaValorQuarto += $registro['valorquarto'];
            $somaLocacoes += $registro['valorconsumo'] + $registro['valorquarto'];
            $numRegistros++;
        }

        if ($numRegistros >= 0) {
            $mediaValorConsumo = $somaValorConsumo / $numRegistros;
            $mediaValorQuarto = $somaValorQuarto / $numRegistros;
            $ticketMedioLocacoes = $somaLocacoes / $numRegistros;
        } else {
            $mediaValorConsumo = 0;
            $mediaValorQuarto = 0;
            $ticketMedioLocacoes = 0;
        }

        return [
            "mediaValorConsumo" => $mediaValorConsumo,
            "mediaValorQuarto" => $mediaValorQuarto,
            "ticketMedioLocacoes" => $ticketMedioLocacoes
        ];
    } else {
        return ["mediaValorConsumo" => 0, "mediaValorQuarto" => 0, "ticketMedioLocacoes" => 0];
    }
}

function respostaPadraoErro() {
    return ["mediaValorConsumo" => 0, "mediaValorQuarto" => 0, "ticketMedioLocacoes" => 0];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexao = conectarAoBanco();
    if ($conexao === null) {
        $resposta["mensagem"] = "Erro na conexão com o banco de dados.";
    } else {
        $idCaixasMesAtual = obterIdCaixaMesAtual($conexao);
        $resposta["id"] = $idCaixasMesAtual;
        $quantidadeCaixas = count($idCaixasMesAtual);
        $faturamentoAtual = somarSaldosFechadosMesAtual($conexao, $idCaixasMesAtual);
        $mediaFaturamento = $faturamentoAtual / date('j');
        $previsaoFaturamento = $mediaFaturamento * date('t');
        $numLocacoes = obterTotalLocacoes($conexao, $idCaixasMesAtual);
        $mediaLocacoes = $numLocacoes / date('j');
        $previsaoLocacoes = $mediaLocacoes * date('t');

        $medias = calcularMedias($conexao, $idCaixasMesAtual);
        $resposta["erro"] = false;
      	  $resposta["mediaValorConsumo"] = $medias["mediaValorConsumo"];
        	$resposta["mediaValorQuarto"] = $medias["mediaValorQuarto"];
       	 $resposta["ticketMedioLocacoes"] = $medias["ticketMedioLocacoes"];
        $resposta["quantidadeCaixas"] = $quantidadeCaixas;
        $resposta["faturamentoAtual"] = $faturamentoAtual;
        	$resposta["mediaDiariaFaturamento"] = $mediaFaturamento;
        	$resposta["previsaoFaturamento"] = $previsaoFaturamento;
        $resposta["numLocacoes"] = $numLocacoes;
       	 $resposta["mediaDiariaLocacoes"] = $mediaLocacoes;
        	$resposta["previsaoLocacoes"] = $previsaoLocacoes;

        $conexao->close();
    }
} else {
    $resposta["mensagem"] = "Requisição inválida.";
    $resposta += respostaPadraoErro();
}

echo json_encode($resposta);
?>

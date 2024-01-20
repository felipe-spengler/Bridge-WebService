<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexao2.php';

$respostaGeral = array();
$respostaGeral["erro"] = true;


function contarLocacoesPorMes($conexao, $ano) {
    $contagemPorMes = array();

    // Consulta para obter a contagem de locações para cada idcaixaatual
    $sqlLocacoes = "SELECT idcaixaatual, COUNT(*) as total_locacoes 
                    FROM registralocado 
                    WHERE YEAR(datahospedagem) = $ano 
                    GROUP BY idcaixaatual";

    $resultLocacoes = $conexao->query($sqlLocacoes);

    if (!$resultLocacoes) {
        // Trate o erro na consulta
        return array('success' => false, 'mensagem' => $conexao->error);
    }

    // Mapeia os resultados para um array associativo (idcaixaatual => total_locacoes)
    $locacoesPorIdCaixa = array();
    while ($rowLocacoes = $resultLocacoes->fetch_assoc()) {
        $locacoesPorIdCaixa[$rowLocacoes['idcaixaatual']] = $rowLocacoes['total_locacoes'];
    }

    // Consulta para obter o mês de cada idcaixa
    $sqlMeses = "SELECT id, MONTH(horaabre) as mes 
                 FROM caixa 
                 WHERE YEAR(horaabre) = $ano";

    $resultMeses = $conexao->query($sqlMeses);

    if (!$resultMeses) {
        // Trate o erro na consulta
        return array('success' => false, 'mensagem' => $conexao->error);
    }

    // Inicializa o array de contagemPorMes com todos os meses e valores iniciais de 0
    for ($mes = 1; $mes <= 12; $mes++) {
        $contagemPorMes[$mes] = 0;
    }

    // Mapeia os resultados para um array associativo (idcaixa => mes)
    $mesesPorIdCaixa = array();
    while ($rowMeses = $resultMeses->fetch_assoc()) {
        $mesesPorIdCaixa[$rowMeses['id']] = $rowMeses['mes'];
    }

    // Soma as locações para cada mês
    foreach ($locacoesPorIdCaixa as $idCaixa => $totalLocacoes) {
        $mes = $mesesPorIdCaixa[$idCaixa];
        $contagemPorMes[$mes] += $totalLocacoes;
    }

    return array('success' => true, 'contagem' => $contagemPorMes);
}

function calculaMeses($conexao, $ano) {

    $sql = "SELECT 
                MONTH(horaabre) as mes, 
                YEAR(horaabre) as ano, 
                SUM(saldofecha - saldoabre) as total_faturamento
            FROM caixa 
            WHERE YEAR(horaabre) = $ano 
            GROUP BY MONTH(horaabre), YEAR(horaabre)";

    $result = $conexao->query($sql);

    if ($result) {
        $dadosFaturamento = array();
        
        // Inicializa o array resposta com todos os meses e valor inicial de faturamento 0
        $resposta = array();
        for ($mes = 1; $mes <= 12; $mes++) {
            $resposta[$mes] = 0;
        }

        while ($row = $result->fetch_assoc()) {
            $resposta[$row['mes']] = $row['total_faturamento'];
        }

        return $resposta;
    } else {
        return array('success' => false, 'mensagem' => $conexao->error);
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexao = conectarAoBanco();

    if ($conexao === null) {
        $respostaGeral["erro"] = true;
        $respostaGeral["mensagem"] = "Erro na conexão com o banco de dados.";
    } else {
        $ano = filter_input(INPUT_POST, 'anoPassado', FILTER_VALIDATE_INT);

        if ($ano === false || $ano === null) {
            $respostaGeral["erro"] = true;
            $respostaGeral["mensagem"] = "Ano inválido ou não fornecido.";
        } else {
            $respostaGeral["locacoes"] = contarLocacoesPorMes($conexao, $ano);
            $respostaGeral["faturamento"] = calculaMeses($conexao, $ano);
            $respostaGeral["erro"] = false;
        }

        $conexao->close();
    }
} else {
    $respostaGeral["erro"] = true;
    $respostaGeral["mensagem"] = "Requisição inválida.";
}



echo json_encode($respostaGeral);
?>

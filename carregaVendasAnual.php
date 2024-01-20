<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexao2.php';

$respostaGeral = array();
$respostaGeral["erro"] = true;

function calculaMeses($conexao) {
    $ano = $_POST['anoPassado'];

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

        return array('success' => true, 'resposta' => $resposta);
    } else {
        return array('success' => false, 'mensagem' => $conexao->error);
    }
}

// Exemplo de uso
$resultado = calculaMeses($sua_conexao);

if ($resultado['success']) {
    $respostaGeral["resultado"] = $resultado['resposta'];
} else {
    $respostaGeral["mensagem"] = "Erro ao calcular os meses: " . $resultado['mensagem'];
}

echo json_encode($respostaGeral);
?>

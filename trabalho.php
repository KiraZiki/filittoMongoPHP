<?php
// Verifica se a extensão MongoDB está carregada
if (!extension_loaded("mongodb")) {
    die("A extensão MongoDB não está instalada.");
}

// Conectando ao MongoDB
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Processar o formulário de inserção
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Receber os dados do formulário
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $data = isset($_POST['data']) ? $_POST['data'] : '';

    // Verificar se os dados foram preenchidos corretamente
    if (!empty($nome) && !empty($data)) {
        // Criar um documento para inserir no MongoDB
        $bulk = new MongoDB\Driver\BulkWrite;
        $document = [
            'nome' => $nome,
            'data' => $data
        ];

        // Inserir o documento na coleção 'atividade' do banco 'trabalho'
        $bulk->insert($document);

        try {
            $manager->executeBulkWrite('banco.trabalho', $bulk);
            echo "Documento inserido com sucesso!";
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erro ao inserir no MongoDB: " . $e->getMessage();
        }
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Atividade</title>
</head>
<body>

<h2>Inserir Nova Atividade</h2>

<!-- Formulário de Inserção -->
<form method="POST" action="">
    <label for="nome">Nome da Matéria:</label><br>
    <input type="text" id="nome" name="nome" required><br><br>

    <label for="data">Data da Atividade:</label><br>
    <input type="date" id="data" name="data" required><br><br>

    <input type="submit" value="Inserir">
</form>

<h2>Lista de Atividades</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Data</th>
    </tr>

    <?php
    // Consulta para selecionar os documentos da coleção 'atividade' no banco 'banco'
    $query = new MongoDB\Driver\Query([]);  // Consulta vazia para buscar todos os documentos
    $cursor = $manager->executeQuery('banco.trabalho', $query);

    foreach ($cursor as $document) {
        // Converter o BSON para array e exibir os dados
        $trabalho = (array) $document;
        echo "<tr><td>" . $trabalho['_id'] . "</td><td>" . $trabalho['nome'] . "</td><td>" . $trabalho['data'] . "</td></tr>";
    }
    ?>

</table>

</body>
</html>

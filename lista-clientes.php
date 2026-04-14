<?php
require_once 'conexao.php';

$sql = "SELECT id, nome FROM clientes ORDER BY id";

$stid = oci_parse($conn, $sql);

if (!$stid) {
    $e = oci_error($conn);

    echo '<pre>';
    echo "Erro ao preparar a consulta.\n\n";
    print_r($e);
    echo '</pre>';

    exit;
}

$exec = oci_execute($stid);

if (!$exec) {
    $e = oci_error($stid);

    echo '<pre>';
    echo "Erro ao executar a consulta.\n\n";
    print_r($e);
    echo '</pre>';

    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Lista de Clientes</title>
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 30px;
            background: #f5f5f5;
            color: #222;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 600px;
            background: #fff;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #e9e9e9;
        }

        .sem-registros {
            background: #fff;
            padding: 15px;
            border: 1px solid #ccc;
            width: 600px;
        }
    </style>
</head>
<body>

    <h1>Lista de Clientes</h1>

    <?php
    $temRegistros = false;

    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if (!$temRegistros) {
            $temRegistros = true;
            echo '<table>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Nome</th>';
            echo '<th>Ações</th>';
            echo '</tr>';
        }

        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['NOME']) . '</td>';
        echo '<td class="acoes-tabela"><a href="edita-clientes.php?id=' . urlencode($row['ID']) . '">Editar</a> <a href="exclui-clientes.php?id=' . urlencode($row['ID']) . '">Excluir</a></td>';
        echo '</tr>';
    }

    if ($temRegistros) {
        echo '</table>';
    } else {
        echo '<div class="sem-registros">Nenhum cliente encontrado.</div>';
    }

    oci_free_statement($stid);
    oci_close($conn);
    ?>

</body>
</html>
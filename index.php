<?php
require_once 'conexao.php';

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$mensagem = '';
$erro = '';
$clientes = array();

if ($busca != '') {
    if (is_numeric($busca)) {
        $sql = "SELECT id, nome FROM clientes WHERE id = :id ORDER BY id";
        $stid = oci_parse($conn, $sql);

        if (!$stid) {
            $e = oci_error($conn);
            $erro = 'Erro ao preparar a busca por ID.';
        } else {
            $idBusca = $busca;
            oci_bind_by_name($stid, ':id', $idBusca);

            $exec = oci_execute($stid);

            if (!$exec) {
                $e = oci_error($stid);
                $erro = 'Erro ao executar a busca: ' . $e['message'];
            } else {
                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    $clientes[] = $row;
                }

                if (count($clientes) == 0) {
                    $mensagem = 'Nenhum cliente encontrado para o ID informado.';
                }
            }

            oci_free_statement($stid);
        }
    } else {
        $sql = "SELECT id, nome
                FROM clientes
                WHERE UPPER(nome) LIKE UPPER(:nome)
                ORDER BY id";

        $stid = oci_parse($conn, $sql);

        if (!$stid) {
            $e = oci_error($conn);
            $erro = 'Erro ao preparar a busca por nome.';
        } else {
            $nomeBusca = '%' . $busca . '%';
            oci_bind_by_name($stid, ':nome', $nomeBusca, 100);

            $exec = oci_execute($stid);

            if (!$exec) {
                $e = oci_error($stid);
                $erro = 'Erro ao executar a busca: ' . $e['message'];
            } else {
                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    $clientes[] = $row;
                }

                if (count($clientes) == 0) {
                    $mensagem = 'Nenhum cliente encontrado para o nome informado.';
                }
            }

            oci_free_statement($stid);
        }
    }
}

oci_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CRUD Legado PHP + Oracle</title>
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 30px;
            background: #f5f5f5;
            color: #222;
        }

        .container {
            max-width: 900px;
        }

        h1 {
            margin-bottom: 10px;
        }

        .subtitulo {
            margin-bottom: 25px;
            color: #555;
        }

        .acoes {
            margin-bottom: 20px;
        }

        .acoes a {
            display: inline-block;
            padding: 10px 16px;
            margin-right: 10px;
            margin-bottom: 10px;
            background: #2d6cdf;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .acoes a:hover {
            background: #1f57b8;
        }

        .busca-box {
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 25px;
        }

        .busca-box label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .busca-box input[type="text"] {
            width: 100%;
            max-width: 420px;
            padding: 10px;
            border: 1px solid #bbb;
            box-sizing: border-box;
            margin-bottom: 12px;
        }

        .busca-box input[type="submit"] {
            padding: 10px 18px;
            background: #28a745;
            color: #fff;
            border: 0;
            cursor: pointer;
            border-radius: 4px;
        }

        .busca-box input[type="submit"]:hover {
            background: #1e7e34;
        }

        .busca-box .limpar {
            display: inline-block;
            margin-left: 10px;
            color: #2d6cdf;
            text-decoration: none;
        }

        .busca-box .limpar:hover {
            text-decoration: underline;
        }

        .mensagem {
            background: #e7f7e7;
            border: 1px solid #8fc98f;
            color: #256b25;
            padding: 12px;
            margin-bottom: 15px;
        }

        .erro {
            background: #fdeaea;
            border: 1px solid #d88;
            color: #a22;
            padding: 12px;
            margin-bottom: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
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

        .acoes-tabela a {
            margin-right: 10px;
            color: #2d6cdf;
            text-decoration: none;
        }

        .acoes-tabela a:hover {
            text-decoration: underline;
        }

        .dica {
            margin-top: 10px;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CRUD Legado PHP 5.2 + Oracle</h1>
        <div class="subtitulo">Painel inicial para consultar e navegar pelas funcionalidades.</div>

        <div class="acoes">
            <a href="lista-clientes.php">Listar Todos os Clientes</a>
            <a href="cadastra-clientes.php">Cadastrar Cliente</a>
        </div>

        <div class="busca-box">
            <form method="get" action="">
                <label for="busca">Buscar cliente por ID ou nome</label>
                <input
                    type="text"
                    name="busca"
                    id="busca"
                    value="<?php echo htmlspecialchars($busca); ?>"
                />
                <br />
                <input type="submit" value="Buscar" />
                <a class="limpar" href="index.php">Limpar busca</a>
            </form>

            <div class="dica">
                Digite um número para buscar por ID ou um texto para buscar por nome.
            </div>
        </div>

        <?php if ($mensagem != '') { ?>
            <div class="mensagem"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php } ?>

        <?php if ($erro != '') { ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php } ?>

        <?php if (count($clientes) > 0) { ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>

                <?php
                $i = 0;
                for ($i = 0; $i < count($clientes); $i++) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($clientes[$i]['ID']); ?></td>
                        <td><?php echo htmlspecialchars($clientes[$i]['NOME']); ?></td>
                        <td class="acoes-tabela">
                            <a href="edita-clientes.php?id=<?php echo urlencode($clientes[$i]['ID']); ?>">Editar</a>
                            <a href="exclui-clientes.php?id=<?php echo urlencode($clientes[$i]['ID']); ?>">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</body>
</html>
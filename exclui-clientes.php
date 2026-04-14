<?php
require_once 'conexao.php';

$mensagem = '';
$erro = '';
$id = '';
$nome = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';

    if ($id == '') {
        $erro = 'ID do cliente não informado.';
    } elseif (!is_numeric($id)) {
        $erro = 'ID inválido.';
    } else {
        $sqlBusca = "SELECT id, nome FROM clientes WHERE id = :id";
        $stidBusca = oci_parse($conn, $sqlBusca);

        if (!$stidBusca) {
            $e = oci_error($conn);
            $erro = 'Erro ao preparar a consulta.';
        } else {
            oci_bind_by_name($stidBusca, ':id', $id);
            $execBusca = oci_execute($stidBusca);

            if (!$execBusca) {
                $e = oci_error($stidBusca);
                $erro = 'Erro ao buscar cliente: ' . $e['message'];
            } else {
                $row = oci_fetch_array($stidBusca, OCI_ASSOC + OCI_RETURN_NULLS);

                if (!$row) {
                    $erro = 'Cliente não encontrado.';
                } else {
                    $nome = $row['NOME'];

                    $sqlDelete = "DELETE FROM clientes WHERE id = :id";
                    $stidDelete = oci_parse($conn, $sqlDelete);

                    if (!$stidDelete) {
                        $e = oci_error($conn);
                        $erro = 'Erro ao preparar a exclusão.';
                    } else {
                        oci_bind_by_name($stidDelete, ':id', $id);
                        $execDelete = oci_execute($stidDelete, OCI_DEFAULT);

                        if (!$execDelete) {
                            $e = oci_error($stidDelete);
                            $erro = 'Erro ao excluir cliente: ' . $e['message'];
                        } else {
                            oci_commit($conn);
                            $mensagem = 'Cliente excluído com sucesso.';
                        }

                        oci_free_statement($stidDelete);
                    }
                }
            }

            oci_free_statement($stidBusca);
        }
    }
} else {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';

    if ($id == '') {
        $erro = 'ID do cliente não informado.';
    } elseif (!is_numeric($id)) {
        $erro = 'ID inválido.';
    } else {
        $sql = "SELECT id, nome FROM clientes WHERE id = :id";
        $stid = oci_parse($conn, $sql);

        if (!$stid) {
            $e = oci_error($conn);
            $erro = 'Erro ao preparar a consulta.';
        } else {
            oci_bind_by_name($stid, ':id', $id);
            $exec = oci_execute($stid);

            if (!$exec) {
                $e = oci_error($stid);
                $erro = 'Erro ao buscar cliente: ' . $e['message'];
            } else {
                $row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

                if ($row) {
                    $id = $row['ID'];
                    $nome = $row['NOME'];
                } else {
                    $erro = 'Cliente não encontrado.';
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
    <title>Excluir Cliente</title>
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

        .box {
            width: 420px;
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px;
        }

        .mensagem {
            width: 420px;
            background: #e7f7e7;
            border: 1px solid #8fc98f;
            color: #256b25;
            padding: 12px;
            margin-bottom: 15px;
        }

        .erro {
            width: 420px;
            background: #fdeaea;
            border: 1px solid #d88;
            color: #a22;
            padding: 12px;
            margin-bottom: 15px;
        }

        .destaque {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background: #c9302c;
            color: #fff;
            border: 0;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #a71f1b;
        }

        .links {
            margin-top: 15px;
        }

        .links a {
            margin-right: 15px;
            color: #2d6cdf;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Excluir Cliente</h1>

    <?php if ($mensagem != '') { ?>
        <div class="mensagem"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php } ?>

    <?php if ($erro != '') { ?>
        <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php } ?>

    <?php if ($erro == '' && $mensagem == '') { ?>
        <div class="box">
            <div class="destaque">
                <strong>ID:</strong> <?php echo htmlspecialchars($id); ?><br />
                <strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?>
            </div>

            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />
                <input type="submit" value="Confirmar Exclusão" />
            </form>
        </div>
    <?php } ?>

    <div class="links">
        <a href="lista-clientes.php">Ver lista de clientes</a>
        <a href="cadastra-clientes.php">Cadastrar cliente</a>
    </div>

</body>
</html>
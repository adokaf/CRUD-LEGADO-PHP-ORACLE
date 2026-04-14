<?php
require_once 'conexao.php';

$mensagem = '';
$erro = '';
$id = '';
$nome = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';

    if ($id == '' || $nome == '') {
        $erro = 'Preencha o ID e o nome do cliente.';
    } elseif (!is_numeric($id)) {
        $erro = 'O campo ID deve ser numérico.';
    } else {
        $sql = "INSERT INTO clientes (id, nome) VALUES (:id, :nome)";
        $stid = oci_parse($conn, $sql);

        if (!$stid) {
            $e = oci_error($conn);
            $erro = 'Erro ao preparar a consulta.';
        } else {
            oci_bind_by_name($stid, ':id', $id);
            oci_bind_by_name($stid, ':nome', $nome, 100);

            $exec = oci_execute($stid, OCI_DEFAULT);

            if (!$exec) {
                $e = oci_error($stid);
                $erro = 'Erro ao cadastrar cliente: ' . $e['message'];
            } else {
                oci_commit($conn);
                $mensagem = 'Cliente cadastrado com sucesso.';
                $id = '';
                $nome = '';
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
    <title>Cadastrar Cliente</title>
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

        form {
            width: 420px;
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background: #2d6cdf;
            color: #fff;
            border: 0;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #1f57b8;
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

    <h1>Cadastrar Cliente</h1>

    <?php if ($mensagem != '') { ?>
        <div class="mensagem"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php } ?>

    <?php if ($erro != '') { ?>
        <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php } ?>

    <form method="post" action="">
        <label for="id">ID</label>
        <input type="text" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>" />

        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($nome); ?>" />

        <input type="submit" value="Cadastrar" />
    </form>

    <div class="links">
        <a href="lista-clientes.php">Ver lista de clientes</a>
        <a href="index.php">Voltar ao início</a>
    </div>

</body>
</html>
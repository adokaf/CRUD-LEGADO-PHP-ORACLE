<?php
/**
 * conexao.php
 * Conexão Oracle para PHP 5.2 + OCI8
 */

$usuario = 'legado';
$senha   = 'legado123';
$host    = 'oracle/XE';
$charset = 'AL32UTF8';

$conn = @oci_connect($usuario, $senha, $host, $charset);

if (!$conn) {
    $e = oci_error();

    echo '<pre>';
    echo "Erro ao conectar no Oracle.\n\n";
    print_r($e);
    echo '</pre>';

    exit;
}
?>
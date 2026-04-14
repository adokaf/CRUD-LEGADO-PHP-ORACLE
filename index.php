<?php

$conn = oci_connect('legado', 'legado123', 'oracle/XE');

if (!$conn) {
    $e = oci_error();
    echo '<pre>';
    print_r($e);
    echo '</pre>';
    die('Falha na conexão com Oracle.');
}

$stid = oci_parse($conn, 'SELECT 1 AS TESTE FROM dual');
oci_execute($stid);
$row = oci_fetch_array($stid, OCI_ASSOC);

echo '<pre>';
print_r($row);
echo '</pre>';

oci_free_statement($stid);
oci_close($conn);
?>
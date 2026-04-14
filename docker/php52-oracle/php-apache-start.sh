#!/bin/bash
set -e

export LD_LIBRARY_PATH=/opt/oracle/instantclient_11_2:${LD_LIBRARY_PATH}

# Garante que as libs do Oracle estejam registradas
ldconfig

# Mostra versões úteis no log
php -v
php -m | grep -i oci || true

exec apache2ctl -D FOREGROUND
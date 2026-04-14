FROM lamarques/php:5.2-apache

ENV ORACLE_CLIENT_DIR=/opt/oracle/instantclient_11_2
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_11_2
ENV NLS_LANG=AMERICAN_AMERICA.AL32UTF8

# Corrige os repositórios antigos do Ubuntu 12.04
RUN rm -f /etc/apt/sources.list && \
    echo 'deb http://old-releases.ubuntu.com/ubuntu precise main restricted universe multiverse' > /etc/apt/sources.list && \
    echo 'deb http://old-releases.ubuntu.com/ubuntu precise-updates main restricted universe multiverse' >> /etc/apt/sources.list && \
    echo 'deb http://old-releases.ubuntu.com/ubuntu precise-security main restricted universe multiverse' >> /etc/apt/sources.list

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    libaio1 \
 && rm -rf /var/lib/apt/lists/*

# Copia os zips do Instant Client para dentro da imagem
COPY docker/oracle-client/instantclient-basic-linux.x64-11.2.0.4.0.zip /tmp/
COPY docker/oracle-client/instantclient-sdk-linux.x64-11.2.0.4.0.zip /tmp/

# Descompacta o Instant Client
RUN mkdir -p /opt/oracle && \
    unzip /tmp/instantclient-basic-linux.x64-11.2.0.4.0.zip -d /opt/oracle && \
    unzip /tmp/instantclient-sdk-linux.x64-11.2.0.4.0.zip -d /opt/oracle && \
    ln -s /opt/oracle/instantclient_11_2/libclntsh.so.11.1 /opt/oracle/instantclient_11_2/libclntsh.so && \
    ln -s /opt/oracle/instantclient_11_2/libocci.so.11.1 /opt/oracle/instantclient_11_2/libocci.so && \
    echo "/opt/oracle/instantclient_11_2" > /etc/ld.so.conf.d/oracle-instantclient.conf && \
    ldconfig

# Instala OCI8 compatível com PHP 5.2
RUN printf "instantclient,/opt/oracle/instantclient_11_2\n" | pecl install oci8-2.0.12

# Habilita a extensão no CLI e no Apache
RUN echo "extension=oci8.so" >> /etc/php/cli-php5.2/php.ini && \
    echo "extension=oci8.so" >> /etc/php/apache2-php5.2/php.ini

# Opcional: reduz o warning antigo de pdo_pgsql duplicado
RUN sed -i '/pdo_pgsql/d' /etc/php/cli-php5.2/php.ini || true && \
    sed -i '/pdo_pgsql/d' /etc/php/apache2-php5.2/php.ini || true

COPY docker/php52-oracle/php-apache-start.sh /usr/local/bin/php-apache-start.sh
RUN chmod +x /usr/local/bin/php-apache-start.sh

WORKDIR /var/www

CMD ["/usr/local/bin/php-apache-start.sh"]
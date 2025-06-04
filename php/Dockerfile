FROM php:8.1-apache-bullseye

RUN apt-get update && apt-get install -y \
    gnupg2 apt-transport-https curl lsb-release unixodbc-dev \
    libssl-dev libxml2-dev libpng-dev libpq-dev libjpeg-dev libfreetype6-dev \
    && curl -sSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/microsoft.gpg \
    && curl -sSL https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/
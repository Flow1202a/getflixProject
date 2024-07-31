FROM php:8.2-apache

# Installe les extensions PHP nécessaires
RUN apt-get update && apt-get upgrade -y
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo_mysql

# Copie les fichiers de l'application dans le dossier par défaut de Apache
COPY src/ /var/www/html/

# Ouvre le port 80 pour Apache
EXPOSE 80

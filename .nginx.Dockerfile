# Builder for nginx image production ready
FROM registry.sfam.ovh/nginx:dolibarr

RUN mkdir /var/www/html/htdocs --parent
ADD --chown=www-data:www-data htdocs /var/www/html/htdocs

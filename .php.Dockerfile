# Builder for php image production ready
FROM registry.sfam.ovh/php:7.1

# Prepare logs collect
RUN touch /var/log/php7.1-fpm.log && chown -R 33:33 /var/log/php7.1-fpm.log

# Copy script front & back
ADD --chown=www-data:www-data . /var/www/html

# Prepare logs path
#RUN mkdir -p var/logs var/cache && chown -R 33:33 var

# Running user
USER www-data

# Share this volume with nginx front container
VOLUME ["/var/www/html"]

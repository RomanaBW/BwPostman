FROM universe3:5000/romana/joomla-files:8.1.0-5.1.0
LABEL maintainer="Romana Boldt <info@boldt-webservice.de>"

# Custom cache invalidation
ARG CACHEBUST=1

# Enable the PHP extensions xdebug and uopz
RUN set -ex; \
	docker-php-ext-enable \
        xdebug \
        uopz \
	; \

# set some needed xdebug settings
# xdebug.client_host=host.docker.internal don't work, use ip of client, it works
RUN { \
  		echo "xdebug.client_host = ${XDEBUG_CLIENT_HOST}"; \
		echo "xdebug.client_port = ${XDEBUG_CLIENT_PORT}"; \
		echo "xdebug.idekey = ${XDEBUG_IDE_KEY}"; \
        echo "xdebug.mode=debug"; \
        echo "xdebug.start_with_request=yes"; \
		echo 'xdebug.output_dir = /var/www/html/administrator/logs/xdebug'; \
		echo 'xdebug.log = /var/www/html/administrator/logs/xdebug/debug.log'; \
	} > /usr/local/etc/php/conf.d/xdebug.ini

# set some needed uopz settings
RUN { \
  		echo 'uopz.exit=1'; \
	} > /usr/local/etc/php/conf.d/uopz.ini

# Copy appropriate Joomla configuration and db adjustments file to intermediate folder
RUN mkdir -p /usr/src/files/images
COPY adjustdb_bwpm.php /usr/src/files/
COPY boldt-webservice.png /usr/src/files/images
COPY boldt-webservice-text.png /usr/src/files/images

# Copy init script and make it executable
COPY docker-entrypoint.sh /entrypoint.sh
RUN chmod u+x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]

FROM universe3:5000/romana/joomla-files-8.1.0:5.3.2
LABEL maintainer=" Romana Boldt <info@boldt-webservice.de> "

# Custom cache invalidation
ARG CACHEBUST=1

# Copy appropriate Joomla configuration and db adjustments file to intermediate folder
RUN mkdir -p /usr/src/files/images
COPY adjustdb_bwpm.php /usr/src/files/
COPY boldt-webservice.png /usr/src/files/images
COPY boldt-webservice-text.png /usr/src/files/images

# Enable the PHP extensions xdebug and zip
RUN set -ex; \
	docker-php-ext-enable \
        xdebug \
        zip \
	;

# set some needed xdebug settings
# xdebug.client_host=host.docker.internal don't work, use ip of client, it works
RUN { \
  		echo "xdebug.client_host = 192.168.2.1"; \
		echo "xdebug.client_port = 9003"; \
		echo "xdebug.idekey = 9003"; \
        echo "xdebug.mode=debug"; \
        echo "xdebug.start_with_request=yes"; \
		echo 'xdebug.output_dir = /var/www/html/administrator/logs/xdebug'; \
		echo 'xdebug.log = /var/www/html/administrator/logs/xdebug/debug.log'; \
	} > /usr/local/etc/php/conf.d/xdebug.ini

# Copy init script and make it executable
COPY docker-entrypoint.sh /entrypoint.sh
RUN chmod u+x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]

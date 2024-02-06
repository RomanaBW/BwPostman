FROM universe3:5000/romana/joomla-jce-files:8.1.0-5.0.0
LABEL maintainer="Romana Boldt <info@boldt-webservice.de>"

# Custom cache invalidation
ARG CACHEBUST=1

# Copy appropriate Joomla configuration and db adjustments file to intermediate folder
RUN mkdir -p /usr/src/files
COPY configuration.php adjustdb_bwpm.php /usr/src/files/

# Copy init script and make it executable
COPY docker-entrypoint.sh /entrypoint.sh
RUN chmod u+x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]

FROM universe3:5000/romana/bwpm-base-files:8.1.0-5.1.1
LABEL maintainer="Romana Boldt <info@boldt-webservice.de>"

# Custom cache invalidation
ARG CACHEBUST=1

ENTRYPOINT ["/usr/bin/env"]
CMD ["apache2-foreground"]

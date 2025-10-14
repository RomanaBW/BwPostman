FROM universe3:5000/romana/bwpm-base-files:7.4.33-4.4.14
LABEL maintainer=" Romana Boldt <info@boldt-webservice.de> "

# Custom cache invalidation
ARG CACHEBUST=1

ENTRYPOINT ["/usr/bin/env"]
CMD ["apache2-foreground"]

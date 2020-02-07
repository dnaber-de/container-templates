FROM httpd:2.4-alpine

RUN mkdir /usr/local/apache2/custom-conf.d

COPY .docker/httpd/modules.conf /usr/local/apache2/custom-conf.d/000-modules.conf
COPY .docker/httpd/container-vhost.conf /usr/local/apache2/custom-conf.d/container-vhost.conf

RUN echo "IncludeOptional /usr/local/apache2/custom-conf.d/*.conf" >> /usr/local/apache2/conf/httpd.conf

RUN mkdir -p /var/www/public
RUN mkdir -p /var/log/apache2

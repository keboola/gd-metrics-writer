#VERSION 1.0.0
FROM keboola/base-php56
MAINTAINER Miro Cillik <miro@keboola.com>

# Instal dependcies
RUN yum -y --enablerepo=epel,remi,remi-php56 install php-mcrypt

# Run writer
ADD . /app
WORKDIR /app
RUN echo "memory_limit = -1" >> /etc/php.ini
RUN composer install --no-interaction

ENTRYPOINT php ./run.php --data=/data

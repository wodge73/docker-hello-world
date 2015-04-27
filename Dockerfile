FROM tutum/apache-php:latest
MAINTAINER Brian Christner

ADD /app/index.php /app/index.php
ADD /app/logo1.png /app/logo1.png
EXPOSE 80
WORKDIR /app
CMD ["/run.sh"]

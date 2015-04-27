FROM tutum/apache-php:latest
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN apt-get update && apt-get install -yq git && rm -rf /var/lib/apt/lists/*
RUN rm -fr /app
ADD /app/index.php /app/index.php
ADD /app/logo1.png /app/logo1.png
EXPOSE 80
WORKDIR /app
CMD ["/run.sh"]

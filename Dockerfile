FROM registry.access.redhat.com/ubi8/php-74
# FROM registry.access.redhat.com/ubi8/php-80
USER root
ADD . /var/www/html/
ADD ./patch1.sh /patch1.sh
RUN bash /patch1.sh && rm /patch1.sh
RUN dnf -y install php-zip
RUN dnf remove nginx-filesystem -y
WORKDIR /var/www/html/
RUN chown -R 1001:0 /run
RUN chown -R 1001:0 /etc/httpd/run
RUN chmod -R 777 /run
RUN chmod -R 777 /etc/httpd/run
RUN chmod -R 777 /var/www/html/ct_id_uploads
RUN chmod -R 777 /var/www/html/odc_uploads
RUN chmod -R 777 /var/www/html/nohup.out
RUN composer install --no-interaction
USER root 
ADD ./patch2.sh /patch2.sh
RUN bash /patch2.sh && rm /patch2.sh
USER 1001
ENTRYPOINT ["httpd", "-D", "FOREGROUND"]
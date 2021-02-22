FROM registry.access.redhat.com/ubi8/php-74
USER root
ADD . /var/www/html/
ADD ./patch1.sh /patch1.sh
RUN bash /patch1.sh && rm /patch1.sh
WORKDIR /opt/ibm
ADD ibmdsdp.tar.gz /opt/ibm/
RUN bash dsdriver/installDSDriver
ENV IBM_DB_HOME /opt/ibm/dsdriver
RUN echo $IBM_DB_HOME | pecl install ibm_db2
RUN yum -y install php-zip
WORKDIR /var/www/html/
RUN sed -i  "$ a extension=ibm_db2.so" /etc/php.ini
RUN sed -i  "$ a extension=zip.so"     /etc/php.ini
#ADD db2consv_ee.lic /opt/ibm/dsdriver/license/db2consv_ee.lic ### for zOS host, enable this line to add your license
RUN echo "PassEnv /opt/ibm/dsdriver/lib" > /etc/httpd/conf.d/db2-lib.conf
RUN chown -R 1001:0 /opt/ibm/dsdriver
RUN chown -R 1001:0  /var/www/html
USER 1001
RUN composer install --no-interaction
USER root
ADD ./patch2.sh /patch2.sh
RUN bash /patch2.sh && rm /patch2.sh
user 1001
ENTRYPOINT ["httpd", "-D", "FOREGROUND"]

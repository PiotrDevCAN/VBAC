#!/bin/bash
#echo "<?php phpinfo();?>" > /var/www/html/index.php
echo 'auto_prepend_file="php/siteheader.php"' > /etc/php.d/rob.ini
echo 'auto_append_file="php/sitefooter.php"' >> /etc/php.d/rob.ini
echo 'memory_limit=512M' >> /etc/php.d/rob.ini

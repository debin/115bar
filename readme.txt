1 copy name rename the file below:
    115app/conf/application.ini.default
    115app/conf/115zone.ini.default
    115app/robots.txt.default
    115app/application/library/Environment.php.default

2 config file
    115app/application/library/ConfigPg.php
    115app/application/library/ConfigRedis.php
    115app/conf/115zone.ini

3 composer
    cd 115app/
    php composer.phar install

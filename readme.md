
### framework
* php
* PostgreSQL
* Redis
* xunsearch

### config file
* copy name rename the file below:
    * 115app/conf/application.ini.default
    * 115app/conf/115zone.ini.default
    * 115app/robots.txt.default
    * 115app/application/library/Environment.php.default

* config file
    * 115app/application/library/ConfigPg.php
    * 115app/application/library/ConfigRedis.php
    * 115app/conf/115zone.ini

* composer
    * cd 115app/
    * php composer.phar install

* log file
    * /opt/log
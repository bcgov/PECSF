#!/bin/sh

/usr/local/bin/apache2-foreground

cd /var/www/html

touch abc.txt

nohup php artisan queue:work --tries=3 --timeout=0 --memory=512 > ./storage/logs/queue-work-1.log &
echo $?
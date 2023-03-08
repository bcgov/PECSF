#!/bin/sh

/usr/local/bin/apache2-foreground

cd /var/www/html

nohup php artisan queue:work --tries=3 --timeout=0 --memory=512 > ./storage/logs/queue-work.log &
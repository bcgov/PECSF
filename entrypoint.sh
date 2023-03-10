#!/bin/sh

/usr/local/bin/apache2-foreground

wait

cd /var/www/html


nohup php artisan queue:work --tries=3 --timeout=0 --memory=512 > ./storage/logs/queue-work-1.log &

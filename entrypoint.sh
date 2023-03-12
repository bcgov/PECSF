#!/bin/sh

/usr/local/bin/apache2-foreground

wait

echo "Apache started" >> /var/www/html/storage/logs/apache-dummy.log

cd /var/www/html


nohup php artisan queue:work --tries=3 --timeout=0 --memory=512 > ./storage/logs/queue-work-1.log &

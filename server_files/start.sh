#!/usr/bin/env bash

cd /var/www/html

# Queue 
nohup php artisan queue:work  --daemon --tries=3 --timeout=0 > ./storage/logs/queue-work.log </dev/null>/dev/null 2>&1 &

# Schedule (required manual start)
#nohup php artisan schedule:work --verbose --no-interaction </dev/null>/dev/null 2>&1 &
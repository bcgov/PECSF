#!/usr/bin/env bash

cd /app

# Queue 
nohup php artisan queue:work --tries=3 --timeout=0 --memory=512 > ./storage/logs/queue-work.log &

# Schedule (required manual start)
nohup php artisan schedule:work --verbose --no-interaction &

# App 
php artisan serve --host=0.0.0.0 --port=8000

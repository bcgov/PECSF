#!/bin/bash
while true; do
  /usr/local/bin/php /var/www/html/admin/cli/cron.php >&1
  sleep 60
done

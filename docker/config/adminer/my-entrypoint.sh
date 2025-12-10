#!/bin/sh
set -e

cp -r /var/www/certs/ /var/www/html/


chown adminer /var/www/html/certs/postgres/client.crt
chown adminer /var/www/html/certs/postgres/client.key
chown adminer /var/www/html/certs/postgres/rootCA.crt

chmod 0600 /var/www/html/certs/postgres/client.crt
chmod 0600 /var/www/html/certs/postgres/client.key
chmod 0600 /var/www/html/certs/postgres/rootCA.crt 

/usr/local/bin/entrypoint.sh

exec "$@"



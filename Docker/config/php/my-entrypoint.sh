#!/bin/sh
set -e

echo 'copying certificates...'

mkdir -p /var/certs/www-data
mkdir -p /var/certs/root

cp -r /var/www/certificates/postgres/ /var/certs/root/
cp -r /var/www/certificates/postgres/ /var/certs/www-data/   

chown root /var/certs/root/postgres/client.crt
chown root /var/certs/root/postgres/client.key
chown root /var/certs/root/postgres/rootCA.crt
chown www-data /var/certs/www-data/postgres/client.crt
chown www-data /var/certs/www-data/postgres/client.key
chown www-data /var/certs/www-data/postgres/rootCA.crt

chmod 0600 /var/certs/root/postgres/client.crt
chmod 0600 /var/certs/root/postgres/client.key
chmod 0600 /var/certs/root/postgres/rootCA.crt 
chmod 0600 /var/certs/www-data/postgres/client.crt
chmod 0600 /var/certs/www-data/postgres/client.key
chmod 0600 /var/certs/www-data/postgres/rootCA.crt 


docker-php-entrypoint

exec "$@"



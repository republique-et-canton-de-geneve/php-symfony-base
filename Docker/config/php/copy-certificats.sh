#!/bin/bash
chmod a+rw -R ./certs/*

cp ./Docker/config/postgres/certs/client.crt ./certs/postgres/www-data/client.crt
cp ./Docker/config/postgres/certs/client.key ./certs/postgres/www-data/client.key
cp ./Docker/config/postgres/certs/rootCA.crt ./certs/postgres/www-data/rootCA.crt
chown www-data ./certs/postgres/www-data/client.crt
chown www-data ./certs/postgres/www-data/client.key
chown www-data ./certs/postgres/www-data/rootCA.crt
chmod 0600 ./certs/postgres/www-data/client.crt
chmod 0600 ./certs/postgres/www-data/client.key
chmod 0600 ./certs/postgres/www-data/rootCA.crt


cp ./Docker/config/postgres/certs/client.crt ./certs/postgres/root/client.crt
cp ./Docker/config/postgres/certs/client.key ./certs/postgres/root/client.key
cp ./Docker/config/postgres/certs/rootCA.crt ./certs/postgres/root/rootCA.crt
chown root ./certs/postgres/root/client.crt
chown root ./certs/postgres/root/client.key
chown root ./certs/postgres/root/rootCA.crt
chmod 0600 ./certs/postgres/root/client.crt
chmod 0600 ./certs/postgres/root/client.key
chmod 0600 ./certs/postgres/root/rootCA.crt
adminer
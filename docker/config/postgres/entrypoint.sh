#!/bin/bash
set -e

cp /var/lib/postgresql/certificates/server.crt /var/lib/postgresql/server.crt
cp /var/lib/postgresql/certificates/server.key /var/lib/postgresql/server.key
cp /var/lib/postgresql/certificates/rootCA.crt /var/lib/postgresql/rootCA.crt


# Update file permissions of certificates
chmod 600 /var/lib/postgresql/server.* /var/lib/postgresql/rootCA.crt
chown postgres:postgres /var/lib/postgresql/server.* /var/lib/postgresql/rootCA.crt

# Run the base entrypoint 
docker-entrypoint.sh postgres


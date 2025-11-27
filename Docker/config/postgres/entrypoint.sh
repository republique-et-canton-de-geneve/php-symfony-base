#!/bin/bash
set -e

# Update file permissions of certificates
chmod 600 /var/lib/postgresql/server.* /var/lib/postgresql/rootCA.crt
chown postgres:postgres /var/lib/postgresql/server.* /var/lib/postgresql/rootCA.crt

# Run the base entrypoint 
docker-entrypoint.sh postgres


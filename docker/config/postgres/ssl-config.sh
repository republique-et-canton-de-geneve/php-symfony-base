#!/bin/bash
# be careful, this script is executed only once, if the databse image doesn't exist !
# if you modify this script, you need to destroy your postgres data image 

set -e

# Configure PostgreSQL to use SSL
echo "ssl = on" >> /var/lib/postgresql/data/postgresql.conf
echo "ssl_cert_file = '/var/lib/postgresql/server.crt'" >> /var/lib/postgresql/data/postgresql.conf
echo "ssl_key_file = '/var/lib/postgresql/server.key'" >> /var/lib/postgresql/data/postgresql.conf
echo "ssl_ca_file = '/var/lib/postgresql/rootCA.crt'" >> /var/lib/postgresql/data/postgresql.conf 


# Enforce SSL for all connections
echo "hostssl all all all cert clientcert=verify-full" > /var/lib/postgresql/data/pg_hba.conf
echo "hostnossl all all all reject" >> /var/lib/postgresql/data/pg_hba.conf




#!/bin/sh
#set -e

#cp /var/www/certs/client.crt /var/www/html/client.crt
#cp /var/www/certs/client.key /var/www/html/client.key
#cp /var/www/certs/rootCA.crt /var/www/html/rootCA.crt

#chown adminer /var/www/html/client.crt
#chown adminer /var/www/html/client.key
#chown adminer /var/www/html/rootCA.crt

#chmod 0600 /var/www/html/client.crt
#chmod 0600 /var/www/html/client.key
#chmod 0600 /var/www/html/rootCA.crt 


#exec "$@"

#!/bin/sh
set -e

entrypoint.sh

exec "$@"



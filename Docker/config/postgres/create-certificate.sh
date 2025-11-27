#!/bin/bash
export $(grep -v '^#' ../../.env | xargs)
sudo chmod a+rw ./certs/*

# Generate a Certificate Authority (CA)
openssl genrsa -out ./certs/rootCA.key 2048
openssl req -x509 -new -nodes -key ./certs/rootCA.key -sha256 -days 36500 -out ./certs/rootCA.crt \
  -subj "/C=CH/ST=GENEVE/L=GENEVE/O=ETAT/OU=OCSIN/CN=localhostCA"

  # Create Server Certificate Files
openssl genrsa -out ./certs/server.key 2048
openssl req -new -key ./certs/server.key -out ./certs/server.csr \
 -subj "/C=CH/ST=GENEVE/L=GENEVE/O=ETAT/OU=Database/CN=localhost"
openssl x509 -req -in ./certs/server.csr -CA ./certs/rootCA.crt -CAkey ./certs/rootCA.key -CAcreateserial -out ./certs/server.crt -days 36500 -sha256 \
-extfile <(printf "subjectAltName=DNS:localhost,DNS:postgres,IP:127.0.0.1")



# Create Client Certificate Files
openssl genrsa -out ./certs/client.key 2048
openssl req -new -key ./certs/client.key -out ./certs/client.csr \
-subj "/C=CH/ST=GENEVE/L=GENEVE/O=ETAT/OU=Developers/CN=${DB_USER}"
openssl x509 -req -in ./certs/client.csr -CA ./certs/rootCA.crt -CAkey ./certs/rootCA.key -CAcreateserial -out ./certs/client.crt -days 36500 -sha256

sudo chmod 0600 ./certs/*
sudo chown user:user ./certs/*




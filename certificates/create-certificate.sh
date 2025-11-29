#!/bin/bash
export $(grep -v '^#' ../Docker/.env | xargs)
sudo chmod -R a+rw ./postgres/*

# Generate a Certificate Authority (CA)
openssl genrsa -out ./postgres/rootCA.key 2048
openssl req -x509 -new -nodes -key ./postgres/rootCA.key -sha256 -days 36500 -out ./postgres/rootCA.crt \
  -subj "/C=CH/ST=GENEVE/L=GENEVE/O=ETAT/OU=OCSIN/CN=localhostCA"

  # Create Server Certificate Files
openssl genrsa -out ./postgres/server.key 2048
openssl req -new -key ./postgres/server.key -out ./postgres/server.csr \
 -subj "/C=CH/ST=GENEVE/L=GENEVE/O=ETAT/OU=Database/CN=postgres"
openssl x509 -req -in ./postgres/server.csr -CA ./postgres/rootCA.crt -CAkey ./postgres/rootCA.key -CAcreateserial -out ./postgres/server.crt -days 36500 -sha256 \
-extfile <(printf "subjectAltName=DNS:localhost,DNS:postgres,IP:127.0.0.1")

# Create Client Certificate Files
openssl genrsa -out ./postgres/client.key 2048
openssl req -new -key ./postgres/client.key -out ./postgres/client.csr \
-subj "/C=CH/ST=GENEVE/L=GENEVE/O=ETAT/OU=Developers/CN=${DB_USER}"
openssl x509 -req -in ./postgres/client.csr -CA ./postgres/rootCA.crt -CAkey ./postgres/rootCA.key -CAcreateserial -out ./postgres/client.crt -days 36500 -sha256

sudo chmod 0666 ./postgres/*
#sudo chown user:user ./postgres/*




#!/usr/bin/env bash

DESTINATION="/tmp"
PASSWORD="1234"
KEYSIZE="1024"
VALID_UNTIL=36500

if [[ $1 == '-h' || $1 == '--help' ]]; then
    echo "Usage: $0 <destination directory>"
    exit
fi
if [[ $1 == '-d' || $1 == '--destination' ]]; then
    DESTINATION="${2:-/tmp}"
fi

PEM_DSA_DIR="${DESTINATION}/resources/certificates/dsa-pem"

declare -a CERTS=(
  "signed"
  "other"
  "expired"
  "corrupted"
  "broken"
)

## Generate directory structure ##

mkdir $DESTINATION/certificates
touch $DESTINATION/index.txt
echo 01 > $DESTINATION/serial.txt
mkdir $PEM_DSA_DIR

## Generate config file

cat>/tmp/ca.conf<<'EOF'
[ ca ]
default_ca = ca_default

[ ca_default ]
dir = REPLACE_LATER
new_certs_dir = $dir/certificates/dsa-pem
database = /tmp/index.txt
serial = /tmp/serial.txt
default_md = sha256
policy = generic_policy

[ generic_policy ]
countryName = optional
stateOrProvinceName = optional
localityName = optional
organizationName = optional
organizationalUnitName = optional
commonName = supplied
emailAddress = optional
EOF

sed -i "s|REPLACE_LATER|${DESTINATION}|" ${DESTINATION}/ca.conf

## Generate dsaparam file
openssl dsaparam -out $PEM_DSA_DIR/dsaparam.pem 1024
DSA_PARAM="${PEM_DSA_DIR}/dsaparam.pem"

## Create CA ##

SUBJ="/emailAddress=noreply@simplesamlphp.org/CN=SimpleSAMLphp\ Testing\ CA/O=SimpleSAMLphp\ HQ/L=Honolulu/ST=Hawaii/C=US"

openssl req -x509 -newkey dsa:$DSA_PARAM -keyout $PEM_DSA_DIR/simplesamlphp.org-ca_nopasswd.key \
  -out $PEM_DSA_DIR/simplesamlphp.org-ca.crt -subj "${SUBJ}" -nodes

openssl dsa -aes256 -in $PEM_DSA_DIR/simplesamlphp.org-ca_nopasswd.key \
  -out $PEM_DSA_DIR/simplesamlphp.org-ca.key -passout pass:$PASSWORD

## Generate certificates

for CERT in "${CERTS[@]}"
do
  SUBJ="/emailAddress=noreply@simplesamlphp.org/CN=${CERT}.simplesamlphp.org/O=SimpleSAMLphp\ HQ/L=Honolulu/ST=Hawaii/C=US"

  openssl req -out $PEM_DSA_DIR/$CERT.simplesamlphp.org.csr -subj "${SUBJ}" -nodes \
    -keyout $PEM_DSA_DIR/$CERT.simplesamlphp.org_nopasswd.key -newkey dsa:$DSA_PARAM

  openssl dsa -aes256 -in $PEM_DSA_DIR/$CERT.simplesamlphp.org_nopasswd.key \
    -out $PEM_DSA_DIR/$CERT.simplesamlphp.org.key -passout pass:$PASSWORD

  if [[ "$CERT" == "expired" ]]; then
    openssl ca -keyfile $PEM_DSA_DIR/simplesamlphp.org-ca_nopasswd.key -cert $PEM_DSA_DIR/simplesamlphp.org-ca.crt \
      -in $PEM_DSA_DIR/$CERT.simplesamlphp.org.csr -out $PEM_DSA_DIR/$CERT.simplesamlphp.org.crt -enddate $(date '+%y%m%d%H%M%SZ') \
      -config /tmp/ca.conf -batch -subj "${SUBJ}"
  else
    openssl ca -keyfile $PEM_DSA_DIR/simplesamlphp.org-ca_nopasswd.key -cert $PEM_DSA_DIR/simplesamlphp.org-ca.crt \
      -in $PEM_DSA_DIR/$CERT.simplesamlphp.org.csr -out $PEM_DSA_DIR/$CERT.simplesamlphp.org.crt \
      -days $VALID_UNTIL -config /tmp/ca.conf -batch -subj "${SUBJ}"
  fi

  sed -i '/-----BEGIN CERTIFICATE-----/,$!d' $PEM_DSA_DIR/$CERT.simplesamlphp.org.crt
done

## Self-signed certificate

SUBJ="/emailAddress=noreply@simplesamlphp.org/CN=selfsigned.simplesamlphp.org/O=SimpleSAMLphp\ HQ/L=Honolulu/ST=Hawaii/C=US"

openssl req -x509 -newkey dsa:$DSA_PARAM -keyout $PEM_DSA_DIR/selfsigned.simplesamlphp.org_nopasswd.key \
  -out $PEM_DSA_DIR/selfsigned.simplesamlphp.org.crt -subj "${SUBJ}" -nodes

openssl dsa -aes256 -in $PEM_DSA_DIR/selfsigned.simplesamlphp.org_nopasswd.key \
  -out $PEM_DSA_DIR/selfsigned.simplesamlphp.org.key -passout pass:$PASSWORD

## Corrupt certificate ##

# This sed-command will swap the first & last character
sed 's/^\(.\)\(.\+\)\(.\)$/\3\2\1/' $PEM_DSA_DIR/corrupted.simplesamlphp.org_nopasswd.key \
  > $PEM_DSA_DIR/corrupted.simplesamlphp.org.key
mv $PEM_DSA_DIR/corrupted.simplesamlphp.org.crt $PEM_DSA_DIR/corrupted.simplesamlphp.org.tmp
sed 's/^\(.\)\(.\+\)\(.\)$/\3\2\1/' $PEM_DSA_DIR/corrupted.simplesamlphp.org.tmp \
  > $PEM_DSA_DIR/corrupted.simplesamlphp.org.crt
rm $PEM_DSA_DIR/corrupted.simplesamlphp.org_nopasswd.key

## Break PEM structure ##

# This sed-command will swap the first & last character
sed 's/ //g' $PEM_DSA_DIR/broken.simplesamlphp.org_nopasswd.key > $PEM_DSA_DIR/broken.simplesamlphp.org.key
mv $PEM_DSA_DIR/broken.simplesamlphp.org.crt $PEM_DSA_DIR/broken.simplesamlphp.org.tmp
sed 's/ //g' $PEM_DSA_DIR/broken.simplesamlphp.org.tmp > $PEM_DSA_DIR/broken.simplesamlphp.org.crt
rm $PEM_DSA_DIR/broken.simplesamlphp.org_nopasswd.key

## Clean-up csr- and ca-files
rm -f $PEM_DSA_DIR/*.csr
rm -f $DESTINATION/ca.conf
rm -f $DESTINATION/index.txt*
rm -f $DESTINATION/serial.txt*
rm -f $PEM_DSA_DIR/*.pem
rm -f $PEM_DSA_DIR/*.tmp

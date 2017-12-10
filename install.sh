#!/bin/bash

if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit
fi

if [ ! -f antpub.phar ]; then
    echo "antpub.phar is not here. Re-building phar..."
    php buildphar.php
fi

if [ ! -d  /usr/src/antpub/ ]; then
    mkdir /usr/src/antpub/
fi

chmod +x antpub.phar

mv antpub.phar /usr/src/antpub/antpub.phar

if [ ! -L /usr/local/bin/publish ]; then
    cd /usr/local/bin/
    ln -s /usr/src/antpub/antpub.phar publish
fi
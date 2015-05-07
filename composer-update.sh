#!/bin/bash

hadTransferSymlink=false

if [ -L "vendor/olcs/olcs-transfer" ]
then
    echo "Removing symlink"
    hadTransferSymlink=true
    rm vendor/olcs/olcs-transfer
fi

composer update

if [ "$hadTransferSymlink" = true ] ;
then
    echo "Recreating symlink"
    rm -rf vendor/olcs/olcs-transfer
    cd vendor/olcs && ln -s ../../../olcs-transfer/ olcs-transfer
fi

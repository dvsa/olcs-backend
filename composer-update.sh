#!/bin/bash

hadCommonSymlink=false
hadTransferSymlink=false
hadUtilsSymlink=false

if [ -L "vendor/olcs/OlcsCommon" ]
then
    echo "Removing olcs-common symlink"
    hadCommonSymlink=true
    rm vendor/olcs/OlcsCommon
fi

if [ -L "vendor/olcs/olcs-transfer" ]
then
    echo "Removing olcs-transfer symlink"
    hadTransferSymlink=true
    rm vendor/olcs/olcs-transfer
fi

if [ -L "vendor/olcs/olcs-utils" ]
then
    echo "Removing olcs-utils symlink"
    hadUtilsSymlink=true
    rm vendor/olcs/olcs-utils
fi

if [ -L "vendor/olcs/olcs-xmltools" ]
then
    echo "Removing olcs-xmltools symlink"
    hadXmlSymlink=true
    rm vendor/olcs/olcs-xmltools
fi

if [ -f composer.phar ] ;
then
    php composer.phar update
else
    composer update
fi

if [ "$hadCommonSymlink" = true ] || [ "$1" = "--force" ];
then
    if [ -d "vendor/olcs/OlcsCommon" ];
    then
        echo "Recreating olcs-common symlink"
        rm -rf vendor/olcs/OlcsCommon
        (cd vendor/olcs && ln -s ../../../olcs-common/ OlcsCommon)
    fi
fi

if [ "$hadTransferSymlink" = true ] || [ "$1" = "--force" ];
then
    if [ -d "vendor/olcs/olcs-transfer" ] ;
    then
        echo "Recreating olcs-transfer symlink"
        rm -rf vendor/olcs/olcs-transfer
        (cd vendor/olcs && ln -s ../../../olcs-transfer/ olcs-transfer)
    fi
fi

if [ "$hadUtilsSymlink" = true ] || [ "$1" = "--force" ];
then
    if [ -d "vendor/olcs/olcs-utils" ] ;
    then
        echo "Recreating olcs-utils symlink"
        rm -rf vendor/olcs/olcs-utils
        (cd vendor/olcs && ln -s ../../../olcs-utils/ olcs-utils)
    fi
fi

if [ "$hadXmlSymlink" = true ] || [ "$1" = "--force" ];
then
    if [ -d "vendor/olcs/olcs-xmltools" ] ;
    then
        echo "Recreating olcs-xmltools symlink"
        rm -rf vendor/olcs/olcs-xmltools
        (cd vendor/olcs && ln -s ../../../olcs-xmltools/ olcs-xmltools)
    fi
fi

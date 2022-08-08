#!/bin/bash

interfaces=`ifconfig -a | grep flags | awk -F ':' '{print $1}'`

for eth in $interfaces; do
    
    check_Link=`ethtool $eth | grep Link | awk -F ': ' '{print $2}'`
    if [ "$check_Link" = "yes" ]
    then
        echo $eth
    fi
done
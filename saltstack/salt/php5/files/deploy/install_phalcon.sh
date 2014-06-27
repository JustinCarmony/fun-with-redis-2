#!/bin/bash

cd /tmp
rm -rf cphalcon
sleep 1

git clone git://github.com/phalcon/cphalcon.git
cd cphalcon/build
sudo ./install
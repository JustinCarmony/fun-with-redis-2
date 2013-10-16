#!/bin/bash

cd /tmp
rm -rf cphalcon

git clone git://github.com/phalcon/cphalcon.git
cd cphalcon/build
sudo ./install
#!/bin/bash

sudo dd if=/dev/zero of=/swapfile bs=1024 count=1024k
mkswap /swapfile

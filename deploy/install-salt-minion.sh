#!/bin/bash

sudo cat /vagrant/deploy/hosts/$1 > /etc/hosts
sudo echo "$1.saltdemo.com" > /etc/hostname

#rm /root/provision-finished.txt

if [ ! -f /root/provision-finished.txt ]; then

	echo "Installing Minion"

    # Install Minion via Bootstrap
	wget -O - http://bootstrap.saltstack.org | sudo sh

	touch /root/provision-finished.txt

fi

cat /vagrant/deploy/config/minion > /etc/salt/minion

service salt-minion restart


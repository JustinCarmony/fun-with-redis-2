#!/bin/bash

sudo cat /vagrant/deploy/hosts/$1 > /etc/hosts

#rm /root/provision-finished.txt

if [ ! -f /root/provision-finished.txt ]; then

	echo "Installing Minion"

    # Install Minion via Bootstrap
	curl -L http://bootstrap.saltstack.org | sudo sh -s -- git develop

	touch /root/provision-finished.txt

fi

cat /vagrant/deploy/config/minion > /etc/salt/minion

service salt-minion restart


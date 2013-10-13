#!/bin/bash

# Copy over new hosts file

sudo cat /vagrant/deploy/hosts/$1 > /etc/hosts

# Remove provision finished to re-run
#rm /root/provision-finished.txt

if [ ! -f /root/provision-finished.txt ]; then
	echo "Installing Master"

	# Use Salt Bootstrap to install Master!
	curl -L http://bootstrap.saltstack.org | sudo sh -s -- -M -N git develop

	# Install the Minion!
	curl -L http://bootstrap.saltstack.org | sudo sh -s -- git develop

	ln -s /vagrant/saltstack/salt /srv/salt
	ln -s /vagrant/saltstack/pillar /srv/pillar

	cat /vagrant/deploy/config/master > /etc/salt/master
	cat /vagrant/deploy/config/minion > /etc/salt/minion

	service salt-master restart
	service salt-minion restart

	touch /root/provision-finished.txt
fi


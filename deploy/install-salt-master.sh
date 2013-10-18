#!/bin/bash

# Copy over new hosts file

cat /vagrant/deploy/hosts/$1 > /etc/hosts
echo "$1.saltdemo.com" > /etc/hostname

# Remove provision finished to re-run
#rm /root/provision-finished.txt

cat /vagrant/deploy/config/master > /etc/salt/master
cat /vagrant/deploy/config/minion > /etc/salt/minion

if [ ! -f /root/provision-finished.txt ]; then
	echo "Installing Master"

	# Use Salt Bootstrap to install Master & Minion!
	wget -O - http://bootstrap.saltstack.org | sudo sh -s -- -M

	ln -s /vagrant/saltstack/salt /srv/salt
	ln -s /vagrant/saltstack/pillar /srv/pillar

	cat /vagrant/deploy/config/master > /etc/salt/master
	cat /vagrant/deploy/config/minion > /etc/salt/minion

	touch /root/provision-finished.txt

	service salt-master restart
	service salt-minion restart
fi


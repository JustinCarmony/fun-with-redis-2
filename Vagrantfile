# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

# Load External Preferences
require File.join(File.dirname(__FILE__), './', 'config/prefs.rb')

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Use AWS for all Servers
  config.vm.provider :aws do |aws, override|
    aws.access_key_id = $aws_access_key_id
    aws.secret_access_key = $aws_secret_access_key
    aws.keypair_name = $aws_keypair_name
    
    aws.ami = $aws_ami
    aws.region = $aws_region

    override.ssh.username = $aws_ssh_username
    override.ssh.private_key_path = $aws_ssh_private_key_path
  end

  config.vm.define :master do |master|
    master.vm.provider "aws" do |aws|
      aws.tags = {
        'Name' => 'redis-demo-master'
      }
    end
    master.vm.provision :shell, :inline => "bash /vagrant/deploy/install-salt-master.sh master"
    master.vm.box = "dummy"
  end

  $num_of_clients.times do |count|
    client_num = count + 1
    config.vm.define "client" + client_num.to_s do |client|
      client.vm.provider "aws" do |aws|
        aws.tags = {
          'Name' => 'redis-demo-client' + client_num.to_s
        }
      end
      client.vm.provision :shell, :inline => "bash /vagrant/deploy/install-salt-minion.sh client" + client_num.to_s
      client.vm.box = "dummy"
    end
  end


end

common:
    pkg.installed:
        - names:
            - build-essential
            - git-core
            - htop
            - strace
            - gcc
            - autoconf
            - siege
            - python-software-properties
            - ruby1.9.1
            - ruby1.9.1-dev
            - gawk
            - psutils
            - python-psutil

supervisor:
    pkg.installed:
        - require:
            - pkg: common
    service.running:
        - require:
            - pkg: supervisor

/etc/init.d/supervisor:
    file.managed:
        - source: salt://common/files/supervisor
        - mode: 755
        - require:
            - pkg: supervisor

/var/deploy:
    file.directory:
        - user: root
        - group: root

/var/deploy/install_swap.sh:
    file.managed:
        - source: salt://common/files/install_swap.sh
    cmd.run:
        - name: bash /var/deploy/install_swap.sh
        - unless: ls /swapfile
        - require:
            - file: /var/deploy/install_swap.sh

/swapfile:
  mount:
    - swap
    - require:
        - cmd: /var/deploy/install_swap.sh

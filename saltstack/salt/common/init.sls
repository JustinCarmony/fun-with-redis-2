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

supervisor:
    pkg.installed:
        - require:
            - pkg: common
    service.running:
        - require:
            - pkg: supervisor


/var/deploy:
    file.directory:
        - user: root
        - group: root



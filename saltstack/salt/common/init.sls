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
            - supervisor
            - ruby1.9.1
            - ruby1.9.1-dev

/var/deploy:
    file.directory:
        - user: root
        - group: root



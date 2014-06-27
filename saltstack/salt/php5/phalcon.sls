include:
    - common
    - php5
    - php5.fpm

libpcre3-dev:
    pkg:
        - installed

/var/deploy/install_phalcon.sh:
    file.managed:
        - source: salt://php5/files/deploy/install_phalcon.sh
        - require:
            - file: /var/deploy

bash /var/deploy/install_phalcon.sh:
    cmd.run:
        - unless: php -i | grep phalcon
        - require:
            - file: /var/deploy/install_phalcon.sh
            - pkg: libpcre3-dev
        - watch_in:
            - service: php5-fpm


/etc/php5/fpm/conf.d/phalcon.ini:
    file.managed:
        - contents: extension=phalcon.so
        - require:
            - cmd: bash /var/deploy/install_phalcon.sh
        - watch_in:
            - service: php5-fpm

/etc/php5/cli/conf.d/phalcon.ini:
    file.managed:
        - contents: extension=phalcon.so
        - require:
            - cmd: bash /var/deploy/install_phalcon.sh
        - watch_in:
            - service: php5-fpm
include:
    - common

php5-fpm:
    pkg.installed:
        - require:
            - pkg: php5
    service.running:
        - require:
            - pkg: php5-fpm

/etc/php5/fpm/pool.d/www.conf:
    file.managed:
        - source: salt://php5/files/pools.d/www.conf
        - require:
            - pkg: php5-fpm
        - watch_in:
            - service: php5-fpm
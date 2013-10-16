include:
    - common

php5:
    pkg.installed:
        - names:
            - php5
            - php5-cli
            - php5-dev
            - php5-mysql

php5-fpm:
    pkg.installed:
        - require:
            - pkg: php5
    service.running:
        - require:
            - pkg: php5-fpm



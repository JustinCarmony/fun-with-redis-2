include:
    - common
    - redis
    - nginx
    - php5
    - php5.phalcon

gem install redis-stat:
    cmd.run:
        - unless: which redis-stat
        - require:
            - pkg: common
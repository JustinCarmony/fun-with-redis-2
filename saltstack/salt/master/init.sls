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

## Supervisor for the worker
/etc/supervisor/conf.d/client_worker.conf:
    file.managed:
        - mode: 755
        - contents: |
            [program:master_worker]
            command=/usr/bin/php /vagrant/src/app/bin/master_worker.php
            process_name=client_worker.php %(process_num)s
            stdout_logfile=/var/log/master_worker.stdout.log
            stderr_logfile=/var/log/master_worker.stderr.log
            autostart=true
            autorestart=true
            startsecs=10
            stopwaitsecs=600
        - watch_in:
            - service: supervisor
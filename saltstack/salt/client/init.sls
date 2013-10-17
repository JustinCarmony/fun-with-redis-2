include:
    - common
    - php5
    - php5.phalcon
    - redis

## Supervisor for the worker
/etc/supervisor/conf.d/client_worker.conf:
    file.managed:
        - mode: 755
        - contents: |
            [program:client_worker]
            numprocs=10
            numprocs_start=1
            command=/usr/bin/php /vagrant/src/app/bin/client_worker.php %(process_num)s
            process_name=client_worker.php %(process_num)s
            stdout_logfile=/var/log/client_worker%(process_num)s.stdout.log
            stderr_logfile=/var/log/client_worker%(process_num)s.stderr.log
            autostart=true
            autorestart=true
            startsecs=10
            stopwaitsecs=600
        - watch_in:
            - service: supervisor

extend:
# Since we don't want a redis server to actually run, but we want the redis-client
# and redis-benchmark, lets just disable the redis server
    redis-server:
        service:
            - disabled
            - dead
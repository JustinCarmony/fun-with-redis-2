include:
    - common
    - php5
    - php5.phalcon
    - redis

# Since we don't want a redis server to actually run, but we want the redis-client
# and redis-benchmark, lets just disable the redis server

extend:
    redis-server:
        service:
            - disabled
            - dead
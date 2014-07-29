include:
    - common
    - php5
    - redis
    
extend:
# Since we don't want a redis server to actually run, but we want the redis-client
# and redis-benchmark, lets just disable the redis server
    redis-server:
        service:
            - disabled
            - dead
include:
    - common

ppa:chris-lea/redis-server:
    cmd.run:
        - name: add-apt-repository --yes ppa:chris-lea/redis-server
        - unless: ls /etc/apt/sources.list.d/chris-lea-redis-server-precise.list

redis-server:
    pkg.installed:
        - require:
            - cmd: ppa:chris-lea/redis-server
    service.running:
        - require:
            - pkg: redis-server

/etc/redis/redis.conf:
    file.managed:
        - source: salt://redis/files/etc/redis.conf
        - watch_in:
            - service: redis-server
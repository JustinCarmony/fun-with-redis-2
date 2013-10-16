include:
    - common

nginx:
    pkg.installed:
        - require: 
            - pkg: common
    service.running:
        - require:
            - pkg: nginx

/etc/nginx/nginx.conf:
    file.managed:
        - source: salt://nginx/files/etc/nginx.conf
        - watch_in:
            - service: nginx

/etc/nginx/sites-available/default:
    file.managed:
        - source: salt://nginx/files/etc/sites-available/default
        - watch_in:
            - service: nginx

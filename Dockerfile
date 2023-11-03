FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Sy --noconfirm nginx php-fpm
COPY ./files /srv/
RUN cp /srv/nginx.conf /etc/nginx/nginx.conf

ENTRYPOINT ["/srv/start.sh"]

## Running (alias d = "doas docker")
# build and run detached (for running bash replace -d with -it)
# $ d build ./ -t test:latest && d run --rm -d --name tda -p 80:80 test
# kill the container with
# $ d kill tda
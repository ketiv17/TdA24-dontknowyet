FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Sy --noconfirm nginx php-fpm
COPY ./files /srv/
RUN cp /srv/nginx.conf /etc/nginx/nginx.conf
RUN chmod +rx ./http/*

ENTRYPOINT ["/srv/start.sh"]
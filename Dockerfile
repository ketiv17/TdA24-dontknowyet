FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Sy --noconfirm nginx php-fpm nodejs npm
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

ENTRYPOINT ["/srv/start.sh"]
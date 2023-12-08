FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Sy --noconfirm nginx php-fpm php-mysqli
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf
RUN echo "extension=mysqli.so" > /etc/php/php.ini

ENTRYPOINT ["/srv/start.sh"]
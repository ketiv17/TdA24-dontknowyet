FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Syu --noconfirm && \
    pacman -S --noconfirm nginx php php-fpm && \
    echo "extension=mysqli.so" >> /etc/php/php.ini
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

ENTRYPOINT ["/srv/start.sh"]
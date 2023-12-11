FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Sy --noconfirm nginx php-fpm
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

# Take the DB_PASSWORD action secret and do something with it (@fretka change the file it writes to)
ARG DB_PASSWORD
RUN echo $DB_PASSWORD >> /src/http/password

# Enable mysqli extension
RUN echo "extension=mysqli.so" >> /etc/php/php.ini

ENTRYPOINT ["/srv/start.sh"]
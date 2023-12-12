FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Sy --noconfirm nginx php-fpm nodejs npm
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

# Take the DB_PASSWORD action secret and make it an enviromental variable
ARG DB_PASSWORD
ENV DB_PASSWORD=$DB_PASSWORD

# Enable mysqli extension
RUN echo "extension=mysqli.so" >> /etc/php/php.ini

ENTRYPOINT ["/srv/start.sh"]
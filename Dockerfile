FROM archlinux

EXPOSE 80

WORKDIR /srv
RUN pacman -Sy --noconfirm nginx php-fpm
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

# Take the DB_PASSWORD action secret and make it enviromental variable
ARG DB_PASSWORD
ENV DB_PASSWORD=$DB_PASSWORD

# Enable mysqli extension
RUN echo "extension=mysqli.so" >> /etc/php/php.ini

# Making enviromental variables usable in php
RUN echo "clear_env = no" >> /etc/php/php-fpm.d/www.conf

ENTRYPOINT ["/srv/start.sh"]
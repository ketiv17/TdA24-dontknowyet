FROM archlinux

EXPOSE 80
RUN pacman -Sy --noconfirm nginx php-fpm unzip
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

## frontend
WORKDIR /srv/bun/
RUN curl -fsSL https://bun.sh/install | bash
ENV PATH=$PATH:/root/.bun/bin
RUN bun install
RUN bun run build


## backend
# Take the DB_PASSWORD action secret and make it enviromental variable
ARG DB_PASSWORD
ENV DB_PASSWORD=$DB_PASSWORD
# Enable mysqli extension
RUN echo "extension=mysqli.so" >> /etc/php/php.ini
# Making enviromental variables usable in php
RUN echo "clear_env = no" >> /etc/php/php-fpm.d/www.conf

ENTRYPOINT ["/srv/start.sh"]
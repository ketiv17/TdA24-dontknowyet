FROM archlinux

EXPOSE 80

# Installation
RUN pacman -Sy --noconfirm nginx php-fpm unzip
RUN curl -fsSL https://bun.sh/install | bash
ENV PATH=$PATH:/root/.bun/bin

# Copy relevant files
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

## frontend
WORKDIR /srv/bun/
RUN bun install
RUN bun run build

## backend
# Take the DB_PASSWORD & TDA_API_PASS action secret and make it enviromental variable
ARG DB_PASSWORD
ENV DB_PASSWORD=$DB_PASSWORD
ARG OPENAI_API_KEY
ENV OPENAI_API_KEY=$OPENAI_API_KEY
# Enable mysqli extension
RUN echo "extension=mysqli.so" >> /etc/php/php.ini
# Making enviromental variables usable in php
RUN echo "clear_env = no" >> /etc/php/php-fpm.d/www.conf

ENTRYPOINT ["/srv/start.sh"]
FROM archlinux

EXPOSE 80

WORKDIR /srv
COPY ./files /srv/
RUN mv /srv/nginx.conf /etc/nginx/nginx.conf

ENTRYPOINT ["/srv/start.sh"]
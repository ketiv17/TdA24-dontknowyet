FROM archlinux

RUN pacman -Sy nginx
RUN nginx
CMD ["echo","done!"]


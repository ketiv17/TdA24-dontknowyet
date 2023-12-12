handy for copy&paste (full docker reload) \
```d kill tda && d build ./ -t tda:latest --build-arg DB_PASSWORD="password" && d run --rm -d --name tda -p 80:80 tda```\
\
alternativly you can run\
```d kill tda && d build ./ -t tda:latest --build-arg DB_PASSWORD="password" && d run --rm -it --name tda -p 80:80 tda```\
after you replace `tail -f /dev/null` with `bash` on the last line of files/start.sh\
to get into bash (on the docker)
handy for copy&paste (full docker reload) \
```docker kill tda ; docker build ./ -t tda:latest --build-arg DB_PASSWORD="DBpassword" --build-arg TDA_API_PASS='apiPassword' && docker run --rm -d --name tda -p 80:80 tda```\
\
alternativly you can run\
```docker kill tda ; docker build ./ -t tda:latest --build-arg DB_PASSWORD="DBpassword" --build-arg TDA_API_PASS='apiPassword' && docker run --rm -it --name tda -p 80:80 tda```
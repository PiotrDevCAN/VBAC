docker build -t vbac --no-cache  --progress=plain  . 2> build.log
docker run -dit -p 8082:8080  --name vbac -v C:/CETAapps/VBAC:/var/www/html --env-file C:/CETAapps/VBAC/dev_env.list vbac

docker build -t vbac . --no-cache 
docker run -dit -p 8082:8080  --name vbac -v C:/Users/RobDaniel/git/vBAC_bm:/var/www/html --env-file C:/Users/RobDaniel/git/vBAC_bm/dev_env.list vbac

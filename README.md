### Online Shop Project
1.- Clone the repository in a new directory of your choice ("directoryName").
```
git clone https://github.com/u83mm/e-commerce.git "directoryName"
```

2.- Navigate to the new directory.
```
cd directoryName
```
3.- Edit docker-compose.yaml file and change this line:
```
"../Ecommerce:/var/www"
```
by this one with the directory name that you have choiced in step 1:
```
"../directoryName:/var/www"
```
4.- Create "db_vol" and "log" directories inside "Application" directory and inside "log" directory create "apache", "db" and "php" directories.
```
cd Application
mkdir db_vol log log/apache log/dg log/php
```
5.- Build the project and stands up the containers
```
docker compose build
docker compose up -d
```
6.- Access to phpMyAdmin.
```
http://localhost:8080/
user: admin
passwd: admin
```
7.- Select "my_database" and go to "import" menu and search my_database.sql file in your "directoryName".

8.- Go to your localhost in the browser and you can do login.
```
http://localhost/
user: admin@admin.com
passwd: admin
```


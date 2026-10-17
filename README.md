Paso a paso:

1 clonar el repositorio
2 composer install
3 cp .env.example .env
php artisan key:generate

4 & "d:\8001_xamp\mysql\bin\mysql.exe" -u root -e "CREATE USER IF NOT EXISTS 'legalcloud'@'localhost' IDENTIFIED BY 'elige-una-clave-local'; GRANT ALL PRIVILEGES ON legalcloud.* TO 'legalcloud'@'localhost'; FLUSH PRIVILEGES;"
5 php artisan migrate --force

6 $env:NODE_OPTIONS="--use-system-ca"
npm ci
npm run build

7 php artisan serve

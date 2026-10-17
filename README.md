# LegalCloud — puesta en marcha local

Comandos para dejar el proyecto funcionando en Windows con PowerShell, desde el clonado. La aplicación Laravel está en `Legalcc2`. MySQL es el de XAMPP (`d:\8001_xamp`).

## Requisitos

- PHP 8.3 o superior, con `pdo_mysql`, `mbstring` y `fileinfo`
- Composer
- Node.js y npm
- MariaDB/MySQL de XAMPP en marcha (puerto 3306)

## 1. Clonar

```powershell
git clone https://github.com/ArontiDumpti/https---github.com-ArontiDumpti-LegalCloudv1.git
cd https---github.com-ArontiDumpti-LegalCloudv1\Legalcc2
```

Si el repositorio ya está en `D:\legalv4`:

```powershell
cd D:\legalv4\Legalcc2
```

## 2. Dependencias de PHP y archivo de entorno

```powershell
composer install
copy .env.example .env
php artisan key:generate
```

`.env` queda apuntando a la base `legalcloud`, usuario `legalcloud` y clave `elige-una-clave-local`.

## 3. Base de datos

Con el MySQL de XAMPP, crea la base y el usuario que usa `.env`:

```powershell
& "d:\8001_xamp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS legalcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'legalcloud'@'localhost' IDENTIFIED BY 'elige-una-clave-local'; GRANT ALL PRIVILEGES ON legalcloud.* TO 'legalcloud'@'localhost'; FLUSH PRIVILEGES;"
```

Aplica el esquema:

```powershell
php artisan migrate --force
```

## 4. Frontend

`npm ci` necesita el almacén de certificados del sistema. Luego se compilan CSS y JS (Laravel Mix). Sin este paso, `http://127.0.0.1:8000/` responde 500 porque falta `public/mix-manifest.json`.

```powershell
$env:NODE_OPTIONS="--use-system-ca"
npm ci
npm run build
```

## 5. Servidor

```powershell
php artisan serve
```

Abre http://127.0.0.1:8000/.

## 6. Primera cuenta

Solo si la base está vacía y todavía no hay organización ni administrador. La contraseña se pide en consola y debe tener al menos 12 caracteres.

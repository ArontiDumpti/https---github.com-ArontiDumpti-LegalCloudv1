# LegalCloud — puesta en marcha local

Guía para dejar la aplicación funcionando en una PC con Windows y PowerShell. El código Laravel está en la carpeta `Legalcc2`. Todos los comandos de esta guía, salvo el de MySQL, se ejecutan dentro de esa carpeta.

La aplicación queda en http://127.0.0.1:8000/.

## Requisitos

Instala esto antes de empezar y comprueba que cada comando responde en PowerShell:

| Herramienta | Comprobar | Versión |
| --- | --- | --- |
| PHP | `php -v` | 8.3 o superior |
| Composer | `composer -V` | 2.x |
| Node.js | `node -v` | 18 o superior |
| npm | `npm -v` | el que trae Node |
| MySQL o MariaDB | ver el paso 3 | MySQL 8 o MariaDB 10.4+, en el puerto 3306 |

PHP tiene que incluir las extensiones `pdo_mysql`, `mbstring` y `fileinfo`. En PowerShell:

```powershell
php -m | Select-String "pdo_mysql|mbstring|fileinfo"
```

Deben aparecer las tres. Si falta alguna, actívala en el `php.ini` que usa esa instalación de PHP y abre una terminal nueva.

MySQL puede ser el de [XAMPP](https://www.apachefriends.org/), Laragon, MariaDB instalado aparte o cualquier otro servidor local. Tiene que estar **en marcha** antes del paso 3. En XAMPP, abre el panel y pulsa Start en MySQL.

## 1. Clonar el repositorio

```powershell
git clone https://github.com/ArontiDumpti/https---github.com-ArontiDumpti-LegalCloudv1.git
cd https---github.com-ArontiDumpti-LegalCloudv1\Legalcc2
```

Si ya tienes la carpeta, entra directo en `Legalcc2`. El resto de la guía asume que la terminal está ahí.

## 2. Dependencias de PHP y archivo de entorno

```powershell
composer install
copy .env.example .env
php artisan key:generate
```

- `composer install` descarga Laravel y el resto de paquetes PHP en `vendor`.
- `copy` crea `.env` a partir de la plantilla. Si `.env` ya existe, no lo sobrescribas.
- `php artisan key:generate` escribe `APP_KEY` en `.env`. Hazlo una sola vez. Si regeneras esa clave después de haber guardado datos, la aplicación no podrá descifrarlos.

`.env` ya apunta a esta base local:

| Variable | Valor |
| --- | --- |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `legalcloud` |
| `DB_USERNAME` | `legalcloud` |
| `DB_PASSWORD` | `elige-una-clave-local` |

La contraseña del paso 3 tiene que ser exactamente la de `DB_PASSWORD`. Si la cambias, cámbiala en los dos sitios.

## 3. Base de datos

Crea la base `legalcloud` y el usuario que lee `.env`.

Si `mysql` está en el PATH:

```powershell
mysql -u root -e "CREATE DATABASE IF NOT EXISTS legalcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'legalcloud'@'localhost' IDENTIFIED BY 'elige-una-clave-local'; GRANT ALL PRIVILEGES ON legalcloud.* TO 'legalcloud'@'localhost'; FLUSH PRIVILEGES;"
```

Si root tiene contraseña, añade `-p` y escríbela cuando la pida:

```powershell
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS legalcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'legalcloud'@'localhost' IDENTIFIED BY 'elige-una-clave-local'; GRANT ALL PRIVILEGES ON legalcloud.* TO 'legalcloud'@'localhost'; FLUSH PRIVILEGES;"
```

Con XAMPP, `mysql` no suele estar en el PATH. Usa la ruta de tu instalación (cámbiala si XAMPP no está en `C:\xampp`):

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS legalcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'legalcloud'@'localhost' IDENTIFIED BY 'elige-una-clave-local'; GRANT ALL PRIVILEGES ON legalcloud.* TO 'legalcloud'@'localhost'; FLUSH PRIVILEGES;"
```

Aplica las tablas:

```powershell
php artisan migrate --force
```

La primera vez crea el esquema. Si vuelves a ejecutarlo y todo está al día, responde `Nothing to migrate`.

## 4. Frontend

Sin este paso, http://127.0.0.1:8000/ responde 500 porque falta `public/mix-manifest.json`.

En Windows, npm a veces no encuentra los certificados del sistema. Esta variable lo evita:

```powershell
$env:NODE_OPTIONS="--use-system-ca"
npm ci
npm run build
```

`npm ci` instala las dependencias de `package-lock.json`. `npm run build` compila CSS y JavaScript con Laravel Mix y genera `public/mix-manifest.json`, `public/css/app.css` y `public/js/app.js`.

## 5. Servidor

```powershell
php artisan serve
```

Deja esa terminal abierta. Cuando veas `Server running on [http://127.0.0.1:8000]`, abre esa dirección en el navegador. Para detenerlo, pulsa `Ctrl+C` en esa misma terminal.

Si el puerto 8000 ya está ocupado:

```powershell
php artisan serve --port=8001
```

Y en `.env` cambia `APP_URL` a `http://localhost:8001`.

## 6. Primera cuenta

Solo hace falta si todavía no hay organización ni administrador. La contraseña se pide en la consola, no se muestra y debe tener al menos 12 caracteres. Cambia el nombre, el correo y el nombre de la persona por los tuyos:

```powershell
php artisan legalcloud:bootstrap "Nombre del despacho" admin@example.com --name="Nombre del administrador"
```

Ejecútalo en otra terminal, con la misma carpeta `Legalcc2`, mientras `php artisan serve` sigue corriendo en la primera.

## Comprobar que arrancó

1. MySQL está en marcha.
2. `php artisan serve` muestra `Server running on [http://127.0.0.1:8000]`.
3. El navegador abre esa URL y no muestra un error 500.

## Si algo falla

**`SQLSTATE[HY000] [2002]` o “connection refused”.** MySQL no está encendido o no escucha en `127.0.0.1:3306`. Arranca el servicio y vuelve a ejecutar `php artisan migrate --force`.

**`SQLSTATE[HY000] [1045] Access denied`.** El usuario o la contraseña de `.env` no coinciden con los de MySQL. Revisa `DB_USERNAME` y `DB_PASSWORD`, y repite el `CREATE USER` / `GRANT` del paso 3 con la misma clave.

**Error 500 al abrir la web, y el log menciona `mix-manifest.json`.** Falta el paso 4. Ejecuta `npm ci` y `npm run build` dentro de `Legalcc2`.

**`php` no se reconoce.** PHP no está en el PATH. Instálalo, o abre la terminal que trae tu entorno (Herd, Laragon o XAMPP) y repite los comandos desde ahí.

**`npm ci` falla por certificados en Windows.** Vuelve a lanzarlo con `$env:NODE_OPTIONS="--use-system-ca"` en la misma ventana de PowerShell.

Más detalle de API, cifrado y búsqueda está en `Legalcc2/docs/`.

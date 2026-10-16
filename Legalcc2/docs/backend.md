# Backend y operación local

## Requisitos y MySQL

- PHP 8.3+, Composer y extensiones PDO MySQL, Fileinfo y Mbstring.
- MySQL 8 o MariaDB 10.4+. La migración se verificó con MariaDB 10.4.32 de XAMPP.
- El esquema usa UUID públicos, claves foráneas, índices por organización y borrado lógico para expedientes/documentos.
- La búsqueda vectorial usa PostgreSQL con la extensión `pgvector`; MySQL conserva usuarios, expedientes, documentos, chunks de texto, auditoría y jobs.

Para una instalación local XAMPP, crea una base vacía y un usuario dedicado desde phpMyAdmin o la consola:

```sql
CREATE DATABASE legalcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'legalcloud'@'localhost' IDENTIFIED BY 'elige-una-clave-local';
GRANT ALL PRIVILEGES ON legalcloud.* TO 'legalcloud'@'localhost';
```

Copia `.env.example` a `.env`, genera `APP_KEY` con `php artisan key:generate` y configura host, puerto, base, usuario y contraseña. Para XAMPP local que use `root` sin contraseña, puedes usar esas credenciales solo en desarrollo; no las reutilices en cloud. Ejecuta `php artisan migrate` para crear el esquema. Usa `LEGALCLOUD_API_MODE=api` para activar HTTP en el frontend y `LEGALCLOUD_API_TOKEN_TTL_DAYS` para configurar expiración de tokens.

Crea la primera organización y su administrador; la contraseña se solicita oculta y no queda en el historial:

```powershell
php artisan legalcloud:bootstrap "Navarro & Asociados" admin@example.com --name="Mariana García"
```

## Seguridad

`POST /api/v1/auth/login` emite un token aleatorio de 256 bits; solo guarda SHA-256 y el token se devuelve una vez. Tiene expiración configurable y se revoca al cerrar sesión. El login limita intentos por usuario/IP. Los roles son `administrator`, `records_manager`, `analyst` y `consultant`.

`auth:api`, `organization.active` y `role` aplican autenticación, tenant activo y permisos. Las consultas se filtran por la organización del usuario autenticado. Las respuestas usan Resources para no serializar secretos ni columnas internas.

## Cifrado de datos sensibles

Laravel cifra con AES-256-CBC autenticado los archivos originales antes de escribirlos en `storage/app/private`; las descargas solo se descifran tras autorización y llevan `Cache-Control: private, no-store`. OCR recibe un temporal con permisos `0600`, eliminado al finalizar. Texto OCR, chunks, mensajes, citas, títulos/descripciones, referencias, nombres de archivo, email/nombre, notificaciones, resultados de jobs y payloads/IPs de auditoría se cifran en MySQL; sesiones de Laravel también están cifradas. Los emails y referencias usan además HMAC-SHA256 con `APP_KEY` para login/unicidad sin dejar esos valores en claro. En PostgreSQL se cifran los fragmentos textuales asociados a vectores; los embeddings permanecen como vectores consultables y requieren permisos de rol/TLS/cifrado del volumen.

`APP_KEY` debe generarse una vez, guardarse en un gestor de secretos y respaldarse de forma segura. No lo regeneres: perderlo impide descifrar los datos existentes y cambia los HMAC de búsqueda. Para filas heredadas, primero revisa `php artisan legalcloud:encrypt-sensitive-data --dry-run`; cuando el inventario esté aprobado, ejecuta el comando sin `--dry-run`. El backfill es idempotente y no imprime datos. Respaldar la base y almacenamiento privado antes de rotar claves. Las búsquedas cifradas sobre títulos/nombres hacen un escaneo acotado a 5000 registros autorizados; para escalar debe añadirse un índice de búsqueda diseñado sin almacenar texto sensible en claro.

El índice pgvector guarda el embedding necesario para calcular similitud y referencias de tenant; el fragmento textual que lo acompaña se cifra. Restringe el rol PostgreSQL, usa TLS y cifrado de disco administrado. Las llamadas LLM envían solo fragmentos autorizados y usan `store=false`.

## API implementada

Todas las rutas están bajo `/api/v1`; `GET /api/v1/openapi.yaml` sirve el contrato.

- Auth: `POST /auth/login`, `POST /auth/logout`.
- Usuarios: listar, crear, consultar y actualizar en `/users`.
- Expedientes: listar, crear, consultar y actualizar en `/cases`.
- Categorías: listar y crear en `/categories`.
- Documentos: listar, cargar, consultar, editar, archivar, versiones y descarga temporal.
- OCR: solicitar procesamiento y consultar el estado de jobs.
- Búsqueda: `POST /search` busca metadatos y texto OCR, acotado al tenant.
- Asistente: recupera chunks autorizados de la versión vigente, persiste la respuesta y sus citas, y audita la consulta. Usa OpenAI Responses API con `gpt-4.1-mini` cuando se habilita; sin evidencia responde que no encontró fuentes y, si hay evidencia pero el proveedor está deshabilitado, devuelve `503`.
- Notificaciones, auditoría y dashboard: lecturas limitadas por usuario, organización y rol.

La búsqueda `keyword` usa MySQL. `semantic` responde `503` hasta habilitar pgvector y conectar embeddings. `hybrid` usa MySQL si el índice está deshabilitado y cambia a pgvector cuando está habilitado; todavía no fusiona scores léxicos y vectoriales.

## PostgreSQL y pgvector

La conexión `vector` está separada de la conexión MySQL principal. Instala PostgreSQL con `pgvector` y habilita `pdo_pgsql` y `pgsql` en el `php.ini` que usa XAMPP/CLI; reinicia Apache si también atenderá peticiones web. Crea una base y un usuario dedicados, configura `VECTOR_DB_HOST`, `VECTOR_DB_PORT`, `VECTOR_DB_DATABASE`, `VECTOR_DB_USERNAME` y `VECTOR_DB_PASSWORD`, y usa dimensiones compatibles con el modelo de embeddings seleccionado. El ejemplo inicial usa 1536 dimensiones.

```sql
CREATE DATABASE legalcloud_vectors;
CREATE USER legalcloud_vectors_user WITH PASSWORD 'elige-una-clave-local';
GRANT ALL PRIVILEGES ON DATABASE legalcloud_vectors TO legalcloud_vectors_user;
```

Con el rol autorizado para instalar la extensión, habilita el índice idempotente:

```powershell
php artisan legalcloud:vector-migrate
```

El comando crea `vector`, la tabla `document_vectors` y un índice HNSW con distancia coseno. Después configura `VECTOR_DB_ENABLED=true`, `VECTOR_DB_CONNECTION=vector`, `VECTOR_EMBEDDING_DIMENSIONS` y `VECTOR_EMBEDDING_MODEL`. El worker indexa chunks en PostgreSQL después del OCR cuando el almacén vectorial está habilitado. El proveedor de embeddings sigue sin configurarse; hasta integrarlo, la búsqueda semántica devuelve `503` intencionalmente.

La cuenta de aplicación solo necesita permisos sobre sus tablas en `legalcloud_vectors`; reserva la instalación de extensiones para el rol de administración de la base. El índice no almacena documentos como fuente de verdad: autorización, versión vigente y metadatos se vuelven a comprobar en MySQL al servir los resultados.

## Archivos y jobs

Se aceptan PDF, DOC/DOCX, PNG/JPG y TIFF de hasta 25 MiB. Los archivos cifrados quedan en `storage/app/private`, no en `public/`. Cada versión conserva nombre, MIME, tamaño y checksum SHA-256. Las descargas autenticadas se descifran bajo demanda, no generan URL pública ni se guardan en caché.

Desarrollo local con `QUEUE_CONNECTION=database`:

```powershell
php artisan queue:work --queue=document-processing,default
```

OCR se procesa fuera de HTTP. Sin proveedor, el job queda `failed` con `ocr_provider_unavailable`. Implementa `App\Domain\AI\Contracts\OcrProvider` y cambia su binding para conectar OCR real. `EmbeddingProvider` y `LlmProvider` aíslan sus proveedores.

## LLM y privacidad

El proveedor integrado es **OpenAI Responses API**, modelo **`gpt-4.1-mini`**, usando el cliente HTTP de Laravel. La integración está desactivada por defecto (`LEGALCLOUD_LLM_ENABLED=false`) y no contiene una API key. Para habilitarla, configura `OPENAI_API_KEY` en `.env`, confirma que tu organización autoriza el envío de fragmentos documentales a OpenAI y establece `LEGALCLOUD_LLM_ENABLED=true`. `OPENAI_MODEL` permite cambiar de modelo y `OPENAI_BASE_URL` configurar un endpoint compatible.

Solo se envían hasta cinco fragmentos relevantes, limitados a documentos de la organización, filtros de la consulta y versión actual; el prompt exige tratar el texto documental como dato no confiable y citar fuentes `[S#]`. Si no hay fuentes, no se invoca el LLM. Los errores HTTP/red se convierten en 503 sin revelar respuestas upstream ni credenciales. La prueba del cliente usa HTTP fakes; no se ha realizado una llamada real porque no hay clave configurada.

## Pruebas

La suite normal usa SQLite en memoria para mantenerse aislada:

```powershell
php artisan test
php artisan test --filter=ApiV1Test
```

Para probar MySQL, crea una base desechable separada y configura temporalmente `DB_CONNECTION=mysql`, host, puerto, base, usuario y contraseña en la sesión de PowerShell. `migrate:fresh` elimina todas las tablas de la base seleccionada: nunca lo ejecutes contra datos que quieras conservar.

## Deploy pendiente

`deploy/docker`, `deploy/terraform` y `deploy/kubernetes` reservan la ubicación, sin manifiestos de producción todavía. Antes de desplegar hay que seleccionar proveedor y servicios para MySQL administrado, storage privado compatible con URLs firmadas, colas, secretos, TLS, backups, observabilidad, OCR, embeddings y LLM.
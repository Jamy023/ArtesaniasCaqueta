# ⚙️ Guía de Instalación y Configuración

## 🎯 Requisitos del Sistema

### Requisitos Mínimos
- **PHP**: >= 8.2
- **MySQL**: >= 8.0
- **Node.js**: >= 18.x
- **NPM**: >= 8.0.0
- **Composer**: >= 2.0

### Extensiones PHP Requeridas
```bash
# Verificar extensiones PHP
php -m | grep -E "(mbstring|xml|pdo|pdo_mysql|tokenizer|json|bcmath|ctype|fileinfo|openssl|curl|gd|zip)"
```

Extensiones necesarias:
- `mbstring`, `xml`, `pdo`, `pdo_mysql`
- `tokenizer`, `json`, `bcmath`, `ctype`
- `fileinfo`, `openssl`, `curl`, `gd`, `zip`

---

## 🚀 Instalación Paso a Paso

### 1️⃣ Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/artesanias.git
cd Artesanias
```

### 2️⃣ Instalar Dependencias PHP
```bash
# Instalar dependencias con Composer
composer install

# En caso de errores, limpiar cache de Composer
composer clear-cache
composer install --no-scripts
composer dump-autoload
```

### 3️⃣ Instalar Dependencias Node.js
```bash
# Verificar versión de Node
node --version  # Debe ser >= 18.x

# Instalar dependencias
npm install

# En caso de errores con package-lock.json
rm -rf node_modules package-lock.json
npm install
```

### 4️⃣ Configurar Variables de Entorno
```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

**Editar archivo `.env`:**
```env
# Configuración básica
APP_NAME="Artesanías Caquetá"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=artesanias_store
DB_USERNAME=root
DB_PASSWORD=tu_password

# ePayco (Configurar después)
EPAYCO_CUSTOMER_ID=1556492
EPAYCO_PUBLIC_KEY=tu_public_key
EPAYCO_TEST_MODE=TRUE

# Configuración de archivos
FILESYSTEM_DISK=public
```

### 5️⃣ Configurar Base de Datos

#### Crear Base de Datos
```sql
-- Conectar a MySQL
mysql -u root -p

-- Crear base de datos
CREATE DATABASE artesanias_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verificar creación
SHOW DATABASES;

-- Salir
EXIT;
```

#### Ejecutar Migraciones y Seeders
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos de prueba)
php artisan db:seed

# O en un solo comando
php artisan migrate --seed
```

### 6️⃣ Configurar Storage
```bash
# Crear enlace simbólico
php artisan storage:link

# Verificar que se creó correctamente
ls -la public/storage  # Debe mostrar el enlace simbólico
```

### 7️⃣ Optimizar Imágenes Existentes
```bash
# Convertir imágenes existentes a WebP
php artisan optimize:images

# Verificar optimización
ls storage/app/public/products/  # Verificar imágenes .webp
```

---

## 🔧 Configuración Avanzada

### Configurar Permisos (Linux/Mac)
```bash
# Permisos para directorios de almacenamiento
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

### Configurar Servidor Web

#### Apache (.htaccess)
Crear archivo `public/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/artesanias/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## ⚡ Comandos de Desarrollo

### Desarrollo Local
```bash
# Método 1: Comando integrado (Recomendado)
composer run dev
# Ejecuta: servidor + queue + logs + vite simultáneamente

# Método 2: Comandos individuales
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Frontend (Vue/Vite)
npm run dev

# Terminal 3: Procesamiento de colas (opcional)
php artisan queue:work

# Terminal 4: Logs en tiempo real (opcional)
php artisan pail
```

### Build para Producción
```bash
# Compilar assets frontend
npm run build

# Optimizar autoload de Composer
composer install --optimize-autoloader --no-dev

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🐞 Solución de Problemas Comunes

### Error: "Key not found"
```bash
# Regenerar clave de aplicación
php artisan key:generate
```

### Error de Migraciones
```bash
# Revisar estado de migraciones
php artisan migrate:status

# Rollback y re-migrar
php artisan migrate:rollback
php artisan migrate
```

### Error de Permisos Storage
```bash
# Linux/Mac
sudo chown -R $USER:www-data storage
sudo chmod -R 775 storage

# Windows (ejecutar como administrador)
icacls storage /grant Everyone:F /T
```

### Error "Class not found"
```bash
# Regenerar autoload
composer dump-autoload
```

### Problemas con NPM
```bash
# Limpiar cache de NPM
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

### Error de Base de Datos
```bash
# Verificar conexión
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar configuración
php artisan config:show database
```

### Problemas con Imágenes
```bash
# Verificar enlace simbólico
ls -la public/storage

# Recrear enlace
php artisan storage:link --force

# Verificar permisos
ls -la storage/app/public/
```

---

## 🏗️ Configuración de Producción

### Variables de Entorno Producción
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=artesanias_store
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password_seguro

# ePayco Producción
EPAYCO_CUSTOMER_ID=tu_customer_id
EPAYCO_PUBLIC_KEY=tu_public_key_real
EPAYCO_TEST_MODE=FALSE
```

### Optimizaciones de Producción
```bash
# Cachear todo
php artisan optimize

# Comando completo de optimización
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Verificar optimizaciones
php artisan optimize:status
```

### Configuración de Tareas Programadas
```bash
# Agregar a crontab (Linux)
crontab -e

# Agregar línea:
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Verificación de Instalación

### Tests Básicos
```bash
# Ejecutar tests
php artisan test

# Test específico
php artisan test --filter=BasicTest
```

### Health Check
```bash
# Verificar API
curl http://localhost:8000/api/health-check

# Verificar frontend
curl http://localhost:8000
```

### Verificar Funcionalidades
1. ✅ **Frontend**: http://localhost:8000
2. ✅ **Admin**: http://localhost:8000/admin
3. ✅ **API**: http://localhost:8000/api/health-check
4. ✅ **Productos**: http://localhost:8000/api/products
5. ✅ **Imágenes**: Verificar carga de imágenes

---

## 📚 Datos de Prueba

### Usuario Administrador
```
Email: admin@artesanias.com
Password: password123
```

### Cliente de Prueba
```
Email: cliente@test.com
Password: password123
```

### Productos de Prueba
- Se crean automáticamente con `php artisan db:seed`
- Incluye categorías y productos con imágenes
- Datos realistas de artesanías colombianas

---

## 🆘 Soporte y Debugging

### Logs de Aplicación
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Usar Laravel Pail para mejor visualización
php artisan pail
```

### Debugging Mode
```env
# En .env para debugging
APP_DEBUG=true
LOG_LEVEL=debug
```

### Comandos Útiles
```bash
# Info del sistema
php artisan about

# Limpiar caches
php artisan optimize:clear

# Verificar rutas
php artisan route:list

# Estado de la aplicación
php artisan inspire
```

Con esta guía, los estudiantes podrán instalar y configurar el sistema completo sin problemas.
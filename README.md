# 🎨 Sistema de E-commerce para Artesanías

## 📋 Descripción del Proyecto

Sistema web completo de comercio electrónico desarrollado específicamente para la venta de artesanías colombianas. El proyecto combina un frontend moderno en Vue.js con un backend robusto en Laravel, ofreciendo una experiencia de usuario fluida tanto para clientes como para administradores.

### 🎯 Características Principales

- **🛒 Tienda Online Completa**: Catálogo de productos, carrito de compras, sistema de categorías
- **💳 Pasarela de Pagos**: Integración completa con ePayco para pagos seguros
- **👨‍💼 Panel de Administración**: Gestión completa de productos, categorías, clientes y pedidos
- **📱 Diseño Responsivo**: Interfaz adaptable a dispositivos móviles y escritorio
- **🖼️ Optimización de Imágenes**: Conversión automática a formato WebP para mejor rendimiento
- **🔐 Sistema de Autenticación**: Login seguro para clientes y administradores
- **📊 Dashboard Administrativo**: Estadísticas y métricas del negocio

## 🛠️ Stack Tecnológico

### Backend
- **Framework**: Laravel 12.0
- **Base de Datos**: MySQL
- **Autenticación**: Laravel Sanctum
- **Optimización de Imágenes**: Spatie Laravel Image Optimizer

### Frontend
- **Framework**: Vue.js 3.5.17
- **Enrutamiento**: Vue Router 4.5.1
- **Estado Global**: Pinia 3.0.3
- **UI Framework**: Quasar 2.18.2
- **Estilos**: CSS Personalizado + 
- **Notificaciones**: Vue Toastification
- **Carrusel**: Swiper.js
- **Iconos**: Lucide Vue

### Herramientas de Desarrollo
- **Bundler**: Vite 5.2.11
- **Package Manager**: NPM
- **HTTP Client**: Axios
- **CSS Processing**: PostCSS + Autoprefixer

## 📁 Estructura del Proyecto

```
Artesanias/
├── 🗂️ app/
│   ├── Http/Controllers/     # Controladores de la aplicación
│   │   ├── AdminController.php
│   │   ├── ClienteController.php
│   │   └── Api/             # Controladores de API
│   │       ├── CategoryController.php
│   │       ├── ProductController.php
│   │       └── OrderController.php
│   ├── Models/              # Modelos Eloquent
│   │   ├── Category.php
│   │   ├── Cliente.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── User.php
│   └── Services/            # Lógica de negocio
├── 🗃️ database/
│   ├── migrations/          # Migraciones de base de datos
│   └── seeders/            # Seeders con datos de prueba
├── 🎨 resources/
│   ├── js/
│   │   ├── components/      # Componentes Vue
│   │   │   ├── Admin/      # Componentes del panel admin
│   │   │   └── Auth/       # Componentes de autenticación
│   │   ├── views/          # Vistas principales
│   │   └── stores/         # Stores de Pinia
│   └── css/                # Estilos personalizados
├── 🌐 routes/
│   ├── web.php             # Rutas web
│   └── api.php             # Rutas API
└── 📦 storage/
    └── app/public/products/ # Imágenes de productos
```

## ⚙️ Instalación y Configuración

### Prerrequisitos
- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM >= 8.0.0
- MySQL >= 8.0

### 1️⃣ Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd Artesanias
```

### 2️⃣ Instalar Dependencias PHP
```bash
composer install
```

### 3️⃣ Instalar Dependencias Node.js
```bash
npm install
```

### 4️⃣ Configurar Variables de Entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 5️⃣ Configurar Base de Datos
```bash
# Crear base de datos MySQL
mysql -u root -p -e "CREATE DATABASE artesanias_store;"

# Ejecutar migraciones y seeders
php artisan migrate --seed
```

### 6️⃣ Crear Enlace Simbólico para Storage
```bash
php artisan storage:link
```

### 7️⃣ Optimizar Imágenes Existentes
```bash
php artisan optimize:images
```

## 🚀 Comandos de Desarrollo

### Desarrollo Completo (Recomendado)
```bash
# Ejecuta servidor, queue, logs y vite simultáneamente
composer run dev
```

### Comandos Individuales
```bash
# Servidor Laravel
php artisan serve

# Desarrollo Frontend
npm run dev

# Build para Producción
npm run build

# Tests
composer run test
```

## 🗄️ Base de Datos

### Tablas Principales

#### `categories`
- Gestión de categorías de productos
- Campos: id, name, description, image, created_at, updated_at

#### `products`
- Catálogo de productos con imágenes optimizadas
- Campos: id, name, description, price, image, category_id, is_active, created_at, updated_at

#### `clientes`
- Información de clientes registrados
- Campos: id, name, email, phone, address, city, is_active, created_at, updated_at

#### `orders`
- Gestión de pedidos y transacciones
- Campos: id, cliente_id, total, status, transaction_id, epayco_data, created_at, updated_at

#### `users`
- Usuarios administrativos
- Campos: id, name, email, password, is_active, created_at, updated_at

## 🔌 API Endpoints

### Productos
- `GET /api/products` - Lista de productos activos
- `GET /api/products/{id}` - Detalle de producto
- `POST /api/products` - Crear producto (Admin)
- `PUT /api/products/{id}` - Actualizar producto (Admin)
- `DELETE /api/products/{id}` - Eliminar producto (Admin)

### Categorías
- `GET /api/categories` - Lista de categorías
- `POST /api/categories` - Crear categoría (Admin)

### Clientes
- `POST /api/clientes` - Registro de cliente
- `POST /api/clientes/login` - Login de cliente

### Pedidos
- `POST /api/orders` - Crear pedido
- `GET /api/orders` - Lista de pedidos (Admin)

## 💳 Integración con ePayco

El sistema incluye integración completa con ePayco para procesar pagos:

- **Configuración**: Archivo `EPAYCO_CONFIG.md`
- **Proceso**: Creación de orden → Redirección a ePayco → Confirmación
- **Seguridad**: Validación de transacciones y estados

## 👨‍💼 Panel de Administración

Acceso completo a través de `/admin` con las siguientes funcionalidades:

- **Dashboard**: Métricas y estadísticas
- **Productos**: CRUD completo con manejo de imágenes
- **Categorías**: Gestión de clasificaciones
- **Clientes**: Administración de usuarios registrados
- **Pedidos**: Seguimiento de transacciones
- **Usuarios**: Gestión de administradores

## 🎨 Funcionalidades del Frontend

### Tienda Online
- Catálogo paginado con filtros por categoría
- Vista detallada de productos
- Carrito de compras persistente
- Proceso de checkout integrado

### Componentes Principales
- **Navbar**: Navegación responsive con carrito
- **CarruselProductos**: Showcase de productos destacados
- **CartSidebar**: Carrito lateral deslizante
- **Footer**: Información de contacto

## 📸 Optimización de Imágenes

- **Conversión WebP**: Todas las imágenes se convierten automáticamente
- **Backup**: Se mantienen copias originales en `products-backup/`
- **Performance**: Mejora significativa en tiempos de carga

## 🔒 Seguridad

- **Autenticación Sanctum**: Tokens seguros para API
- **Middleware**: Protección de rutas administrativas
- **Validación**: Validación robusta en formularios
- **CORS**: Configuración adecuada para requests cross-origin

## 🧪 Testing

```bash
# Ejecutar todos los tests
composer run test

# Tests específicos
php artisan test --filter=ProductTest
```

## 🚀 Despliegue

### Railway (Configurado)
El proyecto incluye `railway.json` para despliegue automático en Railway.

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
DB_PASSWORD=tu_password
```

## 👥 Créditos

Proyecto desarrollado como material educativo para estudiantes, implementando las mejores prácticas de desarrollo web moderno con Laravel y Vue.js.

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

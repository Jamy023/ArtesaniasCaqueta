# 📄 DOCUMENTACIÓN COMPLETA DEL SISTEMA
# Sistema de E-commerce para Artesanías Caquetá

---

**Proyecto:** Sistema de E-commerce para Artesanías  
**Tecnologías:** Laravel 12.0 + Vue.js 3.5 + MySQL  
**Versión:** 1.0  
**Fecha:** Agosto 2025  

---

## 📋 TABLA DE CONTENIDOS

1. **[INTRODUCCIÓN GENERAL](#introducción-general)**
2. **[ARQUITECTURA DEL SISTEMA](#arquitectura-del-sistema)**  
3. **[TECNOLOGÍAS IMPLEMENTADAS](#tecnologías-implementadas)**
4. **[BASE DE DATOS](#base-de-datos)**
5. **[API ENDPOINTS](#api-endpoints)**
6. **[FRONTEND - DOCUMENTACIÓN](#frontend---documentación)**
7. **[INSTALACIÓN Y CONFIGURACIÓN](#instalación-y-configuración)**
8. **[INTEGRACIÓN CON EPAYCO](#integración-con-epayco)**
9. **[MANUAL DE ADMINISTRADOR](#manual-de-administrador)**
10. **[CONCLUSIONES](#conclusiones)**

---

## 🎯 INTRODUCCIÓN GENERAL

### Descripción del Proyecto

El **Sistema de E-commerce para Artesanías** es una plataforma web completa desarrollada específicamente para la comercialización de productos artesanales colombianos. El sistema combina un frontend moderno en Vue.js con un backend robusto en Laravel, ofreciendo una experiencia de usuario fluida tanto para clientes como para administradores.

### Características Principales

- **🛒 Tienda Online Completa**: Catálogo de productos, carrito de compras, sistema de categorías
- **💳 Pasarela de Pagos**: Integración completa con ePayco para pagos seguros
- **👨‍💼 Panel de Administración**: Gestión completa de productos, categorías, clientes y pedidos
- **📱 Diseño Responsivo**: Interfaz adaptable a dispositivos móviles y escritorio
- **🖼️ Optimización de Imágenes**: Conversión automática a formato WebP para mejor rendimiento
- **🔐 Sistema de Autenticación**: Login seguro para clientes y administradores
- **📊 Dashboard Administrativo**: Estadísticas y métricas del negocio

### Objetivos del Proyecto

1. **Digitalizar** la venta de artesanías tradicionales
2. **Proporcionar** una plataforma fácil de usar para artesanos
3. **Integrar** métodos de pago seguros y confiables  
4. **Ofrecer** herramientas administrativas completas
5. **Garantizar** escalabilidad y mantenibilidad del código

---

## 🏗️ ARQUITECTURA DEL SISTEMA

### Patrón de Arquitectura

El sistema implementa una **arquitectura híbrida**:
- **Backend**: Patrón MVC con Laravel
- **Frontend**: Single Page Application (SPA) con Vue.js  
- **API**: RESTful API para comunicación
- **Base de Datos**: MySQL con modelo relacional

### Diagrama de Arquitectura

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Base de       │
│   (Vue.js)      │◄──►│   (Laravel)     │◄──►│   Datos         │
│                 │    │                 │    │   (MySQL)       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       
         │                       │                       
         ▼                       ▼                       
┌─────────────────┐    ┌─────────────────┐               
│   Quasar UI     │    │   Laravel       │               
│   CSS Custom    │    │   Sanctum       │               
│   Pinia Store   │    │   Eloquent ORM  │               
└─────────────────┘    └─────────────────┘               
                                │                        
                                ▼                        
                    ┌─────────────────┐                  
                    │   ePayco API    │                  
                    │   (Pagos)       │                  
                    └─────────────────┘                  
```

### Separación de Responsabilidades

#### Backend (Laravel)
```
app/
├── Http/Controllers/    # Controladores (Lógica de presentación)
│   ├── Api/            # API Controllers
│   └── Web/            # Web Controllers
├── Models/             # Modelos (Lógica de datos)
├── Services/           # Servicios (Lógica de negocio)
└── Middleware/         # Middlewares (Filtros de request)
```

#### Frontend (Vue.js)
```
resources/js/
├── components/         # Componentes reutilizables
├── views/             # Vistas/Páginas
├── stores/            # Estado global (Pinia)
├── composables/       # Lógica reutilizable
└── utils/             # Utilidades
```

### Flujo de Datos

#### 1. Flujo de Autenticación
```
Usuario → LoginForm → AuthStore → Laravel API → Sanctum → Token → Pinia Store
```

#### 2. Flujo de Productos
```
Catálogo → ProductStore → API/ProductController → Product Model → MySQL → JSON Response → Vue Component
```

#### 3. Flujo de Pedidos
```
Checkout → OrderStore → API/OrderController → ePayco → Order Model → Confirmation
```

---

## 🛠️ TECNOLOGÍAS IMPLEMENTADAS

### Stack Tecnológico Completo

#### Backend
- **Framework**: Laravel 12.0
- **Base de Datos**: MySQL  
- **Autenticación**: Laravel Sanctum
- **Optimización de Imágenes**: Spatie Laravel Image Optimizer

#### Frontend
- **Framework**: Vue.js 3.5.17
- **Enrutamiento**: Vue Router 4.5.1
- **Estado Global**: Pinia 3.0.3
- **UI Framework**: Quasar 2.18.2
- **Estilos**: CSS Personalizado + Tailwind CSS 4.1.11
- **Notificaciones**: Vue Toastification
- **Carrusel**: Swiper.js
- **Iconos**: Lucide Vue

#### Herramientas de Desarrollo
- **Bundler**: Vite 5.2.11
- **Package Manager**: NPM
- **HTTP Client**: Axios
- **CSS Processing**: PostCSS + Autoprefixer

### Justificación de Tecnologías

#### Laravel 12.0
- **Razón**: Framework PHP robusto y maduro
- **Beneficios**: 
  - ORM Eloquent potente
  - Sistema de migraciones
  - Middleware integrado
  - Sanctum para APIs

#### Vue.js 3.5.17
- **Razón**: Framework reactivo moderno
- **Beneficios**:
  - Composition API
  - Performance optimizado
  - Ecosistema rico
  - Curva de aprendizaje amigable

#### MySQL
- **Razón**: Base de datos relacional confiable
- **Beneficios**:
  - Transacciones ACID
  - Escalabilidad
  - Herramientas administrativas
  - Amplio soporte

#### Quasar Framework
- **Razón**: Componentes UI profesionales
- **Beneficios**:
  - Componentes listos para usar
  - Diseño Material Design
  - Responsive automático
  - Documentación excelente

---

## 🗄️ BASE DE DATOS

### Esquema General

El sistema utiliza **MySQL** como base de datos principal (`artesanias_store`), proporcionando robustez, escalabilidad y rendimiento óptimo para el e-commerce.

### Diagrama Entidad-Relación

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│   categories    │       │    products     │       │     orders      │
│                 │       │                 │       │                 │
│ id (PK)         │◄─────┐│ id (PK)         │       │ id (PK)         │
│ name            │      ││ name            │       │ cliente_id (FK) │
│ slug            │      └│ category_id (FK)│       │ order_number    │
│ description     │       │ slug            │       │ total_amount    │
│ image           │       │ description     │       │ status          │
│ is_active       │       │ price           │       │ items (JSON)    │
│ created_at      │       │ stock           │       │ customer_data   │
│ updated_at      │       │ sku             │       │ epayco_ref      │
└─────────────────┘       │ dimensions      │       │ created_at      │
                          │ main_image      │       │ updated_at      │
                          │ gallery (JSON)  │       └─────────────────┘
                          │ is_active       │               │
                          │ created_at      │               │
                          │ updated_at      │               │
                          └─────────────────┘               │
                                                            │
┌─────────────────┐       ┌─────────────────┐               │
│     users       │       │    clientes     │◄──────────────┘
│                 │       │                 │
│ id (PK)         │       │ id (PK)         │
│ name            │       │ nombre          │
│ email           │       │ apellido        │
│ password        │       │ tipo_documento  │
│ is_active       │       │ numero_documento│
│ created_at      │       │ email           │
│ updated_at      │       │ password        │
└─────────────────┘       │ telefono        │
                          │ fecha_nacimiento│
                          │ direccion       │
                          │ ciudad          │
                          │ departamento    │
                          │ is_active       │
                          │ created_at      │
                          │ updated_at      │
                          └─────────────────┘
```

### Tablas Principales

#### 🏷️ `categories` - Gestión de Categorías
| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre de la categoría | NOT NULL |
| `slug` | VARCHAR(255) | URL amigable | UNIQUE |
| `description` | TEXT | Descripción detallada | NULLABLE |
| `image` | VARCHAR(255) | Imagen representativa | NULLABLE |
| `is_active` | BOOLEAN | Estado activo/inactivo | DEFAULT TRUE |

#### 🎨 `products` - Catálogo de Productos
| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre del producto | NOT NULL |
| `slug` | VARCHAR(255) | URL amigable | UNIQUE |
| `description` | TEXT | Descripción detallada | NULLABLE |
| `price` | DECIMAL(10,2) | Precio en COP | NOT NULL |
| `stock` | INTEGER | Cantidad disponible | DEFAULT 0 |
| `sku` | VARCHAR(100) | Código único | UNIQUE |
| `main_image` | VARCHAR(255) | Imagen principal | NULLABLE |
| `gallery` | JSON | Galería de imágenes | NULLABLE |
| `category_id` | BIGINT | Categoría del producto | FOREIGN KEY |

#### 👥 `clientes` - Registro de Clientes
| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `nombre` | VARCHAR(255) | Nombre del cliente | NOT NULL |
| `apellido` | VARCHAR(255) | Apellido del cliente | NOT NULL |
| `email` | VARCHAR(255) | Correo electrónico | UNIQUE, NOT NULL |
| `tipo_documento` | ENUM | Tipo de documento | CC, CE, NIT, PP |
| `numero_documento` | VARCHAR(20) | Número de documento | UNIQUE |
| `telefono` | VARCHAR(20) | Número de teléfono | NULLABLE |

#### 📦 `orders` - Gestión de Pedidos
| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `cliente_id` | BIGINT | Cliente del pedido | FOREIGN KEY |
| `order_number` | VARCHAR(50) | Número único | UNIQUE |
| `total_amount` | DECIMAL(10,2) | Monto total | NOT NULL |
| `status` | ENUM | Estado del pedido | pending, paid, failed, cancelled |
| `items` | JSON | Productos del pedido | NOT NULL |
| `epayco_ref` | VARCHAR(255) | Referencia de ePayco | NULLABLE |

---

## 🔌 API ENDPOINTS

### Base URL
```
Local: http://localhost:8000/api
Producción: https://tu-dominio.com/api
```

### Autenticación
- **Sanctum Tokens**: Para clientes (usuarios finales)
- **Admin Middleware**: Para administradores  
- **Rutas Públicas**: Sin autenticación requerida

### Endpoints Principales

#### 📦 PRODUCTOS

##### Listar Productos
```http
GET /api/products
```
**Parámetros:**
- `admin_view`: Mostrar productos inactivos
- `search`: Búsqueda por nombre
- `category_id`: Filtrar por categoría
- `per_page`: Elementos por página (default: 12)

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Abanico Guacamaya",
      "slug": "abanico-guacamaya",
      "price": 25000,
      "main_image": "products/abanico-guacamaya.webp",
      "category": {
        "id": 1,
        "name": "Abanicos"
      }
    }
  ],
  "current_page": 1,
  "total": 50
}
```

##### Crear Producto
```http
POST /api/products
```
🔒 **Requiere**: Autenticación Admin

**Body:**
```json
{
  "name": "Nombre del producto",
  "description": "Descripción detallada",
  "price": 25000,
  "stock": 10,
  "category_id": 1,
  "is_active": true
}
```

#### 🏷️ CATEGORÍAS

##### Listar Categorías
```http
GET /api/categories
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "name": "Abanicos",
    "slug": "abanicos",
    "description": "Abanicos artesanales",
    "products_count": 15
  }
]
```

#### 👥 CLIENTES

##### Registro de Cliente
```http
POST /api/clientes/register
```

**Body:**
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "tipo_documento": "CC",
  "numero_documento": "12345678",
  "email": "juan@email.com",
  "password": "password123"
}
```

##### Login de Cliente
```http
POST /api/clientes/login
```

**Respuesta:**
```json
{
  "success": true,
  "token": "bearer_token_here",
  "user": {
    "id": 1,
    "nombre": "Juan",
    "email": "juan@email.com"
  }
}
```

#### 🛒 PEDIDOS

##### Crear Pedido
```http
POST /api/orders/create
```
🔒 **Requiere**: Token Sanctum

**Body:**
```json
{
  "items": [
    {
      "id": 1,
      "name": "Abanico Guacamaya",
      "price": 25000,
      "quantity": 2
    }
  ],
  "total_amount": 50000
}
```

**Respuesta con datos de ePayco:**
```json
{
  "success": true,
  "order_id": 123,
  "order_number": "ORD-20240827123456-001",
  "epayco_data": {
    "p_cust_id_cliente": "1556492",
    "p_key": "tu_public_key",
    "p_amount": "50000"
  }
}
```

---

## 🎨 FRONTEND - DOCUMENTACIÓN

### Arquitectura del Frontend

#### Stack Tecnológico
- **Framework**: Vue.js 3.5.17 (Composition API)
- **Enrutamiento**: Vue Router 4.5.1
- **Estado Global**: Pinia 3.0.3
- **UI Framework**: Quasar 2.18.2
- **Estilos**: CSS Personalizado + TailwindCSS 4.1.11
- **Build Tool**: Vite 5.2.11

### Estructura de Carpetas
```
resources/js/
├── 📁 components/           # Componentes reutilizables
│   ├── Admin/              # Panel administrativo
│   ├── Auth/               # Autenticación
│   ├── App.vue             # Componente raíz
│   ├── Navbar.vue          # Navegación
│   ├── Footer.vue          # Pie de página
│   └── CartSidebar.vue     # Carrito lateral
├── 📁 views/               # Vistas principales
│   ├── Home.vue           # Página de inicio
│   ├── Products.vue       # Catálogo
│   └── ProductDetail.vue  # Detalle producto
├── 📁 stores/             # Estado global (Pinia)
│   ├── authStore.js       # Autenticación
│   ├── cartStore.js       # Carrito
│   └── adminAuthStore.js  # Auth admin
└── 📁 css/               # Estilos personalizados
    ├── Home.css          # Página inicio
    ├── nav.css           # Navegación  
    └── Product.css       # Productos
```

### Componentes Principales

#### 🏠 App.vue - Componente Raíz
**Funcionalidad:**
- Estructura toda la aplicación
- Maneja visualización condicional de navbar y footer
- Distingue entre rutas públicas y administrativas

```vue
<template>
  <div id="app">
    <template v-if="!isAdminRoute && !$route.meta.hideNav">
      <Navbar />
    </template>
    
    <main :class="{ 'admin-main': isAdminRoute, 'public-main': !isAdminRoute }">
      <router-view />
    </main>
    
    <template v-if="!isAdminRoute">
      <Footer />
    </template>
  </div>
</template>
```

#### 🛒 CartSidebar.vue - Carrito Lateral
**Funcionalidades:**
- Panel lateral deslizante
- Operaciones CRUD sobre items
- Cálculo automático de totales
- Integración con checkout

#### 🧭 Navbar.vue - Navegación Principal  
**Características:**
- Navegación responsive
- Indicador de carrito con contador
- Modal de login integrado
- Menú de usuario autenticado

### Stores de Pinia (Estado Global)

#### 🔐 authStore.js - Gestión de Autenticación

**Estado:**
```javascript
state: () => ({
  cliente: null,           // Datos del cliente
  token: null,             // JWT token
  isAuthenticated: false,  // Estado auth
  loading: false,          // Loading
  errors: {}              // Errores
})
```

**Acciones Principales:**
- `login(credentials)`: Autenticar usuario
- `register(userData)`: Registrar nuevo usuario  
- `logout()`: Cerrar sesión
- `getProfile()`: Obtener perfil del usuario
- `initAuth()`: Inicializar auth al cargar app

#### 🛍️ cartStore.js - Gestión del Carrito

**Estado:**
```javascript  
state: () => ({
  items: [],              // Items del carrito
  isLoading: false       // Estado de carga
})
```

**Getters Computed:**
```javascript
itemCount: computed(() => {
  return items.value.reduce((total, item) => total + item.quantity, 0)
})

totalPrice: computed(() => {
  return items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
})
```

**Acciones del Carrito:**
- `addToCart(product, quantity)`: Agregar producto
- `removeFromCart(productId)`: Eliminar producto
- `updateQuantity(productId, newQuantity)`: Actualizar cantidad
- `clearCart()`: Limpiar carrito
- `getCheckoutData()`: Datos para checkout

### Sistema de Estilos

#### CSS Personalizado + Quasar UI

**Configuración:**
- **CSS Puro**: Estilos personalizados por componente (1100+ líneas)
- **Quasar Framework**: Componentes UI profesionales
- **TailwindCSS**: Configurado para uso específico (principalmente admin)
- **Gradientes y Efectos**: Uso extensivo de gradientes CSS

**Tema de Colores:**
```css
/* Colores principales del proyecto */
.navbar {
  background: linear-gradient(135deg, #388E3C 0%, #2E7D32 100%);
}

.hero-enhanced {
  background: linear-gradient(135deg, rgb(59, 63, 59) 0%, rgb(48, 63, 49) 80%);
}

/* Paleta de colores */
- Verde Principal: #2E7D32, #4CAF50
- Verde Claro: #E8F5E8, #F1F8E9  
- Marrón Texto: #5D4037, #8D6E63
- Transparencias: rgba(255,255,255,0.1-0.9)
```

### Vistas Principales

#### 🏡 Home.vue - Página de Inicio
**Secciones:**
1. **Hero Section**: Imagen de fondo con call-to-action
2. **Carrusel de Productos**: Productos destacados  
3. **Categorías Destacadas**: Grid de categorías
4. **Sobre Nosotros**: Información de empresa

#### 📦 Products.vue - Catálogo de Productos  
**Funcionalidades:**
- Grid responsive de productos
- Filtros por categoría y búsqueda
- Paginación automática
- Loading states y skeleton loaders

#### 💳 CheckoutPage.vue - Proceso de Pago
**Flujo del Checkout:**
1. **Validación del Carrito**: Verificar items y stock
2. **Información del Cliente**: Datos de facturación
3. **Resumen del Pedido**: Items y totales  
4. **Integración ePayco**: Redirección a pasarela

---

## ⚙️ INSTALACIÓN Y CONFIGURACIÓN

### Requisitos del Sistema

#### Requisitos Mínimos
- **PHP**: >= 8.2
- **MySQL**: >= 8.0
- **Node.js**: >= 18.x
- **NPM**: >= 8.0.0
- **Composer**: >= 2.0

### Instalación Paso a Paso

#### 1️⃣ Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/artesanias.git
cd Artesanias
```

#### 2️⃣ Instalar Dependencias PHP
```bash
composer install
```

#### 3️⃣ Instalar Dependencias Node.js
```bash
npm install
```

#### 4️⃣ Configurar Variables de Entorno
```bash
cp .env.example .env
php artisan key:generate
```

**Archivo `.env` configurado:**
```env
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

# ePayco
EPAYCO_CUSTOMER_ID=1556492
EPAYCO_PUBLIC_KEY=tu_public_key
EPAYCO_TEST_MODE=TRUE
```

#### 5️⃣ Configurar Base de Datos
```bash
# Crear base de datos MySQL
mysql -u root -p -e "CREATE DATABASE artesanias_store;"

# Ejecutar migraciones y seeders
php artisan migrate --seed
```

#### 6️⃣ Configurar Storage
```bash
php artisan storage:link
```

#### 7️⃣ Optimizar Imágenes
```bash
php artisan optimize:images
```

### Comandos de Desarrollo

#### Desarrollo Local (Recomendado)
```bash
composer run dev
# Ejecuta: servidor + queue + logs + vite simultáneamente
```

#### Comandos Individuales
```bash
# Servidor Laravel
php artisan serve

# Frontend (Vue/Vite)
npm run dev

# Build para Producción
npm run build
```

---

## 💳 INTEGRACIÓN CON EPAYCO

### Configuración de ePayco

El sistema utiliza **Checkout Onpage** moderno de ePayco para procesar pagos de manera segura.

#### Variables de Entorno Requeridas
```env
EPAYCO_PUBLIC_KEY=tu_p_key_real_sin_asteriscos
EPAYCO_CUSTOMER_ID=1556492
EPAYCO_TEST_MODE=TRUE
APP_URL=https://tu-dominio.com
```

### Flujo de Pagos

#### 1. Creación de Pedido
```javascript
// Frontend: Crear pedido
const response = await axios.post('/api/orders/create', {
  items: cartItems,
  total_amount: totalAmount
})

const { epayco_data, order_number } = response.data
```

#### 2. Integración con ePayco
```javascript
// Abrir modal de ePayco
ePayco.checkout.open({
  key: epayco_data.p_key,
  amount: epayco_data.p_amount,
  description: epayco_data.p_description,
  response: epayco_data.p_url_response,
  confirmation: epayco_data.p_url_confirmation
})
```

#### 3. Procesamiento de Respuesta
- **URL de Respuesta**: Donde regresa el usuario
- **URL de Confirmación**: Webhook para actualizar estado
- **Estados**: pending, paid, failed, cancelled

### Datos de Prueba
```
Tarjeta: 4575623182290326
CVV: 123
Fecha: 12/28
Nombre: Maria Perez
```

---

## 👨‍💼 MANUAL DE ADMINISTRADOR

### Acceso al Panel

#### Credenciales por Defecto
```
URL: http://localhost:8000/admin
Email: admin@artesanias.com
Password: password123
```

### Dashboard Principal

#### Métricas Mostradas
- **Total de Productos**: Productos en catálogo
- **Productos Activos**: Productos visibles
- **Total de Categorías**: Categorías disponibles  
- **Total de Pedidos**: Pedidos en sistema
- **Pedidos Pendientes**: Pendientes de pago
- **Pedidos Pagados**: Transacciones exitosas
- **Total de Clientes**: Usuarios registrados

### Gestión de Productos

#### Crear Nuevo Producto
1. Clic en **"+ Nuevo Producto"**
2. Completar formulario:
   - Nombre del Producto (Obligatorio)
   - Descripción Detallada (Obligatorio)
   - Precio en COP (Obligatorio)
   - Stock Inicial (Obligatorio)
   - Categoría (Seleccionar)
   - Imágenes (JPG, PNG, WEBP - máx 5MB)

#### Gestión de Imágenes
- **Conversión automática** a WebP
- **Optimización** de tamaño y calidad
- **Respaldo** de imágenes originales
- **Galería múltiple** por producto

### Gestión de Categorías

#### Crear Categoría
```
Nombre: [Obligatorio, único]
Descripción: [Opcional]  
Imagen: [URL opcional]
Estado: [Activo/Inactivo]
```

### Gestión de Pedidos

#### Estados de Pedido
- 🟡 **Pendiente** (pending): Esperando pago
- ✅ **Pagado** (paid): Pago confirmado
- ❌ **Fallido** (failed): Pago rechazado
- ⚫ **Cancelado** (cancelled): Pedido cancelado

#### Información de Pedido
- **Datos del Cliente**: Información completa
- **Productos**: Lista detallada con cantidades
- **Pago**: Estado y referencia ePayco
- **Total**: Monto y desglose

### Gestión de Clientes

#### Información del Cliente
- **Datos Personales**: Nombre, documento, email
- **Contacto**: Teléfono, dirección
- **Estadísticas**: Pedidos realizados, monto gastado
- **Estado**: Cuenta activa/inactiva

---

## 📊 CONCLUSIONES

### Logros del Proyecto

#### ✅ Funcionalidades Implementadas
1. **E-commerce Completo**: Catálogo, carrito, checkout
2. **Sistema de Pagos**: Integración exitosa con ePayco
3. **Panel Administrativo**: Gestión completa de datos  
4. **Autenticación Segura**: Sanctum para APIs
5. **Diseño Responsive**: Adaptable a todos los dispositivos
6. **Optimización**: Imágenes WebP, performance CSS
7. **Base de Datos Robusta**: Modelo relacional completo

#### 🎯 Objetivos Cumplidos
- **Digitalización**: Plataforma online funcional
- **Usabilidad**: Interfaz intuitiva y amigable
- **Seguridad**: Autenticación y pagos seguros
- **Escalabilidad**: Arquitectura preparada para crecimiento
- **Mantenibilidad**: Código bien estructurado y documentado

### Tecnologías Destacadas

#### Backend
- **Laravel 12.0**: Framework robusto y actualizado
- **MySQL**: Base de datos confiable y escalable
- **Sanctum**: Autenticación moderna para SPAs
- **Eloquent ORM**: Manejo elegante de datos

#### Frontend  
- **Vue.js 3**: Framework reactivo moderno
- **Composition API**: Código más mantenible
- **Pinia**: Estado global eficiente
- **Quasar**: Componentes UI profesionales
- **CSS Personalizado**: Diseño único y atractivo

### Características Técnicas Sobresalientes

#### 1. Arquitectura Sólida
- Separación clara de responsabilidades
- Patrón MVC implementado correctamente
- API RESTful bien estructurada
- SPA con navegación fluida

#### 2. Optimizaciones Implementadas
- **Imágenes WebP**: Conversión automática
- **Lazy Loading**: Carga eficiente de componentes  
- **Bundle Splitting**: Optimización de JavaScript
- **CSS Optimizado**: Estilos específicos y eficientes

#### 3. Experiencia de Usuario
- **Diseño Atractivo**: Visual profesional
- **Navegación Intuitiva**: UX cuidada
- **Responsive Design**: Funciona en todos los dispositivos
- **Performance**: Carga rápida y fluida

#### 4. Seguridad Implementada
- **Autenticación JWT**: Tokens seguros
- **Validación Robusta**: Frontend y backend
- **Middleware**: Protección de rutas
- **CORS**: Configuración apropiada

### Impacto del Proyecto

#### Para los Artesanos
- **Plataforma de Ventas**: Canal digital eficiente
- **Gestión Fácil**: Panel administrativo intuitivo
- **Alcance Global**: Ventas sin limitaciones geográficas

#### Para los Clientes  
- **Experiencia Moderna**: Interface atractiva y funcional
- **Compra Segura**: Pagos protegidos con ePayco
- **Información Completa**: Detalles de productos y categorías

#### Para el Desarrollo
- **Código Limpio**: Bien estructurado y documentado
- **Escalabilidad**: Preparado para crecimiento
- **Mantenibilidad**: Fácil de modificar y extender
- **Tecnologías Actuales**: Stack moderno y actualizado

### Recomendaciones Futuras

#### Mejoras Técnicas
1. **Implementar Testing**: Unit tests y integration tests
2. **Cache Avanzado**: Redis para mejor performance
3. **CDN**: Distribución global de assets
4. **Monitoreo**: Logs avanzados y métricas

#### Funcionalidades Adicionales
1. **Sistema de Reviews**: Comentarios y calificaciones
2. **Wishlist**: Lista de deseos para clientes
3. **Notificaciones Push**: Alertas en tiempo real
4. **Multi-vendor**: Múltiples vendedores

#### Optimizaciones
1. **PWA**: Progressive Web App
2. **SEO Avanzado**: Mejor posicionamiento
3. **Analytics**: Google Analytics integrado  
4. **A/B Testing**: Optimización de conversión

---

## 📋 ANEXOS

### A. Comandos Útiles

#### Laravel
```bash
# Limpiar cache
php artisan optimize:clear

# Ver rutas
php artisan route:list

# Migrar base de datos
php artisan migrate

# Seeders
php artisan db:seed

# Generar clave
php artisan key:generate

# Crear enlace storage
php artisan storage:link
```

#### Frontend
```bash
# Instalar dependencias
npm install

# Desarrollo
npm run dev

# Build producción
npm run build

# Limpiar cache
npm cache clean --force
```

### B. Estructura de Archivos Importante

```
Artesanias/
├── app/Http/Controllers/Api/     # Controladores API
├── app/Models/                   # Modelos Eloquent
├── database/migrations/          # Migraciones DB
├── database/seeders/            # Datos de prueba
├── resources/js/components/     # Componentes Vue
├── resources/js/views/          # Vistas Vue
├── resources/js/stores/         # Estado Pinia
├── resources/css/               # Estilos CSS
├── routes/api.php               # Rutas API
├── storage/app/public/products/ # Imágenes productos
└── docs/                        # Documentación
```

### C. Variables de Entorno Principales

```env
# Aplicación
APP_NAME="Artesanías Caquetá"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=artesanias_store
DB_USERNAME=root
DB_PASSWORD=

# ePayco
EPAYCO_CUSTOMER_ID=1556492
EPAYCO_PUBLIC_KEY=tu_public_key
EPAYCO_TEST_MODE=TRUE

# Storage
FILESYSTEM_DISK=public
```

---

**© 2025 - Sistema de E-commerce para Artesanías**  
**Desarrollado con Laravel 12.0 + Vue.js 3.5**  
**Documentación Técnica Completa**

---
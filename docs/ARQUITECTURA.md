# 🏗️ Arquitectura del Sistema

## 📊 Diagrama de Arquitectura

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Base de       │
│   (Vue.js)      │◄──►│   (Laravel)     │◄──►│   Datos         │
│                 │    │                 │    │   (SQLite)      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       
         │                       │                       
         ▼                       ▼                       
┌─────────────────┐    ┌─────────────────┐               
│   Quasar UI     │    │   Laravel       │               
│   TailwindCSS   │    │   Sanctum       │               
│   Pinia Store   │    │   Eloquent ORM  │               
└─────────────────┘    └─────────────────┘               
                                │                        
                                ▼                        
                    ┌─────────────────┐                  
                    │   ePayco API    │                  
                    │   (Pagos)       │                  
                    └─────────────────┘                  
```

## 🎯 Patrón de Arquitectura

### Arquitectura MVC + SPA

El sistema implementa una arquitectura híbrida:
- **Backend**: Patrón MVC con Laravel
- **Frontend**: Single Page Application (SPA) con Vue.js
- **API**: RESTful API para comunicación

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

## 🔄 Flujo de Datos

### 1. Flujo de Autenticación
```
Usuario → LoginForm → AuthStore → Laravel API → Sanctum → Token → Pinia Store
```

### 2. Flujo de Productos
```
Catálogo → ProductStore → API/ProductController → Product Model → SQLite → JSON Response → Vue Component
```

### 3. Flujo de Pedidos
```
Checkout → OrderStore → API/OrderController → ePayco → Order Model → Confirmation
```

## 🗃️ Patrón de Base de Datos

### Modelo Entidad-Relación Simplificado

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Categories  │    │  Products   │    │   Orders    │
│             │◄───┤             │    │             │
│ - id        │    │ - id        │    │ - id        │
│ - name      │    │ - name      │    │ - cliente_id│
│ - image     │    │ - price     │    │ - total     │
└─────────────┘    │ - category_id    │ - status    │
                   └─────────────┘    └─────────────┘
                                              │
                                              │
                                    ┌─────────────┐
                                    │  Clientes   │
                                    │             │
                                    │ - id        │
                                    │ - name      │
                                    │ - email     │
                                    └─────────────┘
```

## 🔧 Patrones de Diseño Implementados

### 1. Repository Pattern (Implícito con Eloquent)
```php
// Modelo actúa como Repository
class Product extends Model {
    public static function getActiveProducts() {
        return self::where('is_active', true)->get();
    }
}
```

### 2. Service Layer Pattern
```php
// En Services/ (si se implementa)
class OrderService {
    public function createOrder($data) {
        // Lógica de negocio para crear pedidos
    }
}
```

### 3. Observer Pattern (Vue Reactivity)
```javascript
// Pinia Store - Estado reactivo
export const useProductStore = defineStore('products', {
  state: () => ({
    products: []
  }),
  actions: {
    async fetchProducts() {
      // Observer automático en componentes
    }
  }
})
```

### 4. Middleware Pattern
```php
// Middleware para autenticación admin
class AdminAuth {
    public function handle($request, Closure $next) {
        // Lógica de autorización
    }
}
```

## 🚦 Middleware y Filtros

### Backend Middlewares
- **AdminAuth**: Protege rutas administrativas
- **Authenticate**: Valida tokens Sanctum
- **TrustProxies**: Configuración de proxies

### Frontend Guards
- **Router Guards**: Protección de rutas en Vue Router
- **Auth Composables**: Verificación de estado de autenticación

## 📡 Comunicación API

### Patrón Request/Response
```javascript
// Frontend (Axios)
const response = await axios.get('/api/products')
const products = response.data

// Backend (Laravel)
return response()->json($products)
```

### Manejo de Estados HTTP
- **200**: Éxito
- **201**: Creado
- **401**: No autorizado
- **422**: Error de validación
- **500**: Error del servidor

## 🔐 Seguridad por Capas

### Capa 1: Frontend
- Validación de formularios
- Sanitización de entrada
- Protección CSRF en meta tags

### Capa 2: API
- Middleware de autenticación
- Validación de requests
- Rate limiting

### Capa 3: Base de Datos
- Eloquent ORM (previene SQL injection)
- Migraciones controladas
- Seeders para datos de prueba

## 🎨 Arquitectura de Componentes Vue

### Jerarquía de Componentes
```
App.vue
├── Navbar.vue
├── Router View
│   ├── Home.vue
│   │   ├── CarruselProductos.vue
│   │   └── ProductCard.vue
│   ├── Products.vue
│   └── ProductDetail.vue
├── CartSidebar.vue
└── Footer.vue
```

### Comunicación Entre Componentes
- **Props**: Padre → Hijo
- **Emits**: Hijo → Padre
- **Pinia Store**: Estado global
- **Provide/Inject**: Para componentes profundos

## 📦 Gestión de Dependencias

### Backend (Composer)
- Laravel Framework
- Sanctum (API Authentication)
- Spatie Image Optimizer

### Frontend (NPM)
- Vue.js (Core Framework)
- Vue Router (Routing)
- Pinia (State Management)
- Quasar (UI Framework)
- Axios (HTTP Client)

## ⚡ Optimizaciones de Rendimiento

### Backend
- **Eloquent Relations**: Lazy loading optimizado
- **Query Optimization**: Selects específicos
- **Image Optimization**: WebP conversion

### Frontend
- **Code Splitting**: Lazy loading de rutas
- **Bundle Optimization**: Vite optimizations
- **Component Lazy Loading**: Dynamic imports

## 🔄 Estados de la Aplicación

### Estados Globales (Pinia)
- **authStore**: Usuario autenticado
- **cartStore**: Carrito de compras
- **adminAuthStore**: Sesión administrativa

### Estados Locales (Componentes)
- Loading states
- Form validation states
- UI interaction states

## 🌐 Configuración de Rutas

### Backend Routes (Laravel)
```php
// api.php
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);

// web.php  
Route::get('/{any}', function () {
    return view('app'); // SPA entry point
})->where('any', '.*');
```

### Frontend Routes (Vue Router)
```javascript
const routes = [
  { path: '/', component: Home },
  { path: '/products', component: Products },
  { path: '/admin', component: AdminLayout }
]
```

Esta arquitectura garantiza escalabilidad, mantenibilidad y separación clara de responsabilidades.
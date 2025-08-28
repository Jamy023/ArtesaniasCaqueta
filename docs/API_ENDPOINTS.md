# 🔌 Documentación de API - Endpoints

## 🌐 Base URL
```
Local: http://localhost:8000/api
Producción: https://tu-dominio.com/api
```

## 🔐 Autenticación

### Tipos de Autenticación
- **Sanctum Tokens**: Para clientes (usuarios finales)
- **Admin Middleware**: Para administradores
- **Rutas Públicas**: Sin autenticación requerida

### Headers Requeridos
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  # Solo para rutas protegidas
```

---

## 📦 PRODUCTOS

### 🔍 Listar Productos
```http
GET /api/products
```

**Parámetros de Query:**
- `admin_view` (boolean): Mostrar todos los productos (incluye inactivos)
- `search` (string): Búsqueda por nombre
- `category_id` (integer): Filtrar por categoría
- `is_active` (boolean): Filtrar por estado
- `sort_by` (string): Campo para ordenar (default: created_at)
- `sort_direction` (string): asc/desc (default: desc)
- `per_page` (integer): Elementos por página (default: 12)

**Respuesta Exitosa (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Abanico Guacamaya",
      "slug": "abanico-guacamaya",
      "description": "Hermoso abanico artesanal...",
      "price": 25000,
      "stock": 10,
      "sku": "ABN-001",
      "main_image": "products/abanico-guacamaya.webp",
      "gallery": ["image1.webp", "image2.webp"],
      "is_active": true,
      "category": {
        "id": 1,
        "name": "Abanicos",
        "slug": "abanicos"
      }
    }
  ],
  "current_page": 1,
  "total": 50
}
```

### 👁️ Ver Producto por Slug/ID
```http
GET /api/products/{slug}
```

**Parámetros:**
- `admin_view` (query): Incluir productos inactivos

**Respuesta Exitosa (200):**
```json
{
  "id": 1,
  "name": "Abanico Guacamaya",
  "slug": "abanico-guacamaya",
  "description": "Descripción detallada...",
  "price": 25000,
  "category": {
    "id": 1,
    "name": "Abanicos"
  }
}
```

### ➕ Crear Producto
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
  "sku": "PRD-001",
  "dimensions": {
    "width": 30,
    "height": 20,
    "depth": 2
  },
  "main_image": "products/imagen-principal.webp",
  "gallery": ["image1.webp", "image2.webp"],
  "category_id": 1,
  "is_active": true
}
```

### ✏️ Actualizar Producto
```http
PUT /api/products/{id}
PATCH /api/products/{id}
```
🔒 **Requiere**: Autenticación Admin

### 🗑️ Eliminar Producto
```http
DELETE /api/products/{id}
```
🔒 **Requiere**: Autenticación Admin

### 🔄 Cambiar Estado
```http
PATCH /api/products/{id}/toggle-active
```
🔒 **Requiere**: Autenticación Admin

### 📸 Subir Imagen
```http
POST /api/upload-product-image
```
🔒 **Requiere**: Autenticación Admin

**Body (multipart/form-data):**
- `image`: Archivo de imagen (jpeg,png,jpg,gif,webp, max: 5MB)

**Respuesta:**
```json
{
  "message": "Imagen subida y optimizada exitosamente",
  "path": "products/imagen.webp",
  "url": "/storage/products/imagen.webp"
}
```

---

## 🏷️ CATEGORÍAS

### 📋 Listar Categorías
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
    "image": "categories/abanicos.webp",
    "is_active": true,
    "products_count": 15
  }
]
```

### 👁️ Ver Categoría
```http
GET /api/categories/{id}/show
```

### ➕ Crear Categoría
```http
POST /api/categories
```
🔒 **Requiere**: Autenticación Admin

**Body:**
```json
{
  "name": "Nueva Categoría",
  "description": "Descripción de la categoría",
  "image": "https://ejemplo.com/imagen.jpg",
  "is_active": true
}
```

### 🛍️ Productos por Categoría
```http
GET /api/categories/{category_id}/products
```

**Parámetros:**
- `admin_view` (query): Incluir productos inactivos

---

## 👥 CLIENTES

### 🔐 Registro de Cliente
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
  "password": "password123",
  "password_confirmation": "password123",
  "telefono": "3001234567",
  "fecha_nacimiento": "1990-01-01",
  "direccion": "Calle 123 #45-67",
  "ciudad": "Florencia",
  "departamento": "Caquetá"
}
```

### 🚪 Login de Cliente
```http
POST /api/clientes/login
```

**Body:**
```json
{
  "email": "cliente@email.com",
  "password": "password123"
}
```

**Respuesta:**
```json
{
  "success": true,
  "token": "bearer_token_here",
  "user": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan@email.com"
  }
}
```

### 👤 Perfil del Cliente
```http
GET /api/clientes/profile
```
🔒 **Requiere**: Token Sanctum

### ✏️ Actualizar Perfil
```http
PUT /api/clientes/profile
```
🔒 **Requiere**: Token Sanctum

### 🚪 Logout
```http
POST /api/clientes/logout
```
🔒 **Requiere**: Token Sanctum

### 🔑 Cambiar Contraseña
```http
POST /api/clientes/change-password
```
🔒 **Requiere**: Token Sanctum

**Body:**
```json
{
  "current_password": "password_actual",
  "new_password": "nueva_password",
  "new_password_confirmation": "nueva_password"
}
```

---

## 🛒 PEDIDOS

### 📝 Crear Pedido
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

**Respuesta:**
```json
{
  "success": true,
  "order_id": 123,
  "order_number": "ORD-20240827123456-001",
  "epayco_data": {
    "p_cust_id_cliente": "1556492",
    "p_key": "tu_public_key",
    "p_id_invoice": "ORD-20240827123456-001",
    "p_amount": "50000",
    "p_signature": "signature_hash",
    "p_email": "cliente@email.com"
  }
}
```

### 📋 Mis Pedidos
```http
GET /api/orders
```
🔒 **Requiere**: Token Sanctum

### 👁️ Ver Pedido
```http
GET /api/orders/{id}
```
🔒 **Requiere**: Token Sanctum

### 💳 Respuesta de ePayco
```http
GET|POST /api/orders/epayco-response
```
🌐 **Público**: Callback de ePayco

### ✅ Confirmación de ePayco
```http
POST /api/orders/epayco-confirmation
```
🌐 **Público**: Webhook de ePayco

---

## 👨‍💼 ADMINISTRACIÓN

### 🔐 Login Admin
```http
POST /api/admin/login
```

**Body:**
```json
{
  "email": "admin@email.com",
  "password": "admin_password"
}
```

### 🔍 Verificar Autenticación
```http
GET /api/admin/check-auth
```

### 📊 Estadísticas del Dashboard
```http
GET /api/admin/dashboard-stats
```
🔒 **Requiere**: Admin Auth

**Respuesta:**
```json
{
  "total_products": 50,
  "active_products": 45,
  "total_categories": 8,
  "total_orders": 120,
  "pending_orders": 5,
  "paid_orders": 100,
  "total_customers": 80,
  "recent_orders": []
}
```

### 📦 Gestión de Pedidos Admin
```http
GET /api/admin/orders          # Listar todos los pedidos
GET /api/admin/orders/{id}     # Ver pedido específico
PATCH /api/admin/orders/{id}/status  # Actualizar estado
```

### 👥 Gestión de Clientes Admin
```http
GET /api/clientes              # Listar clientes
POST /api/clientes             # Crear cliente
PUT /api/clientes/{id}         # Actualizar cliente
PATCH /api/clientes/{id}/toggle-active  # Cambiar estado
PATCH /api/clientes/{id}/change-password  # Cambiar contraseña
```

### 👤 Gestión de Usuarios Admin
```http
GET /api/users                 # Listar usuarios admin
POST /api/users                # Crear usuario
PUT /api/users/{id}            # Actualizar usuario
PATCH /api/users/{id}/toggle-active  # Cambiar estado
DELETE /api/users/{id}         # Eliminar usuario
PUT /api/users/{id}/change-password  # Cambiar contraseña
```

---

## 🖼️ IMÁGENES

### 🌐 Servir Imágenes
```http
GET /api/image/{filename}
```
🌐 **Público**: Servir imágenes de productos

---

## 🩺 UTILIDADES

### ❤️ Health Check
```http
GET /api/health-check
```

**Respuesta:**
```json
{
  "status": "ok",
  "message": "API funcionando correctamente",
  "timestamp": "2024-08-27T10:30:00.000000Z",
  "laravel_version": "12.0"
}
```

### 👤 Usuario Autenticado
```http
GET /api/user
```
🔒 **Requiere**: Token Sanctum

---

## 📋 Códigos de Respuesta

| Código | Descripción |
|--------|-------------|
| `200` | Éxito |
| `201` | Creado exitosamente |
| `401` | No autorizado |
| `403` | Prohibido |
| `404` | No encontrado |
| `422` | Error de validación |
| `500` | Error del servidor |

## 🚨 Manejo de Errores

### Estructura de Error Estándar
```json
{
  "message": "Descripción del error",
  "errors": {
    "campo": ["Error específico del campo"]
  }
}
```

### Errores de Validación (422)
```json
{
  "message": "Error de validación",
  "errors": {
    "email": ["El campo email es obligatorio"],
    "password": ["La contraseña debe tener al menos 8 caracteres"]
  }
}
```

## 🔧 Consideraciones de Desarrollo

### Rate Limiting
- **Clientes**: 60 requests/minuto
- **Admin**: Sin límite específico
- **Públicas**: 100 requests/minuto

### Paginación
- Parámetro `per_page`: máximo 100 elementos
- Respuesta incluye: `current_page`, `total`, `per_page`, `last_page`

### Filtros y Ordenamiento
- Usar parámetros de query para filtros
- `sort_by` acepta campos válidos del modelo
- `sort_direction`: `asc` o `desc`

### Optimizaciones
- Imágenes se convierten automáticamente a WebP
- Productos incluyen relación con categoría
- Paginación automática en listados

Esta documentación cubre todos los endpoints disponibles en la API del sistema de artesanías.
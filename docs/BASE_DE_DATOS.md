# 🗄️ Documentación de Base de Datos

## 📊 Esquema General

El sistema utiliza **MySQL** como base de datos principal (`artesanias_store`), proporcionando robustez, escalabilidad y rendimiento óptimo para el e-commerce. La estructura está diseñada para un e-commerce de artesanías con las siguientes entidades principales:

## 📋 Tablas y Relaciones

### Diagrama Entidad-Relación

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│   categories    │       │    products     │       │     orders      │
│                 │       │                 │       │                 │
│ id (PK)         │◄─────┐│ id (PK)         │       │ id (PK)         │
│ name            │      ││ name            ││◄──┐│ │ cliente_id (FK) │
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

## 📑 Descripción Detallada de Tablas

### 🏷️ `categories`
Gestiona las categorías de productos artesanales.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre de la categoría | NOT NULL |
| `slug` | VARCHAR(255) | URL amigable | UNIQUE |
| `description` | TEXT | Descripción detallada | NULLABLE |
| `image` | VARCHAR(255) | Imagen representativa | NULLABLE |
| `is_active` | BOOLEAN | Estado activo/inactivo | DEFAULT TRUE |
| `created_at` | TIMESTAMP | Fecha de creación | AUTO |
| `updated_at` | TIMESTAMP | Fecha de modificación | AUTO |

**Relaciones:**
- `hasMany(Product::class)` - Una categoría tiene muchos productos

---

### 🎨 `products`
Catálogo principal de artesanías.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre del producto | NOT NULL |
| `slug` | VARCHAR(255) | URL amigable | UNIQUE |
| `description` | TEXT | Descripción detallada | NULLABLE |
| `price` | DECIMAL(10,2) | Precio en COP | NOT NULL |
| `stock` | INTEGER | Cantidad disponible | DEFAULT 0 |
| `sku` | VARCHAR(100) | Código único del producto | UNIQUE |
| `dimensions` | JSON | Dimensiones del producto | NULLABLE |
| `main_image` | VARCHAR(255) | Imagen principal | NULLABLE |
| `gallery` | JSON | Galería de imágenes | NULLABLE |
| `is_active` | BOOLEAN | Estado activo/inactivo | DEFAULT TRUE |
| `category_id` | BIGINT | Categoría del producto | FOREIGN KEY |
| `created_at` | TIMESTAMP | Fecha de creación | AUTO |
| `updated_at` | TIMESTAMP | Fecha de modificación | AUTO |

**Relaciones:**
- `belongsTo(Category::class)` - Un producto pertenece a una categoría

**Casts:**
```php
'dimensions' => 'array',
'gallery' => 'array',
'is_active' => 'boolean'
```

---

### 👥 `clientes`
Registro de clientes de la tienda.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `nombre` | VARCHAR(255) | Nombre del cliente | NOT NULL |
| `apellido` | VARCHAR(255) | Apellido del cliente | NOT NULL |
| `tipo_documento` | ENUM | Tipo de documento | CC, CE, NIT, PP |
| `numero_documento` | VARCHAR(20) | Número de documento | UNIQUE |
| `email` | VARCHAR(255) | Correo electrónico | UNIQUE, NOT NULL |
| `password` | VARCHAR(255) | Contraseña hasheada | NOT NULL |
| `telefono` | VARCHAR(20) | Número de teléfono | NULLABLE |
| `fecha_nacimiento` | DATE | Fecha de nacimiento | NULLABLE |
| `direccion` | TEXT | Dirección completa | NULLABLE |
| `ciudad` | VARCHAR(100) | Ciudad de residencia | NULLABLE |
| `departamento` | VARCHAR(100) | Departamento | NULLABLE |
| `is_active` | BOOLEAN | Estado activo/inactivo | DEFAULT TRUE |
| `email_verified_at` | TIMESTAMP | Verificación de email | NULLABLE |
| `remember_token` | VARCHAR(100) | Token de recordar sesión | NULLABLE |
| `created_at` | TIMESTAMP | Fecha de registro | AUTO |
| `updated_at` | TIMESTAMP | Fecha de modificación | AUTO |

**Relaciones:**
- `hasMany(Order::class)` - Un cliente puede tener muchos pedidos

**Métodos Personalizados:**
- `getFullNameAttribute()` - Retorna nombre completo
- `getFormattedDocumentAttribute()` - Retorna documento formateado

**Autenticación:**
- Implementa `HasApiTokens` para Sanctum
- Campo de identificación: `email`

---

### 📦 `orders`
Gestión de pedidos y transacciones.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `cliente_id` | BIGINT | Cliente del pedido | FOREIGN KEY |
| `order_number` | VARCHAR(50) | Número único de pedido | UNIQUE |
| `total_amount` | DECIMAL(10,2) | Monto total | NOT NULL |
| `status` | ENUM | Estado del pedido | pending, paid, failed, cancelled |
| `items` | JSON | Productos del pedido | NOT NULL |
| `customer_data` | JSON | Datos del cliente | NULLABLE |
| `epayco_ref` | VARCHAR(255) | Referencia de ePayco | NULLABLE |
| `epayco_transaction_id` | VARCHAR(255) | ID de transacción | NULLABLE |
| `payment_method` | VARCHAR(50) | Método de pago | NULLABLE |
| `notes` | TEXT | Notas adicionales | NULLABLE |
| `created_at` | TIMESTAMP | Fecha de creación | AUTO |
| `updated_at` | TIMESTAMP | Fecha de modificación | AUTO |

**Relaciones:**
- `belongsTo(Cliente::class)` - Un pedido pertenece a un cliente

**Constantes de Estado:**
```php
const STATUS_PENDING = 'pending';
const STATUS_PAID = 'paid';
const STATUS_FAILED = 'failed';
const STATUS_CANCELLED = 'cancelled';
```

**Métodos Personalizados:**
- `generateOrderNumber()` - Genera número único de pedido
- `getFormattedStatusAttribute()` - Estado en español
- `isPaid()` - Verifica si está pagado
- `isPending()` - Verifica si está pendiente

---

### 👨‍💼 `users`
Usuarios administrativos del sistema.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT | Clave primaria | AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre del usuario | NOT NULL |
| `email` | VARCHAR(255) | Correo electrónico | UNIQUE, NOT NULL |
| `email_verified_at` | TIMESTAMP | Verificación de email | NULLABLE |
| `password` | VARCHAR(255) | Contraseña hasheada | NOT NULL |
| `is_active` | BOOLEAN | Estado activo/inactivo | DEFAULT TRUE |
| `remember_token` | VARCHAR(100) | Token de recordar sesión | NULLABLE |
| `created_at` | TIMESTAMP | Fecha de creación | AUTO |
| `updated_at` | TIMESTAMP | Fecha de modificación | AUTO |

## 🔧 Migraciones Importantes

### Historial de Migraciones

1. **`create_users_table`** - Tabla de usuarios administrativos
2. **`create_cache_table`** - Sistema de caché
3. **`create_jobs_table`** - Sistema de colas
4. **`create_categories_table`** - Categorías de productos
5. **`create_products_table`** - Catálogo de productos
6. **`create_clientes_table`** - Registro de clientes
7. **`create_personal_access_tokens_table`** - Tokens Sanctum
8. **`add_is_active_to_users_table`** - Campo de estado para usuarios
9. **`create_orders_table`** - Sistema de pedidos
10. **`add_is_active_to_clientes_table`** - Campo de estado para clientes
11. **`update_product_images_to_webp`** - Optimización de imágenes

## 🌱 Seeders y Datos de Prueba

### Seeders Disponibles

1. **`AdminUserSeeder`** - Crea usuario administrador por defecto
2. **`CategorySeeder`** - Categorías iniciales de artesanías
3. **`ProductSeeder`** - Productos de ejemplo con imágenes
4. **`Additional10ProductsSeeder`** - Productos adicionales

### Ejecutar Seeders
```bash
# Todos los seeders
php artisan db:seed

# Seeder específico
php artisan db:seed --class=CategorySeeder
```

## 🔍 Consultas Comunes

### Productos Activos por Categoría
```sql
SELECT p.*, c.name as category_name 
FROM products p 
JOIN categories c ON p.category_id = c.id 
WHERE p.is_active = 1 AND c.is_active = 1;
```

### Pedidos con Cliente
```sql
SELECT o.*, c.nombre, c.apellido, c.email 
FROM orders o 
JOIN clientes c ON o.cliente_id = c.id 
ORDER BY o.created_at DESC;
```

### Productos Más Vendidos
```sql
SELECT p.name, COUNT(*) as order_count
FROM products p
JOIN orders o ON JSON_SEARCH(o.items, 'one', p.id) IS NOT NULL
WHERE o.status = 'paid'
GROUP BY p.id, p.name
ORDER BY order_count DESC;
```

## 🔐 Índices y Optimización

### Índices Automáticos
- Claves primarias (`id`)
- Claves foráneas (`category_id`, `cliente_id`)
- Campos únicos (`email`, `slug`, `sku`)

### Índices Recomendados para Producción
```sql
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created ON orders(created_at);
```

## 💾 Backup y Restauración

### Crear Backup
```bash
# Backup completo de MySQL
mysqldump -u root -p artesanias_store > backup_$(date +%Y%m%d).sql

# Backup de tabla específica
mysqldump -u root -p artesanias_store products > products_backup.sql

# Backup con estructura y datos
mysqldump -u root -p --routines --triggers artesanias_store > backup_completo.sql
```

### Restaurar Backup
```bash
# Restaurar desde backup
mysql -u root -p artesanias_store < backup_20240827.sql

# Crear base de datos y restaurar
mysql -u root -p -e "CREATE DATABASE artesanias_store;"
mysql -u root -p artesanias_store < backup_completo.sql
```

## 📈 Consideraciones de Escalabilidad

### Para Crecimiento del Negocio
- **Optimización MySQL**: Configuración avanzada de InnoDB
- **Índices Adicionales**: Optimización de consultas frecuentes
- **Particionamiento**: División de tablas grandes por fecha
- **Réplicas de Lectura**: Para consultas de productos
- **Master-Slave**: Separación de lecturas y escrituras

### Monitoreo Recomendado
- Tamaño de base de datos
- Consultas lentas
- Uso de índices
- Bloqueos de tabla

Este esquema proporciona una base sólida para un e-commerce de artesanías con capacidad de crecimiento.
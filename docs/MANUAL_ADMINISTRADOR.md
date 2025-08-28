# 👨‍💼 Manual de Usuario - Panel de Administración

## 🚀 Introducción

El panel de administración del sistema de artesanías es una interfaz completa que permite gestionar todos los aspectos del e-commerce. Este manual guiará a los administradores a través de todas las funcionalidades disponibles.

---

## 🔐 Acceso al Panel

### 1. URL de Acceso
```
http://localhost:8000/admin
```

### 2. Credenciales por Defecto
```
Email: admin@artesanias.com
Password: password123
```

### 3. Proceso de Login
1. Navegue a la URL del panel administrativo
2. Complete el formulario con email y contraseña
3. Haga clic en "Iniciar Sesión"
4. Será redirigido al dashboard principal

---

## 📊 Dashboard Principal

### Información Mostrada

#### Métricas Principales
- **Total de Productos**: Cantidad total de productos en el catálogo
- **Productos Activos**: Productos visibles para los clientes
- **Total de Categorías**: Número de categorías disponibles
- **Total de Pedidos**: Pedidos realizados en el sistema
- **Pedidos Pendientes**: Pedidos en estado pendiente
- **Pedidos Pagados**: Pedidos completados exitosamente
- **Total de Clientes**: Usuarios registrados
- **Clientes Activos**: Usuarios con cuentas activas

#### Gráficos y Estadísticas
- Gráfico de pedidos por mes
- Productos más vendidos
- Categorías más populares
- Ingresos mensuales

---

## 🛍️ Gestión de Productos

### Acceso
**Menú** → **Productos**

### Lista de Productos

#### Funcionalidades
1. **Ver todos los productos** con información resumida
2. **Filtrar por:**
   - Estado (Activo/Inactivo)
   - Categoría
   - Rango de precio
3. **Buscar** por nombre o SKU
4. **Ordenar** por fecha, precio, nombre
5. **Paginación** automática

#### Columnas Mostradas
- **Imagen**: Thumbnail del producto
- **Nombre**: Nombre completo del producto
- **Categoría**: Categoría asignada
- **Precio**: Precio en COP
- **Stock**: Cantidad disponible
- **Estado**: Activo/Inactivo
- **Acciones**: Botones de editar/eliminar

### Crear Nuevo Producto

#### Pasos:
1. Clic en **"+ Nuevo Producto"**
2. Completar formulario:

**Información Básica:**
```
- Nombre del Producto (Obligatorio)
- Descripción Detallada (Obligatorio)
- Precio en COP (Obligatorio)
- Stock Inicial (Obligatorio)
- SKU (Opcional - se genera automático)
```

**Clasificación:**
```
- Categoría (Seleccionar de lista)
- Estado (Activo/Inactivo)
```

**Dimensiones (Opcional):**
```json
{
  "width": 30,
  "height": 20,
  "depth": 5
}
```

**Imágenes:**
```
- Imagen Principal (Obligatorio)
- Galería de Imágenes (Hasta 5 imágenes)
- Formatos: JPG, PNG, WEBP
- Tamaño máximo: 5MB por imagen
```

3. **Guardar** - El sistema:
   - Genera slug automáticamente
   - Optimiza imágenes a WebP
   - Valida datos
   - Guarda en base de datos

### Editar Producto

#### Pasos:
1. En la lista, clic en **"Editar"** (icono lápiz)
2. Modificar campos necesarios
3. **"Actualizar Producto"**

**Notas importantes:**
- Al cambiar el nombre, se regenera el slug
- Las imágenes se reoptimiza automáticamente
- El historial de cambios se mantiene

### Gestión de Imágenes

#### Subir Nueva Imagen:
1. **"Subir Imagen"** en el formulario
2. Seleccionar archivo
3. El sistema automáticamente:
   - Convierte a formato WebP
   - Optimiza el tamaño
   - Genera nombre único
   - Crea respaldo de original

#### Galería de Productos:
- **Imagen Principal**: Se muestra en listados
- **Galería**: Imágenes adicionales para vista detallada
- **Drag & Drop**: Reordenar imágenes
- **Eliminar**: Remover imágenes individuales

### Activar/Desactivar Productos

#### Método Rápido:
- Toggle switch en la lista de productos
- Cambio inmediato sin recargar página

#### Efectos:
- **Activo**: Visible para clientes
- **Inactivo**: Solo visible para administradores

---

## 🏷️ Gestión de Categorías

### Acceso
**Menú** → **Categorías**

### Lista de Categorías

#### Información Mostrada:
- **Nombre de Categoría**
- **Descripción**
- **Imagen Representativa**
- **Cantidad de Productos**
- **Estado** (Activo/Inactivo)
- **Fecha de Creación**

### Crear Nueva Categoría

#### Formulario:
```
Nombre: [Texto obligatorio]
Slug: [Se genera automáticamente]
Descripción: [Texto opcional]
Imagen: [URL de imagen opcional]
Estado: [Activo/Inactivo]
```

#### Validaciones:
- Nombre único (no duplicados)
- Slug único generado automáticamente
- URL de imagen válida (si se proporciona)

### Editar Categoría

#### Proceso:
1. Clic en **"Editar"** en la categoría deseada
2. Modificar campos
3. **"Guardar Cambios"**

**Consideraciones:**
- Al cambiar nombre, se regenera slug
- Si tiene productos asociados, estos mantienen la relación

### Eliminar Categoría

#### Restricciones:
- **No se puede eliminar** si tiene productos asociados
- Debe reasignar productos a otra categoría primero

#### Proceso Seguro:
1. Verificar productos asociados
2. Reasignar productos a otra categoría
3. Proceder con eliminación

---

## 👥 Gestión de Clientes

### Acceso
**Menú** → **Clientes**

### Lista de Clientes

#### Columnas:
- **Nombre Completo**
- **Email**
- **Teléfono**
- **Tipo/Número Documento**
- **Ciudad**
- **Estado** (Activo/Inactivo)
- **Fecha de Registro**
- **Último Login**

#### Filtros Disponibles:
- Por estado (Activo/Inactivo)
- Por ciudad
- Por tipo de documento
- Por fecha de registro

### Información Detallada del Cliente

#### Datos Personales:
```
- Nombre y Apellido
- Tipo de Documento (CC, CE, NIT, PP)
- Número de Documento
- Email (único en sistema)
- Teléfono
- Fecha de Nacimiento
```

#### Datos de Ubicación:
```
- Dirección Completa
- Ciudad
- Departamento
```

#### Estadísticas del Cliente:
```
- Total de Pedidos
- Monto Total Gastado
- Pedido Más Reciente
- Estado de la Cuenta
```

### Acciones sobre Clientes

#### Activar/Desactivar Cuenta:
- **Activa**: Cliente puede hacer login y compras
- **Inactiva**: Cliente no puede acceder al sistema

#### Cambiar Contraseña:
1. Seleccionar cliente
2. Clic en **"Cambiar Contraseña"**
3. Generar nueva contraseña
4. Cliente recibe notificación por email

#### Editar Información:
- Modificar datos personales
- Actualizar información de contacto
- Cambiar estado de la cuenta

---

## 📦 Gestión de Pedidos

### Acceso
**Menú** → **Pedidos**

### Lista de Pedidos

#### Información Mostrada:
- **Número de Pedido** (ORD-YYYYMMDDHHMMSS-XXX)
- **Cliente** (Nombre y email)
- **Fecha del Pedido**
- **Total del Pedido**
- **Estado del Pago**
- **Método de Pago**
- **Referencia ePayco**

#### Estados de Pedido:
```
🟡 Pendiente (pending) - Esperando pago
✅ Pagado (paid) - Pago confirmado
❌ Fallido (failed) - Pago rechazado
⚫ Cancelado (cancelled) - Pedido cancelado
```

### Detalle del Pedido

#### Información del Cliente:
```
- Datos personales completos
- Información de contacto
- Dirección de entrega
- Documento de identificación
```

#### Productos del Pedido:
```
- Lista detallada de productos
- Cantidad de cada producto
- Precio unitario
- Subtotal por producto
- Total general
```

#### Información de Pago:
```
- Método de pago utilizado
- Referencia de ePayco
- ID de transacción
- Fecha de procesamiento
- Estado del pago
```

### Gestión de Estados

#### Cambiar Estado de Pedido:
1. Acceder al detalle del pedido
2. Seleccionar nuevo estado
3. **"Actualizar Estado"**
4. Cliente recibe notificación automática

#### Estados y Acciones:
- **Pendiente → Pagado**: Confirmar pago manual
- **Pendiente → Cancelado**: Cancelar pedido
- **Pagado → Cancelado**: Proceso de devolución

### Reportes de Pedidos

#### Filtros Disponibles:
- Por rango de fechas
- Por estado de pago
- Por cliente específico
- Por monto mínimo/máximo

#### Exportar Datos:
- Descargar en formato Excel
- Generar PDF de reportes
- Exportar para contabilidad

---

## 👤 Gestión de Usuarios Administrativos

### Acceso
**Menú** → **Usuarios**

### Lista de Administradores

#### Información:
- **Nombre del Usuario**
- **Email de Acceso**
- **Rol/Permisos**
- **Estado** (Activo/Inactivo)
- **Último Acceso**
- **Fecha de Creación**

### Crear Nuevo Administrador

#### Formulario:
```
Nombre Completo: [Obligatorio]
Email: [Único, obligatorio]
Contraseña: [Mínimo 8 caracteres]
Confirmar Contraseña: [Debe coincidir]
Estado: [Activo/Inactivo]
```

#### Validaciones:
- Email único en el sistema
- Contraseña segura (mínimo 8 caracteres)
- Confirmación de contraseña

### Gestión de Permisos

#### Roles Disponibles:
- **Super Admin**: Acceso completo al sistema
- **Gestor de Productos**: Solo productos y categorías
- **Gestor de Pedidos**: Solo pedidos y clientes
- **Solo Lectura**: Ver información sin modificar

### Cambiar Contraseña

#### Para Otro Usuario:
1. Seleccionar usuario
2. **"Cambiar Contraseña"**
3. Generar nueva contraseña segura
4. Usuario recibe notificación

#### Para Cuenta Propia:
1. **"Mi Perfil"** en menú superior
2. **"Cambiar Contraseña"**
3. Proporcionar contraseña actual
4. Establecer nueva contraseña

---

## 📈 Reportes y Estadísticas

### Dashboard de Métricas

#### Métricas en Tiempo Real:
- Ventas del día actual
- Pedidos pendientes de procesar
- Productos con stock bajo
- Nuevos clientes registrados

#### Gráficos Disponibles:
1. **Ventas Mensuales**: Ingresos por mes
2. **Productos Más Vendidos**: Top 10 productos
3. **Categorías Populares**: Ventas por categoría
4. **Crecimiento de Clientes**: Registros por mes

### Reportes Personalizados

#### Generar Reporte de Ventas:
1. **"Reportes"** → **"Ventas"**
2. Seleccionar rango de fechas
3. Filtrar por categorías (opcional)
4. **"Generar Reporte"**
5. Descargar en Excel o PDF

#### Reporte de Inventario:
- Productos por categoría
- Niveles de stock
- Productos sin stock
- Valorización del inventario

---

## 🔧 Configuración del Sistema

### Configuración General

#### Información de la Empresa:
```
- Nombre de la Empresa
- Dirección
- Teléfono de Contacto
- Email de Contacto
- Redes Sociales
```

#### Configuración de Productos:
```
- SKU automático (activar/desactivar)
- Formato de números de pedido
- Stock mínimo de alerta
- Moneda por defecto (COP)
```

### Configuración de ePayco

#### Variables Requeridas:
```
Customer ID: 1556492
Public Key: [Clave pública de ePayco]
Modo de Prueba: Activado/Desactivado
URLs de Confirmación: [URLs del sistema]
```

#### Estados de Configuración:
- ✅ **Configurado**: ePayco funcionando correctamente
- ⚠️ **Pendiente**: Faltan configuraciones
- ❌ **Error**: Problemas en la configuración

---

## 🚨 Resolución de Problemas

### Problemas Comunes

#### 1. Error al Subir Imágenes
**Síntomas:** Mensaje de error al subir imágenes de productos

**Soluciones:**
- Verificar que el archivo sea menor a 5MB
- Usar formatos JPG, PNG, GIF o WEBP
- Verificar permisos de storage
- Ejecutar: `php artisan storage:link`

#### 2. Pedidos No Actualizan Estado
**Síntomas:** Estados de ePayco no se reflejan

**Soluciones:**
- Verificar configuración de webhook de ePayco
- Revisar logs en `storage/logs/laravel.log`
- Confirmar URLs de confirmación
- Verificar conectividad con ePayco

#### 3. Dashboard Carga Lento
**Síntomas:** Métricas tardan en cargar

**Soluciones:**
- Verificar conexión a base de datos
- Ejecutar: `php artisan optimize:clear`
- Revisar consultas SQL en logs
- Optimizar índices de base de datos

### Mantenimiento Regular

#### Tareas Semanales:
1. **Revisar logs de errores**
2. **Verificar respaldos de base de datos**
3. **Actualizar productos sin stock**
4. **Procesar pedidos pendientes**

#### Tareas Mensuales:
1. **Generar reporte de ventas**
2. **Optimizar base de datos**
3. **Revisar métricas de rendimiento**
4. **Actualizar información de productos**

---

## 💡 Consejos y Mejores Prácticas

### Gestión de Productos

#### Recomendaciones:
1. **Usar imágenes de alta calidad** (mínimo 800x800px)
2. **Escribir descripciones detalladas** con palabras clave
3. **Mantener precios actualizados** regularmente
4. **Gestionar stock** proactivamente
5. **Usar SKUs descriptivos** para mejor organización

#### SEO y Visibilidad:
1. **Nombres descriptivos** con palabras clave
2. **Categorización correcta** de productos
3. **Imágenes optimizadas** (WebP automático)
4. **Descriptions únicas** para cada producto

### Gestión de Pedidos

#### Flujo Recomendado:
1. **Revisar pedidos pendientes** diariamente
2. **Confirmar pagos** en caso de dudas
3. **Comunicar con clientes** sobre estados
4. **Mantener inventario** actualizado
5. **Procesar devoluciones** rápidamente

### Atención al Cliente

#### Mejores Prácticas:
1. **Responder consultas** en máximo 24 horas
2. **Mantener información actualizada** de productos
3. **Procesar pedidos** rápidamente
4. **Comunicar cambios** proactivamente
5. **Resolver problemas** de manera eficiente

---

## 📞 Soporte Técnico

### Información de Contacto
- **Desarrollador**: [Información de contacto]
- **Email de Soporte**: soporte@artesanias.com
- **Teléfono**: +57 300 123 4567

### Recursos Adicionales
- **Manual Técnico**: Documentación completa del sistema
- **API Documentation**: Referencia de endpoints
- **Base de Conocimiento**: Preguntas frecuentes

---

## 📋 Checklist de Tareas Diarias

### Al Iniciar el Día:
- [ ] Revisar dashboard de métricas
- [ ] Verificar pedidos pendientes
- [ ] Revisar productos con stock bajo
- [ ] Comprobar notificaciones del sistema

### Durante el Día:
- [ ] Procesar nuevos pedidos
- [ ] Responder consultas de clientes
- [ ] Actualizar información de productos
- [ ] Revisar reportes de errores

### Al Finalizar:
- [ ] Generar reporte diario
- [ ] Verificar respaldos
- [ ] Revisar logs de errores
- [ ] Planificar tareas del día siguiente

Este manual proporciona una guía completa para administrar eficientemente el sistema de e-commerce de artesanías.
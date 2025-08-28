# 🎨 Documentación del Frontend - Vue.js

## 🌟 Arquitectura del Frontend

### Stack Tecnológico
- **Framework**: Vue.js 3.5.17 (Composition API)
- **Enrutamiento**: Vue Router 4.5.1
- **Estado Global**: Pinia 3.0.3
- **UI Framework**: Quasar 2.18.2
- **Estilos**: CSS Personalizado 
- **Build Tool**: Vite 5.2.11
- **HTTP Client**: Axios
- **Notificaciones**: Vue Toastification

### Estructura de Carpetas
```
resources/js/
├── 📁 components/           # Componentes reutilizables
│   ├── Admin/              # Componentes del panel administrativo
│   │   ├── AdminDashboard.vue
│   │   ├── AdminLayout.vue
│   │   ├── AdminLogin.vue
│   │   ├── AdminProductos.vue
│   │   ├── AdminCategorias.vue
│   │   ├── AdminClientes.vue
│   │   ├── AdminPedidos.vue
│   │   └── AdminUsuarios.vue
│   ├── Auth/               # Componentes de autenticación
│   │   ├── LoginModal.vue
│   │   └── RegisterView.vue
│   ├── App.vue             # Componente raíz
│   ├── Navbar.vue          # Barra de navegación
│   ├── Footer.vue          # Pie de página
│   ├── CarruselProductos.vue # Carrusel de productos
│   ├── CartSidebar.vue     # Carrito lateral
│   └── CheckoutComponent.vue # Proceso de pago
├── 📁 views/               # Vistas/Páginas principales
│   ├── Home.vue           # Página de inicio
│   ├── Products.vue       # Catálogo de productos
│   ├── ProductDetail.vue  # Detalle de producto
│   ├── Categories.vue     # Vista de categorías
│   ├── CheckoutPage.vue   # Página de checkout
│   ├── AccountPage.vue    # Cuenta del usuario
│   ├── OrderConfirmation.vue # Confirmación de pedido
│   └── NotFound.vue       # Página 404
├── 📁 stores/             # Stores de Pinia (estado global)
│   ├── authStore.js       # Autenticación de usuarios
│   ├── adminAuthStore.js  # Autenticación de admin
│   └── cartStore.js       # Carrito de compras
├── 📁 composables/        # Lógica reutilizable
├── 📁 utils/              # Utilidades
│   └── imageUtils.js      # Utilidades de imágenes
├── 📁 css/                # Estilos personalizados
│   ├── Home.css
│   ├── Product.css
│   ├── Acount.css
│   └── nav.css
└── 📁 plugins/            # Configuración de plugins
    └── auth.js
```

---

## 🧩 Componentes Principales

### 🏠 App.vue - Componente Raíz

**Funcionalidad:**
- Componente principal que estructura toda la aplicación
- Maneja la visualización condicional de navbar y footer
- Distingue entre rutas públicas y administrativas

**Características Clave:**
```vue
<template>
  <div id="app">
    <!-- Navbar y Footer solo en rutas públicas -->
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

**Lógica Importante:**
- **Detección de rutas admin**: Verifica si la ruta actual comienza con `/admin`
- **Inicialización de auth**: Se ejecuta al montar la aplicación
- **Estilos condicionales**: Aplica diferentes clases según el tipo de ruta

---

### 🧭 Navbar.vue - Navegación Principal

**Funcionalidades:**
- Navegación responsive con menú hamburguesa en móviles
- Indicador del carrito de compras con contador
- Modal de login integrado
- Menú de usuario autenticado

**Elementos Clave:**
- **Logo y navegación**: Enlaces a Home, Productos, Categorías
- **Carrito**: Muestra cantidad de items y precio total
- **Autenticación**: Login modal o menú de usuario
- **Responsive**: Se adapta a dispositivos móviles

---

### 🛒 CartSidebar.vue - Carrito Lateral

**Funcionalidades:**
- Panel lateral deslizante con los productos del carrito
- Operaciones CRUD sobre items del carrito
- Cálculo automático de totales
- Integración con el proceso de checkout

**Operaciones:**
```javascript
// Aumentar/disminuir cantidad
increaseQuantity(productId)
decreaseQuantity(productId)

// Eliminar producto
removeFromCart(productId)

// Proceder al checkout
proceedToCheckout()
```

---

### 🎠 CarruselProductos.vue - Showcase de Productos

**Funcionalidades:**
- Carrusel automático de productos destacados
- Integración con Swiper.js
- Cards de productos con información básica
- Enlaces directos a detalles del producto

---

## 🗄️ Stores de Pinia (Estado Global)

### 🔐 authStore.js - Gestión de Autenticación

**Estado:**
```javascript
state: () => ({
  cliente: null,           // Datos del cliente autenticado
  token: null,             // JWT token
  isAuthenticated: false,  // Estado de autenticación
  loading: false,          // Indicador de carga
  errors: {},             // Errores de validación
  initialized: false      // Inicialización completada
})
```

**Getters:**
- `isLoggedIn`: Verifica si el usuario está autenticado
- `currentUser`: Retorna los datos del usuario actual
- `hasErrors`: Indica si existen errores

**Acciones Principales:**
- `login(credentials)`: Autenticar usuario
- `register(userData)`: Registrar nuevo usuario
- `logout()`: Cerrar sesión
- `getProfile()`: Obtener perfil del usuario
- `initAuth()`: Inicializar autenticación al cargar la app

**Persistencia:**
- El token se guarda en `localStorage`
- Configuración automática de headers de Axios
- Validación de expiración de tokens

---

### 🛍️ cartStore.js - Gestión del Carrito

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

isEmpty: computed(() => items.value.length === 0)
```

**Acciones del Carrito:**
```javascript
// Agregar producto al carrito
addToCart(product, quantity = 1)

// Eliminar producto del carrito
removeFromCart(productId)

// Actualizar cantidad
increaseQuantity(productId)
decreaseQuantity(productId)
updateQuantity(productId, newQuantity)

// Utilidades
clearCart()
isInCart(productId)
getItemQuantity(productId)
validateStock()
getCheckoutData()
```

**Persistencia:**
- Guarda automáticamente en `localStorage`
- Carga datos al inicializar la aplicación
- Manejo de errores en la persistencia

---

## 🎯 Vistas Principales

### 🏡 Home.vue - Página de Inicio

**Secciones:**
1. **Hero Section**: Imagen de fondo con call-to-action
2. **Carrusel de Productos**: Productos destacados
3. **Categorías Destacadas**: Grid de categorías principales
4. **Sobre Nosotros**: Información de la empresa

**Integraciones:**
- CarruselProductos component
- API de productos y categorías
- Navegación fluida a catálogo

---

### 📦 Products.vue - Catálogo de Productos

**Funcionalidades:**
- Grid responsive de productos
- Filtros por categoría y búsqueda
- Paginación automática
- Loading states y skeleton loaders

**Filtros Implementados:**
```javascript
// Filtro por categoría
filterByCategory(categoryId)

// Búsqueda por texto
searchProducts(searchTerm)

// Ordenamiento
sortProducts(sortBy, direction)
```

---

### 👁️ ProductDetail.vue - Detalle de Producto

**Características:**
- Galería de imágenes con zoom
- Información completa del producto
- Selector de cantidad
- Botón "Agregar al Carrito"
- Productos relacionados

**Estados:**
- Loading de producto
- Producto no encontrado (404)
- Stock disponible/agotado

---

### 💳 CheckoutPage.vue - Proceso de Pago

**Flujo del Checkout:**
1. **Validación del Carrito**: Verificar items y stock
2. **Información del Cliente**: Datos de facturación
3. **Resumen del Pedido**: Items y totales
4. **Integración ePayco**: Redirección a pasarela

**Validaciones:**
- Carrito no vacío
- Usuario autenticado
- Stock suficiente
- Datos completos del cliente

---

## 🔧 Sistema de Enrutamiento

### Configuración de Rutas

**Rutas Públicas:**
```javascript
{
  path: '/',
  name: 'Home',
  component: Home
},
{
  path: '/products',
  name: 'Products',
  component: Products
},
{
  path: '/product/:slug',
  name: 'ProductDetail',
  component: ProductDetail
}
```

**Rutas Protegidas (Clientes):**
```javascript
{
  path: '/checkout',
  name: 'Checkout',
  component: CheckoutPage,
  meta: { requiresAuth: true }
},
{
  path: '/account',
  name: 'Account',
  component: AccountPage,
  meta: { requiresAuth: true }
}
```

**Rutas Administrativas:**
```javascript
{
  path: '/admin',
  component: AdminLayout,
  meta: { requiresAdmin: true },
  children: [
    { path: '', name: 'AdminDashboard', component: AdminDashboard },
    { path: 'products', name: 'AdminProducts', component: AdminProductos }
  ]
}
```

### Guards de Navegación

```javascript
// Guard para autenticación de clientes
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next({ name: 'Home' })
  } else {
    next()
  }
})
```

---

## 🎨 Sistema de Estilos

### CSS Personalizado + Quasar UI

**Configuración:**
- **CSS Puro**: Estilos personalizados y específicos por componente
- **Quasar Framework**: Componentes UI como botones, inputs, dialogs, notificaciones
- **TailwindCSS**: Configurado pero usado mínimamente (principalmente en admin)
- **Gradientes y Efectos**: Uso extensivo de gradientes CSS y animaciones

**Tema de Colores:**
```css
/* Colores principales del proyecto */
.navbar {
  background: linear-gradient(135deg, #388E3C 0%, #2E7D32 100%);
}

.hero-enhanced {
  background: linear-gradient(135deg, rgb(59, 63, 59) 0%, rgb(48, 63, 49) 80%);
}

.trust-section {
  background: linear-gradient(135deg, #F1F8E9 0%, #E8F5E8 100%);
}

/* Paleta de colores */
- Verde Principal: #2E7D32, #4CAF50
- Verde Claro: #E8F5E8, #F1F8E9
- Marrón Texto: #5D4037, #8D6E63
- Blanco/Transparencias: rgba(255,255,255,0.1-0.9)
```

### Responsive Design

**Breakpoints:**
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

**Estrategia Mobile-First:**
```css
/* Mobile por defecto */
.product-grid {
  grid-template-columns: 1fr;
}

/* Tablet */
@media (min-width: 768px) {
  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Desktop */
@media (min-width: 1024px) {
  .product-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
```

---

## ⚡ Optimizaciones de Performance

### Lazy Loading

**Componentes:**
```javascript
// Lazy loading de vistas
const ProductDetail = () => import('../views/ProductDetail.vue')
const AdminLayout = () => import('../components/Admin/AdminLayout.vue')
```

**Imágenes:**
```vue
<img
  :src="imageUrl"
  loading="lazy"
  :alt="product.name"
/>
```

### Bundle Splitting

**Vite Configuration:**
```javascript
export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia'],
          admin: ['./src/components/Admin/'],
          auth: ['./src/components/Auth/']
        }
      }
    }
  }
})
```

---

## 🔌 Integración con API

### Configuración de Axios

**Base Configuration:**
```javascript
// plugins/auth.js
axios.defaults.baseURL = '/api'
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Accept'] = 'application/json'
```

### Interceptores

**Request Interceptor:**
```javascript
axios.interceptors.request.use(
  config => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => Promise.reject(error)
)
```

**Response Interceptor:**
```javascript
axios.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore()
      authStore.clearAuthData()
      router.push('/login')
    }
    return Promise.reject(error)
  }
)
```

---

## 🎭 Manejo de Estados y Loading

### Loading States

**Global Loading:**
```javascript
// En stores
const isLoading = ref(false)

// En componentes
const loading = computed(() => authStore.loading || productStore.loading)
```

**Skeleton Loaders:**
```vue
<template>
  <div v-if="loading" class="skeleton-loader">
    <div class="skeleton-item"></div>
    <div class="skeleton-item"></div>
  </div>
  <div v-else>
    <!-- Contenido real -->
  </div>
</template>
```

### Error Handling

**Error Display:**
```vue
<template>
  <div v-if="hasErrors" class="error-container">
    <div v-for="(error, field) in errors" :key="field" class="error-message">
      {{ error[0] }}
    </div>
  </div>
</template>
```

---

## 💡 Mejores Prácticas Implementadas

### Composition API

**Estructura de Componente:**
```vue
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

// Refs reactivos
const loading = ref(false)
const products = ref([])

// Computed properties
const filteredProducts = computed(() => {
  return products.value.filter(product => product.is_active)
})

// Lifecycle hooks
onMounted(async () => {
  await loadProducts()
})

// Methods
const loadProducts = async () => {
  // Lógica de carga
}
</script>
```

### Estado Reactivo

**Pinia Stores:**
- Estado mutable con `ref()`
- Propiedades computadas con `computed()`
- Acciones asíncronas bien manejadas

### Reutilización de Código

**Composables:**
```javascript
// composables/useProducts.js
export function useProducts() {
  const products = ref([])
  const loading = ref(false)
  
  const loadProducts = async () => {
    loading.value = true
    try {
      const response = await axios.get('/products')
      products.value = response.data
    } finally {
      loading.value = false
    }
  }
  
  return {
    products: readonly(products),
    loading: readonly(loading),
    loadProducts
  }
}
```

---

## 🚀 Build y Desarrollo

### Comandos de Desarrollo

```bash
# Desarrollo con hot reload
npm run dev

# Build para producción
npm run build

# Preview del build
npm run preview
```

### Configuración de Vite

**Optimizaciones:**
- Hot Module Replacement (HMR)
- Tree shaking automático
- CSS code splitting
- Compresión de assets

---

## 📱 Funcionalidades Móviles

### PWA Features

**Service Worker:** (Potencial implementación)
- Cache de assets estáticos
- Offline functionality básica
- Update notifications

### Touch Interactions

- Swipe en carrusel de productos
- Touch-friendly buttons (min 44px)
- Scroll smooth en listados

---

## 🔍 SEO y Accesibilidad

### Meta Tags Dinámicos

```javascript
// En cada vista
export default {
  meta: {
    title: 'Productos - Artesanías Caquetá',
    description: 'Descubre nuestra colección de artesanías...'
  }
}
```

### Accesibilidad

- **Alt tags** en todas las imágenes
- **ARIA labels** en botones interactivos
- **Contrast ratios** apropiados
- **Keyboard navigation** soportada

Esta documentación del frontend proporciona una visión completa de la arquitectura, componentes y funcionalidades implementadas en Vue.js.
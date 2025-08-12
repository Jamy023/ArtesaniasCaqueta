import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';
import axios from 'axios';

// ✅ IMPORTACIÓN CORRECTA DE QUASAR
import { Quasar, Notify, Dialog, Loading } from 'quasar'
import 'quasar/dist/quasar.css'
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/fontawesome-v6/fontawesome-v6.css'

import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

// Configuración de axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.withCredentials = true;
window.axios = axios;

// Importar componentes principales
import App from './components/App.vue';
import Home from './views/Home.vue';
import Products from './views/Products.vue';
import ProductDetail from './views/ProductDetail.vue';
import NotFound from './views/NotFound.vue';
import Registro from './components/Auth/RegisterView.vue';
import CheckoutPage from './views/CheckoutPage.vue'; // 🆕 NUEVO
import AccountPage from './views/AccountPage.vue';

// Importar layout y vistas del admin
import AdminLogin from './components/Admin/AdminLogin.vue'

// Configurar rutas
const routes = [
  // Rutas públicas
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
    path: '/products/:slug',
    name: 'ProductDetail',
    component: ProductDetail,
    props: true
  },
  {
    path: '/register',
    name: 'Registro',
    component: Registro,
    props: route => ({ redirect: route.query.redirect }),

      meta: { 
    hideNav: true,
    hideFooter: true 
  }
  },
  // 🆕 NUEVA RUTA DE CHECKOUT
  {
    path: '/checkout',
    name: 'Checkout',
    component: CheckoutPage,
    meta: { 
      requiresAuth: true,
      title: 'Finalizar Compra'
    }
  },
  {
    path: '/account',
    name: 'Account',
    component: AccountPage,
    meta: { 
      requiresAuth: true,
      title: 'Mi Cuenta'
    }
  },
  
  // Rutas administrativas
  {
    path: '/admin/login',
    name: 'admin-login',
    component: AdminLogin,
    meta: { 
      requiresGuest: true,
      layout: 'clean'
    }
  },   
  {
    path: '/admin',
    redirect: '/admin/dashboard',
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('./components/Admin/AdminDashboard.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'admin-dashboard-home',
        component: () => import('./components/Admin/AdminHome.vue')
      },
      { 
        path: 'productos', 
        name: 'admin-productos',
        component: () => import('./components/Admin/AdminProductos.vue') 
      },
      { 
        path: 'categorias', 
        name: 'admin-categorias',
        component: () => import('./components/Admin/AdminCategorias.vue') 
      },
      { 
        path: 'usuarios', 
        name: 'admin-usuarios',
        component: () => import('./components/Admin/AdminUsuarios.vue') 
      },
      { 
        path: 'pedidos', 
        name: 'admin-pedidos',
        component: () => import('./components/Admin/AdminPedidos.vue') 
      },
    ]
  },
  
  // Ruta 404
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound
  }
];

// Crear router
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  }
});

// Crear Pinia y app
const pinia = createPinia();
const app = createApp(App);

// Configuración de Quasar
app.use(Quasar, {
  plugins: {
    Notify,
    Dialog,
    Loading
  },
  config: {
    notify: {
      position: 'top-right',
      timeout: 2500,
      textColor: 'white',
      actions: [{ icon: 'close', color: 'white' }]
    }
  }
})

app.use(pinia);
app.use(router);

// Importar stores
import { useAdminAuthStore } from './stores/adminAuthStore';
import { useAuthStore } from './stores/authStore'; // 🆕 NUEVO
import { useCartStore } from './stores/cartStore';

// Guards de navegación
router.beforeEach(async (to, from, next) => {
  const adminAuthStore = useAdminAuthStore();
  const authStore = useAuthStore(); // 🆕 NUEVO
  const cartStore = useCartStore();
  
  // Inicializar autenticación de admin si no se ha hecho
  if (!adminAuthStore.initialized) {
    await adminAuthStore.initAuth();
  }
  
  // Inicializar autenticación de cliente
  await authStore.initAuth();
  
  // Cargar carrito desde localStorage
  cartStore.loadFromLocalStorage();

  // 🆕 VERIFICAR RUTAS QUE REQUIEREN AUTENTICACIÓN DE CLIENTE
  if (to.meta.requiresAuth && !to.path.startsWith('/admin')) {
    if (!authStore.isAuthenticated) {
      console.log('Ruta protegida de cliente, redirigiendo a registro');
      return next({ 
        name: 'Registro', 
        query: { redirect: to.path } 
      });
    }
  }

  // Verificar rutas que requieren autenticación de admin
  if (to.meta.requiresAuth && to.path.startsWith('/admin')) {
    if (!adminAuthStore.isLoggedIn) {
      console.log('Ruta protegida de admin, redirigiendo a login');
      return next({ name: 'admin-login' });
    }
  }

  // Verificar rutas que requieren NO estar autenticado (como login)
  if (to.meta.requiresGuest) {
    if (adminAuthStore.isLoggedIn) {
      console.log('Ya está autenticado como admin, redirigiendo a dashboard');
      return next({ name: 'admin-dashboard' });
    }
  }

  next();
});

// Interceptor para manejar errores de autenticación
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Si hay error 401 en rutas de admin, cerrar sesión admin
      if (router.currentRoute.value.path.startsWith('/admin')) {
        const adminAuthStore = useAdminAuthStore();
        adminAuthStore.logout();
        router.push('/admin/login');
      } 
      // Si hay error 401 en rutas de cliente, cerrar sesión cliente
      else {
        const authStore = useAuthStore();
        authStore.logout();
        router.push('/register');
      }
    }
    return Promise.reject(error);
  }
);

// Configurar Toast
app.use(Toast, {
  position: POSITION.BOTTOM_RIGHT,
  timeout: 5000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: true,
  hideProgressBar: false,
  progressStyle: {
    background: '#3b82f6'
  }
});

// Manejo de errores
router.onError((error) => {
  console.error('Router error:', error);
});

app.config.errorHandler = (error, instance, info) => {
  console.error('Global error:', error, info);
};
const cartStore = useCartStore()
cartStore.loadFromLocalStorage()
// Montar aplicación
app.mount('#app');
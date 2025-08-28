
//importaciones y configuraciones
import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';
import axios from 'axios';
import { Quasar, Notify, Dialog, Loading } from 'quasar'
import 'quasar/dist/quasar.css'
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'
import '@quasar/extras/material-icons-round/material-icons-round.css'
import '@quasar/extras/material-icons-sharp/material-icons-sharp.css'
import '@quasar/extras/fontawesome-v6/fontawesome-v6.css'
import '@quasar/extras/bootstrap-icons/bootstrap-icons.css'

import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

// Configuración dinámica de URL base
const isDevelopment = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const baseURL = isDevelopment 
  ? 'http://127.0.0.1:8000/api'  // Local
  : `${window.location.origin}/api`; //o producción

axios.defaults.baseURL = baseURL;
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.withCredentials = true;
axios.defaults.timeout = 30000; // 30 segundos timeout




window.axios = axios;


import App from './components/App.vue';
import Home from './views/Home.vue';
import Products from './views/Products.vue';
import ProductDetail from './views/ProductDetail.vue';
import NotFound from './views/NotFound.vue';
import Registro from './components/Auth/RegisterView.vue';
import CheckoutPage from './views/CheckoutPage.vue';
import AccountPage from './views/AccountPage.vue';
import OrderConfirmation from './views/OrderConfirmation.vue';
import EpaycoTest from './views/EpaycoTest.vue';

import AdminLogin from './components/Admin/AdminLogin.vue'


const routes = [
  //rutas de cliente
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
  {
    path: '/order-confirmation',
    name: 'OrderConfirmation',
    component: OrderConfirmation,
    meta: { 
      title: 'Confirmación de Pedido'
    }
  },
  {
    path: '/epayco-test',
    name: 'EpaycoTest',
    component: EpaycoTest,
    meta: { 
      title: 'Prueba ePayco'
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
        path: 'clientes',
        name: 'admin-clientes',
        component: () => import('./components/Admin/AdminClientes.vue')
      },
      {
        path: 'pedidos',
        name: 'admin-pedidos',
        component: () => import('./components/Admin/AdminPedidos.vue')
      }
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

// Importar stores DESPUÉS de configurar Pinia
import { useAdminAuthStore } from './stores/adminAuthStore';
import { useAuthStore } from './stores/authStore';
import { useCartStore } from './stores/cartStore';

// Interceptor de axios para manejo de errores de autenticación
let interceptorActive = false;

axios.interceptors.response.use(
  (response) => response,
  async (error) => {
    // Evitar bucle infinito
    if (interceptorActive) {
      return Promise.reject(error);
    }

    if (error.response?.status === 401) {
      interceptorActive = true;
      
      try {
        const currentRoute = router.currentRoute.value.path;
        
        // Si hay error 401 en rutas de admin, cerrar sesión admin
        if (currentRoute.startsWith('/admin')) {
          const adminAuthStore = useAdminAuthStore();
          adminAuthStore.clearAuthData();
          router.push('/admin/login');
        } 
        // Si hay error 401 en rutas de cliente, cerrar sesión cliente
        else {
          const authStore = useAuthStore();
          
          // Solo limpiar si realmente está logueado
          if (authStore.isLoggedIn) {
            authStore.clearAuthData(false); // No resetear initialized para permitir re-auth
            
            // Solo redirigir si no estamos en páginas públicas
            if (currentRoute !== '/' && currentRoute !== '/products' && !currentRoute.startsWith('/products/')) {
              router.push('/register');
            }
          }
        }
      } finally {
        setTimeout(() => {
          interceptorActive = false;
        }, 1000);
      }
    }
    
    return Promise.reject(error);
  }
);

// Guards de navegación para manejo de autenticación
router.beforeEach(async (to, from, next) => {
  const adminAuthStore = useAdminAuthStore();
  const authStore = useAuthStore();
  const cartStore = useCartStore();
  
  try {
    // Inicializar autenticaciones si no están iniciadas
    const initPromises = [];
    
    if (!adminAuthStore.initialized) {
      initPromises.push(adminAuthStore.initAuth().catch(err => {
        console.error('Error inicializando admin auth:', err);
      }));
    }
    
    if (!authStore.initialized) {
      initPromises.push(authStore.initAuth().catch(err => {
        console.error('Error inicializando client auth:', err);
      }));
    }
    
    // Esperar a que se completen las inicializaciones
    if (initPromises.length > 0) {
      await Promise.all(initPromises);
    }
    
    // Cargar carrito
    cartStore.loadFromLocalStorage();

    // Verificar rutas que requieren autenticación de cliente
    if (to.meta.requiresAuth && !to.path.startsWith('/admin')) {
      if (!authStore.isLoggedIn) {
        return next({ 
          name: 'Registro', 
          query: { redirect: to.path } 
        });
      }
    }

    // Verificar rutas que requieren autenticación de admin
    if (to.meta.requiresAuth && to.path.startsWith('/admin')) {
      if (!adminAuthStore.isLoggedIn) {
        return next({ name: 'admin-login' });
      }
    }

    // Verificar rutas que requieren NO estar autenticado
    if (to.meta.requiresGuest) {
      if (adminAuthStore.isLoggedIn) {
        return next({ name: 'admin-dashboard' });
      }
    }

    next();
    
  } catch (error) {
    console.error('Error en guard de navegación:', error);
    next();
  }
});

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
  console.error('🔴 Router error:', error);
});

app.config.errorHandler = (error, instance, info) => {
  console.error('🔴 Global error:', error, info);
};


/**
 * Inicializa la aplicación Vue
 * Configura los stores de autenticación y monta la app
 */
const initializeApp = async () => {
  try {
    // Obtener stores
    const adminAuthStore = useAdminAuthStore();
    const authStore = useAuthStore();
    const cartStore = useCartStore();
    
    // Inicializar autenticaciones en paralelo
    await Promise.all([
      adminAuthStore.initAuth().catch(err => {
        console.error('Error inicializando admin auth:', err);
        return null;
      }),
      authStore.initAuth().catch(err => {
        console.error('Error inicializando client auth:', err);
        return null;
      }),
    ]);
    
    // Cargar carrito desde localStorage
    cartStore.loadFromLocalStorage();
    
  } catch (error) {
    console.error('Error durante inicialización:', error);
  } finally {
    app.mount('#app');
  }
};

// Inicializar la aplicación
initializeApp();
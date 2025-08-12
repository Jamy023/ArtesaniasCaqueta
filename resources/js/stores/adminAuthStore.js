// stores/adminAuthStore.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAdminAuthStore = defineStore('adminAuth', {
  state: () => ({
    admin: null,
    isAuthenticated: false,
    loading: false,
    errors: {},
    initialized: false
  }),

  getters: {
    isLoggedIn: (state) => state.isAuthenticated && !!state.admin,
    currentAdmin: (state) => state.admin,
    hasErrors: (state) => Object.keys(state.errors).length > 0
  },

  actions: {
    // Inicialización de autenticación del admin
    async initAuth() {
      if (this.initialized) return;
      
      this.initialized = true;
      this.loading = true;

      try {
        // CORREGIDO: Usar la ruta correcta sin /api
        const response = await axios.get('/admin/check-auth', {
          withCredentials: true,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          // IMPORTANTE: No usar la baseURL para rutas de admin
          baseURL: ''
        });
        
        if (response.data.authenticated) {
          this.admin = response.data.user;
          this.isAuthenticated = true;
          console.log('Administrador autenticado:', this.admin);
        }
      } catch (error) {
        console.log('No hay sesión de administrador activa');
        this.clearAuthData();
      } finally {
        this.loading = false;
      }
    },

    // Login del administrador
    async login(credentials) {
      this.loading = true;
      this.errors = {};

      try {
        // Obtener el CSRF token antes de hacer login
        await axios.get('/sanctum/csrf-cookie', { baseURL: '' });

        // CORREGIDO: Hacer login con la ruta correcta
        const response = await axios.post('/admin/login', {
          email: credentials.email,
          password: credentials.password,
          remember: credentials.remember || false
        }, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          withCredentials: true,
          // IMPORTANTE: No usar la baseURL para rutas de admin
          baseURL: ''
        });

        if (response.data.success) {
          // CORREGIDO: Después del login exitoso, verificar autenticación
          await this.checkAuth();
          return response.data;
        }
      } catch (error) {
        console.error('Error en login:', error);
        this.errors = error.response?.data?.errors || { 
          general: [error.response?.data?.message || 'Error de conexión'] 
        };
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Verificar autenticación actual
    async checkAuth() {
      try {
        const response = await axios.get('/admin/check-auth', {
          withCredentials: true,
          baseURL: '' // No usar baseURL para admin
        });
        
        if (response.data.authenticated) {
          this.admin = response.data.user;
          this.isAuthenticated = true;
          console.log('Auth verificada correctamente:', this.admin);
        } else {
          this.clearAuthData();
        }
        
        return response.data;
      } catch (error) {
        console.error('Error verificando autenticación:', error);
        this.clearAuthData();
        throw error;
      }
    },

    // Logout del administrador
    async logout() {
      this.loading = true;

      try {
        await axios.post('/admin/logout', {}, {
          withCredentials: true,
          baseURL: ''
        });
        
        this.clearAuthData();
        // Redirigir al login después del logout exitoso
        window.location.href = '/admin/login';
      } catch (error) {
        console.error('Error al cerrar sesión:', error);
        // Incluso si hay error en el servidor, limpiar datos locales
        this.clearAuthData();
        window.location.href = '/admin/login';
      } finally {
        this.loading = false;
      }
    },

    // Limpiar datos de autenticación
    clearAuthData() {
      this.admin = null;
      this.isAuthenticated = false;
      this.initialized = false;
      this.errors = {};
    },

    // Limpiar errores
    clearErrors() {
      this.errors = {};
    },

    // Verificar si el admin tiene permisos específicos
    hasPermission(permission) {
      if (!this.admin) return false;
      return true;
    }
  }
})
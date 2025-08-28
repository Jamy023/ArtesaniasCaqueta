// stores/adminAuthStore.js - Store de autenticación de administradores con sesiones de Laravel
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAdminAuthStore = defineStore('adminAuth', {
  // ==================== ESTADO ====================
  
  state: () => ({
    /** @type {Object|null} Datos del administrador autenticado */
    admin: null,
    
    /** @type {boolean} Indica si el administrador está autenticado */
    isAuthenticated: false,
    
    /** @type {boolean} Indica si se está realizando alguna operación de auth */
    loading: false,
    
    /** @type {Object} Errores de validación o autenticación */
    errors: {},
    
    /** @type {boolean} Indica si ya se inicializó la autenticación */
    initialized: false
  }),

  // ==================== GETTERS ====================

  getters: {
    /**
     * Verifica si el administrador está completamente autenticado
     * @returns {boolean} True si está autenticado y tiene datos de admin
     */
    isLoggedIn: (state) => state.isAuthenticated && !!state.admin,
    
    /**
     * Obtiene los datos del administrador actual
     * @returns {Object|null} Datos del administrador o null
     */
    currentAdmin: (state) => state.admin,
    
    /**
     * Verifica si hay errores en el store
     * @returns {boolean} True si existen errores
     */
    hasErrors: (state) => Object.keys(state.errors).length > 0
  },

  // ==================== ACCIONES ====================

  actions: {
    /**
     * Inicializa la autenticación del administrador
     * Verifica si hay una sesión activa en el servidor
     * Solo se ejecuta una vez por sesión de la aplicación
     */
    async initAuth() {
      // Si ya está inicializado, no hacer nada
      if (this.initialized) return;
      
      this.initialized = true;
      this.loading = true;

      try {
        // Verificar si hay una sesión activa en el servidor
        // Nota: Se usa baseURL: '' para evitar el prefijo /api
        const response = await axios.get('/admin/check-auth', {
          withCredentials: true, // Incluir cookies de sesión
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          baseURL: '' // No usar la baseURL configurada para API
        });
        
        // Si el servidor confirma autenticación, establecer datos
        if (response.data.authenticated) {
          this.admin = response.data.user;
          this.isAuthenticated = true;
        }
      } catch (error) {
        // Si hay error (401, 403, etc.), limpiar datos locales
        this.clearAuthData();
      } finally {
        this.loading = false;
      }
    },

    /**
     * Autentica un administrador con email y password
     * Utiliza sesiones de Laravel (no tokens) para administradores
     * @param {Object} credentials - Credenciales del administrador
     * @param {string} credentials.email - Email del administrador
     * @param {string} credentials.password - Password del administrador
     * @param {boolean} [credentials.remember=false] - Si recordar la sesión
     * @returns {Object} Respuesta del servidor
     */
    async login(credentials) {
      this.loading = true;
      this.errors = {};

      try {
        // Obtener el CSRF token necesario para Laravel
        await axios.get('/sanctum/csrf-cookie', { baseURL: '' });

        // Enviar credenciales al endpoint de login de admin
        const response = await axios.post('/admin/login', {
          email: credentials.email,
          password: credentials.password,
          remember: credentials.remember || false
        }, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          withCredentials: true, // Incluir cookies de sesión
          baseURL: '' // No usar baseURL para rutas de admin
        });

        // Si login exitoso, verificar autenticación para obtener datos del usuario
        if (response.data.success) {
          await this.checkAuth();
          return response.data;
        }
      } catch (error) {
        // Manejar errores de validación o autenticación
        this.errors = error.response?.data?.errors || { 
          general: [error.response?.data?.message || 'Error de conexión'] 
        };
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Verifica el estado actual de autenticación con el servidor
     * Útil para refrescar datos del usuario después del login
     * @returns {Object} Respuesta del servidor con estado de autenticación
     */
    async checkAuth() {
      try {
        const response = await axios.get('/admin/check-auth', {
          withCredentials: true,
          baseURL: '' // No usar baseURL para admin
        });
        
        if (response.data.authenticated) {
          this.admin = response.data.user;
          this.isAuthenticated = true;
        } else {
          this.clearAuthData();
        }
        
        return response.data;
      } catch (error) {
        // Si falla la verificación, limpiar datos locales
        this.clearAuthData();
        throw error;
      }
    },

    /**
     * Cierra la sesión del administrador
     * Invalida la sesión en el servidor y redirige al login
     */
    async logout() {
      this.loading = true;

      try {
        // Invalidar sesión en el servidor
        await axios.post('/admin/logout', {}, {
          withCredentials: true,
          baseURL: ''
        });
        
        this.clearAuthData();
        
        // Redirigir al login después del logout exitoso
        window.location.href = '/admin/login';
      } catch (error) {
        // Incluso si hay error en el servidor, limpiar datos locales
        this.clearAuthData();
        window.location.href = '/admin/login';
      } finally {
        this.loading = false;
      }
    },

    /**
     * Limpia todos los datos de autenticación del administrador
     * Resetea el store a su estado inicial
     */
    clearAuthData() {
      this.admin = null;
      this.isAuthenticated = false;
      this.initialized = false;
      this.errors = {};
    },

    /**
     * Limpia todos los errores del store
     */
    clearErrors() {
      this.errors = {};
    },

    /**
     * Verifica si el administrador tiene un permiso específico
     * Placeholder para futura implementación de roles y permisos
     * @param {string} permission - Nombre del permiso a verificar
     * @returns {boolean} True si tiene el permiso (actualmente siempre true)
     */
    hasPermission(permission) {
      if (!this.admin) return false;
      
      // TODO: Implementar sistema de roles y permisos
      // Por ahora, todos los admins tienen todos los permisos
      return true;
    }
  }
})
// stores/authStore.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    cliente: null,
    token: localStorage.getItem('auth_token') || null,
    isAuthenticated: false,
    loading: false,
    errors: {},
    initialized: false
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && !!state.cliente && state.isAuthenticated,
    currentUser: (state) => state.cliente,
    hasErrors: (state) => Object.keys(state.errors).length > 0
  },

  actions: {
    // Configurar axios con el token
    setAxiosToken(token) {
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        localStorage.setItem('auth_token', token)
      } else {
        delete axios.defaults.headers.common['Authorization']
        localStorage.removeItem('auth_token')
      }
    },

    // Inicialización mejorada con mejor manejo de errores
    async initAuth() {
      if (this.initialized) return;
      
      this.initialized = true;
      
      if (this.token && !this.cliente) {
        // Verificar si el token es válido y no está expirado
        if (!this.isValidToken() || this.isTokenExpired()) {
          console.log('Token inválido o expirado, limpiando...');
          this.clearAuthData();
          return;
        }

        this.setAxiosToken(this.token);
        this.loading = true;
        
        try {
          await this.getProfile();
          this.isAuthenticated = true;
          console.log('Usuario autenticado correctamente desde localStorage');
        } catch (error) {
          console.error('Error al verificar token:', error);
          this.clearAuthData();
        } finally {
          this.loading = false;
        }
      }
    },

    async login(credentials) {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.post('/clientes/login', {
          email: credentials.email,
          password: credentials.password
        })

        const { cliente, token } = response.data

        this.cliente = cliente
        this.token = token
        this.isAuthenticated = true
        this.setAxiosToken(token)

        return response.data
      } catch (error) {
        console.error('Error en login:', error)
        this.errors = error.response?.data?.errors || { general: ['Error de conexión'] }
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.post('/clientes/register', userData)

        const { cliente, token } = response.data

        this.cliente = cliente
        this.token = token
        this.isAuthenticated = true
        this.setAxiosToken(token)

        return response.data
      } catch (error) {
        console.error('Error en registro:', error)
        this.errors = error.response?.data?.errors || { general: ['Error de conexión'] }
        throw error
      } finally {
        this.loading = false
      }
    },

    // Logout mejorado - no falla si el servidor devuelve 401
    async logout() {
      this.loading = true

      try {
        // Solo intentar logout en servidor si tenemos un token válido
        if (this.token && this.isValidToken()) {
          await axios.post('/clientes/logout')
        }
      } catch (error) {
        // Si es 401, significa que el token ya no es válido, lo cual está bien
        if (error.response?.status !== 401) {
          console.error('Error al cerrar sesión:', error)
        }
      } finally {
        // Siempre limpiar los datos locales, independientemente del resultado del servidor
        this.clearAuthData()
        this.loading = false
      }
    },

    // Función para limpiar todos los datos de autenticación
    clearAuthData() {
      this.cliente = null
      this.token = null
      this.isAuthenticated = false
      this.initialized = false
      this.setAxiosToken(null)
      this.errors = {}
    },

    async getProfile() {
      try {
        const response = await axios.get('/clientes/profile')
        this.cliente = response.data.cliente
        this.isAuthenticated = true
        return response.data
      } catch (error) {
        console.error('Error obteniendo perfil:', error)
        throw error
      }
    },

    // 🔥 MÉTODO ACTUALIZADO: updateProfile
    async updateProfile(profileData) {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.put('/clientes/profile', profileData)
        
        // Actualizar el cliente en el store con los nuevos datos
        this.cliente = response.data.cliente
        
        return response.data
      } catch (error) {
        console.error('Error actualizando perfil:', error)
        this.errors = error.response?.data?.errors || { general: ['Error de conexión'] }
        throw error
      } finally {
        this.loading = false
      }
    },

    clearErrors() {
      this.errors = {}
    },

    // Verificar si el token tiene formato JWT válido
    isValidToken() {
      if (!this.token) return false;
      
      try {
        const parts = this.token.split('.');
        if (parts.length !== 3) return false;
        
        // Intentar decodificar el payload
        JSON.parse(atob(parts[1]));
        return true;
      } catch (error) {
        console.error('Token no tiene formato JWT válido:', error);
        return false;
      }
    },

    // Verificar si el token está expirado
    isTokenExpired() {
      if (!this.token || !this.isValidToken()) return true;
      
      try {
        const payload = JSON.parse(atob(this.token.split('.')[1]));
        const currentTime = Date.now() / 1000;
        
        // Si no tiene campo exp, consideramos que no expira
        if (!payload.exp) return false;
        
        // Agregar buffer de 5 minutos
        return payload.exp < (currentTime + 300);
      } catch (error) {
        console.error('Error al verificar expiración del token:', error);
        return true;
      }
    }
  }
})
// stores/authStore.js - Store de autenticación de clientes con Sanctum
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
    // Verifica si el cliente está completamente autenticado
    isLoggedIn: (state) => {
      return !!state.token && !!state.cliente && state.isAuthenticated
    },
    currentUser: (state) => state.cliente,
    hasErrors: (state) => Object.keys(state.errors).length > 0
  },

  actions: {
    /**
     * Configura el token de autenticación en axios y localStorage
     * @param {string|null} token - Token de Sanctum o null para limpiar
     */
    setAxiosToken(token) {
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        localStorage.setItem('auth_token', token)
      } else {
        delete axios.defaults.headers.common['Authorization']
        localStorage.removeItem('auth_token')
      }
    },

    /**
     * Inicializa la autenticación del cliente
     * Verifica si hay un token guardado y lo valida con el servidor
     */
    async initAuth() {
      // Si ya está inicializado y autenticado correctamente, no hacer nada
      if (this.initialized && this.isAuthenticated && this.cliente) {
        return
      }
      
      this.initialized = true
      
      // Sincronizar token del localStorage si existe y es diferente
      const storedToken = localStorage.getItem('auth_token')
      if (storedToken && storedToken !== this.token) {
        this.token = storedToken
      }
      
      // Si no hay token, usuario no autenticado
      if (!this.token) {
        this.isAuthenticated = false
        this.cliente = null
        return
      }

      // Configurar axios con el token existente
      this.setAxiosToken(this.token)
      this.loading = true
      
      try {
        // Verificar token con el servidor obteniendo el perfil
        await this.getProfile()
        
        // Si llegamos aquí, el token es válido
        this.isAuthenticated = true
        
      } catch (error) {
        // Si error 401/403, el token no es válido
        if (error.response && [401, 403].includes(error.response.status)) {
          this.clearAuthData(true) // resetear initialized porque el token es inválido
        }
        // Para otros errores, mantener el token para reintentar después
        
      } finally {
        this.loading = false
      }
    },

    /**
     * Obtiene el perfil del cliente autenticado desde el servidor
     * @returns {Object} Respuesta del servidor con los datos del cliente
     */
    async getProfile() {
      if (!this.token) {
        throw new Error('No hay token disponible')
      }

      try {
        const response = await axios.get('/clientes/profile')
        
        this.cliente = response.data.cliente
        this.isAuthenticated = true
        
        return response.data
      } catch (error) {
        // Si es 401/403, limpiar datos de auth porque el token no es válido
        if (error.response && [401, 403].includes(error.response.status)) {
          this.clearAuthData(true) // resetear initialized porque el token es inválido
        }
        
        throw error
      }
    },

    /**
     * Autentica un cliente con email y password
     * @param {Object} credentials - Credenciales del cliente
     * @param {string} credentials.email - Email del cliente
     * @param {string} credentials.password - Password del cliente
     * @returns {Object} Respuesta del servidor con cliente y token
     */
    async login(credentials) {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.post('/clientes/login', {
          email: credentials.email,
          password: credentials.password
        })

        const { cliente, token } = response.data

        // Establecer todos los datos de autenticación
        this.cliente = cliente
        this.token = token
        this.isAuthenticated = true
        this.setAxiosToken(token)

        return response.data
        
      } catch (error) {
        this.errors = error.response?.data?.errors || { 
          general: [error.response?.data?.message || 'Error de conexión'] 
        }
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Cierra la sesión del cliente
     * Invalida el token en el servidor y limpia los datos locales
     */
    async logout() {
      this.loading = true

      try {
        // Intentar invalidar el token en el servidor si es válido
        if (this.token && this.isValidToken()) {
          await axios.post('/clientes/logout')
        }
      } catch (error) {
        // Si hay error 401, el token ya no era válido
        // Para otros errores, continuamos con la limpieza local
        if (error.response?.status !== 401) {
          console.error('Error en logout del servidor:', error)
        }
      } finally {
        // Limpiar datos locales (no resetear initialized para permitir re-auth)
        this.clearAuthData(false)
        this.loading = false
      }
    },

    /**
     * Limpia todos los datos de autenticación
     * @param {boolean} resetInitialized - Si debe resetear la flag initialized
     *                                   - true: para tokens inválidos (fuerza re-inicialización)
     *                                   - false: para logout normal (permite re-auth en recarga)
     */
    clearAuthData(resetInitialized = false) {
      this.cliente = null
      this.token = null
      this.isAuthenticated = false
      this.errors = {}
      
      // Solo resetear initialized si se especifica explícitamente
      // Esto permite que la app se re-inicialice en recarga de página
      if (resetInitialized) {
        this.initialized = false
      }
      
      // Limpiar token de axios y localStorage
      this.setAxiosToken(null)
    },

    /**
     * Actualiza el perfil del cliente autenticado
     * @param {Object} profileData - Datos del perfil a actualizar
     * @returns {Object} Respuesta del servidor con el cliente actualizado
     */
    async updateProfile(profileData) {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.put('/clientes/profile', profileData)
        this.cliente = response.data.cliente
        return response.data
      } catch (error) {
        this.errors = error.response?.data?.errors || { general: ['Error de conexión'] }
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Limpia todos los errores del store
     */
    clearErrors() {
      this.errors = {}
    },

    /**
     * Verifica si el token es válido
     * Para tokens de Sanctum, solo verificamos que exista y tenga longitud suficiente
     * @returns {boolean} True si el token es válido
     */
    isValidToken() {
      return !!this.token && this.token.length > 40
    },

    /**
     * Verifica si el token está expirado
     * Los tokens de Sanctum no tienen expiración por defecto (son permanentes hasta logout)
     * La expiración se maneja en el backend
     * @returns {boolean} Siempre false para tokens de Sanctum
     */
    isTokenExpired() {
      return false
    },

    /**
     * Registra un nuevo cliente
     * @param {Object} userData - Datos del cliente a registrar
     * @returns {Object} Respuesta del servidor con cliente y token
     */
    async register(userData) {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.post('/clientes/register', userData)
        const { cliente, token } = response.data

        // Establecer todos los datos de autenticación
        this.cliente = cliente
        this.token = token
        this.isAuthenticated = true
        this.setAxiosToken(token)

        return response.data
        
      } catch (error) {
        this.errors = error.response?.data?.errors || { 
          general: [error.response?.data?.message || 'Error de conexión'] 
        }
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fuerza una re-autenticación completa
     * Útil cuando se necesita refrescar el estado de autenticación
     */
    async forceReauth() {
      this.initialized = false
      await this.initAuth()
    }
  }
})
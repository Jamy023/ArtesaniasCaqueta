// stores/authStore.js - VERSIÓN CON DEBUG INTENSIVO
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
    isLoggedIn: (state) => {
      const result = !!state.token && !!state.cliente && state.isAuthenticated
      console.log('🔍 isLoggedIn getter:', {
        hasToken: !!state.token,
        hasCliente: !!state.cliente,
        isAuthenticated: state.isAuthenticated,
        result
      })
      return result
    },
    currentUser: (state) => state.cliente,
    hasErrors: (state) => Object.keys(state.errors).length > 0
  },

  actions: {
    // Configurar axios con el token
    setAxiosToken(token) {
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        localStorage.setItem('auth_token', token)
        console.log('✅ Token configurado:', {
          tokenStart: token.substring(0, 20) + '...',
          savedInLocalStorage: localStorage.getItem('auth_token') ? 'YES' : 'NO',
          axiosHeaderSet: !!axios.defaults.headers.common['Authorization']
        })
      } else {
        delete axios.defaults.headers.common['Authorization']
        localStorage.removeItem('auth_token')
        console.log('🗑️ Token eliminado de axios y localStorage')
      }
    },

    // 🔥 INICIALIZACIÓN CON DEBUG DETALLADO
    async initAuth() {
      console.log('🚀 === INICIANDO AUTENTICACIÓN ===')
      
      if (this.initialized) {
        console.log('⚠️ Auth ya inicializado, saltando...')
        console.log('📊 Estado actual:', {
          token: this.token ? 'EXISTS' : 'NULL',
          cliente: this.cliente?.nombre || 'NULL',
          isAuthenticated: this.isAuthenticated
        })
        return
      }
      
      console.log('🔧 Inicializando autenticación por primera vez...')
      this.initialized = true
      
      // Verificar localStorage directamente
      const storedToken = localStorage.getItem('auth_token')
      console.log('🔍 Verificando localStorage:', {
        storedToken: storedToken ? `${storedToken.substring(0, 20)}...` : 'NULL',
        stateToken: this.token ? `${this.token.substring(0, 20)}...` : 'NULL',
        tokensMatch: storedToken === this.token
      })
      
      // Si no hay token, usuario no autenticado
      if (!this.token) {
        console.log('❌ No hay token guardado - usuario no autenticado')
        return
      }

      // Verificar validez del token
      const isValid = this.isValidToken()
      const isExpired = this.isTokenExpired()
      
      console.log('🔍 Validación de token:', {
        isValid,
        isExpired,
        tokenLength: this.token.length,
        tokenParts: this.token.split('.').length
      })
      
      if (!isValid || isExpired) {
        console.log('❌ Token inválido/expirado, limpiando...')
        this.clearAuthData()
        return
      }

      // Configurar axios inmediatamente
      this.setAxiosToken(this.token)
      this.loading = true
      
      try {
        console.log('📡 Verificando token con servidor...')
        console.log('🔗 URL de verificación:', axios.defaults.baseURL + '/clientes/profile')
        console.log('🔑 Token enviado:', `Bearer ${this.token.substring(0, 20)}...`)
        
        const response = await this.getProfile()
        
        // Marcar como autenticado si el perfil se obtuvo correctamente
        this.isAuthenticated = true
        
        console.log('✅ ¡AUTENTICACIÓN EXITOSA!', {
          user: this.cliente?.nombre,
          email: this.cliente?.email,
          isLoggedIn: this.isLoggedIn
        })
        
      } catch (error) {
        console.error('❌ ERROR EN VERIFICACIÓN:', {
          message: error.message,
          status: error.response?.status,
          statusText: error.response?.statusText,
          url: error.config?.url,
          headers: error.config?.headers
        })
        
        // Si error 401/403, token no válido
        if (error.response && [401, 403].includes(error.response.status)) {
          console.log('🔴 Token rechazado por servidor - limpiando datos')
          this.clearAuthData()
        } else {
          console.log('⚠️ Error de servidor/red - manteniendo token para retry')
        }
        
      } finally {
        this.loading = false
        console.log('🏁 Inicialización completada. Estado final:', {
          isAuthenticated: this.isAuthenticated,
          isLoggedIn: this.isLoggedIn,
          hasToken: !!this.token,
          hasCliente: !!this.cliente
        })
      }
    },

    // Obtener perfil del usuario
    async getProfile() {
      console.log('📋 Obteniendo perfil de usuario...')
      
      if (!this.token) {
        throw new Error('No hay token disponible')
      }

      try {
        const response = await axios.get('/clientes/profile')
        
        console.log('📋 Respuesta del perfil:', {
          status: response.status,
          hasCliente: !!response.data?.cliente,
          clienteName: response.data?.cliente?.nombre
        })
        
        this.cliente = response.data.cliente
        this.isAuthenticated = true
        
        return response.data
      } catch (error) {
        console.error('❌ Error obteniendo perfil:', {
          status: error.response?.status,
          message: error.response?.data?.message || error.message
        })
        
        // Si es 401/403, limpiar datos de auth
        if (error.response && [401, 403].includes(error.response.status)) {
          console.log('🔴 Perfil rechazado - token inválido')
          this.clearAuthData()
        }
        
        throw error
      }
    },

    // Login con debug
    async login(credentials) {
      console.log('🔐 === INICIANDO LOGIN ===')
      
      this.loading = true
      this.errors = {}

      try {
        console.log('📤 Enviando credenciales:', {
          email: credentials.email,
          passwordLength: credentials.password?.length
        })
        
        const response = await axios.post('/clientes/login', {
          email: credentials.email,
          password: credentials.password
        })

        console.log('📥 Respuesta del login:', {
          status: response.status,
          hasToken: !!response.data?.token,
          hasCliente: !!response.data?.cliente,
          tokenStart: response.data?.token?.substring(0, 20) + '...'
        })

        const { cliente, token } = response.data

        // Establecer todos los datos
        this.cliente = cliente
        this.token = token
        this.isAuthenticated = true
        this.setAxiosToken(token)

        console.log('✅ LOGIN EXITOSO:', {
          user: cliente?.nombre,
          email: cliente?.email,
          isLoggedIn: this.isLoggedIn
        })

        return response.data
        
      } catch (error) {
        console.error('❌ ERROR EN LOGIN:', {
          status: error.response?.status,
          message: error.response?.data?.message,
          errors: error.response?.data?.errors
        })
        
        this.errors = error.response?.data?.errors || { 
          general: [error.response?.data?.message || 'Error de conexión'] 
        }
        throw error
      } finally {
        this.loading = false
      }
    },

    // Logout con debug
    async logout() {
      console.log('🚪 === INICIANDO LOGOUT ===')
      
      this.loading = true

      try {
        if (this.token && this.isValidToken()) {
          console.log('📤 Enviando logout al servidor...')
          await axios.post('/clientes/logout')
          console.log('✅ Logout exitoso en servidor')
        }
      } catch (error) {
        if (error.response?.status !== 401) {
          console.error('⚠️ Error en logout servidor:', error)
        } else {
          console.log('⚠️ Token ya inválido en servidor (401)')
        }
      } finally {
        this.clearAuthData()
        this.loading = false
        console.log('✅ Logout completado localmente')
      }
    },

    // Limpiar datos con debug
    clearAuthData() {
      console.log('🧹 === LIMPIANDO DATOS DE AUTH ===')
      console.log('📊 Estado antes de limpiar:', {
        hasToken: !!this.token,
        hasCliente: !!this.cliente,
        isAuthenticated: this.isAuthenticated,
        initialized: this.initialized
      })
      
      this.cliente = null
      this.token = null
      this.isAuthenticated = false
      this.initialized = false
      this.errors = {}
      
      this.setAxiosToken(null)
      
      console.log('✅ Datos limpiados. Estado después:', {
        hasToken: !!this.token,
        hasCliente: !!this.cliente,
        isAuthenticated: this.isAuthenticated,
        initialized: this.initialized,
        localStorageToken: localStorage.getItem('auth_token')
      })
    },

    // Resto de métodos sin cambios...
    async updateProfile(profileData) {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.put('/clientes/profile', profileData)
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

    isValidToken() {
      if (!this.token) return false
      
      try {
        const parts = this.token.split('.')
        if (parts.length !== 3) return false
        
        JSON.parse(atob(parts[1]))
        return true
      } catch (error) {
        console.error('Token formato inválido:', error)
        return false
      }
    },

    isTokenExpired() {
      if (!this.token || !this.isValidToken()) return true
      
      try {
        const payload = JSON.parse(atob(this.token.split('.')[1]))
        const currentTime = Date.now() / 1000
        
        if (!payload.exp) return false
        
        const isExpired = payload.exp < (currentTime + 300)
        
        if (isExpired) {
          const timeLeft = payload.exp - currentTime
          console.log('⏰ Token expirado:', {
            exp: new Date(payload.exp * 1000).toLocaleString(),
            current: new Date().toLocaleString(),
            timeLeft: Math.round(timeLeft) + ' segundos'
          })
        }
        
        return isExpired
      } catch (error) {
        console.error('Error verificando expiración:', error)
        return true
      }
    },

    async forceReauth() {
      console.log('🔄 Forzando re-autenticación...')
      this.initialized = false
      await this.initAuth()
    }
  }
})
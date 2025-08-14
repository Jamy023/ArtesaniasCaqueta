// resources/js/plugins/auth.js
import { useAuthStore } from '@/stores/authStore'

export default {
  install(app) {
    // Hacer métodos de auth disponibles globalmente
    app.config.globalProperties.$auth = {
      // Método para inicializar auth
      async init() {
        const authStore = useAuthStore()
        return await authStore.initAuth()
      },
      
      // Método para forzar re-autenticación
      async refresh() {
        const authStore = useAuthStore()
        return await authStore.forceReauth()
      },
      
      // Método para logout
      async logout() {
        const authStore = useAuthStore()
        return await authStore.logout()
      },
      
      // Getters
      get isLoggedIn() {
        const authStore = useAuthStore()
        return authStore.isLoggedIn
      },
      
      get currentUser() {
        const authStore = useAuthStore()
        return authStore.currentUser
      },
      
      get loading() {
        const authStore = useAuthStore()
        return authStore.loading
      }
    }

    // Método para debug de auth
    app.config.globalProperties.$debugAuth = () => {
      const authStore = useAuthStore()
      console.log('🔍 Debug Auth State:', {
        token: authStore.token ? `${authStore.token.substring(0, 20)}...` : 'null',
        isAuthenticated: authStore.isAuthenticated,
        isLoggedIn: authStore.isLoggedIn,
        initialized: authStore.initialized,
        currentUser: authStore.currentUser,
        hasErrors: authStore.hasErrors,
        errors: authStore.errors
      })
    }
  }
}
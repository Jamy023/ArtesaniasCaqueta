<template>
  <div class="modal-overlay" v-if="isOpen" @click="closeModal">
    <div class="modal-container" @click.stop>
      <div class="modal-header">
        <h2>Iniciar Sesión</h2>
        <button class="close-btn" @click="closeModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      
      <div class="modal-body">
        <form @submit.prevent="login">
          <!-- Mostrar errores generales -->
          <div v-if="displayErrors.general" class="general-error">
            <div v-for="error in displayErrors.general" :key="error" class="error-message">
              {{ error }}
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input 
              id="email"
              v-model="form.email" 
              type="email" 
              placeholder="tu@email.com"
              required
              :class="{ 'error': displayErrors.email }"
              :disabled="authStore.loading || isSubmitting"
            >
            <span v-if="displayErrors.email" class="error-message">
              {{ Array.isArray(displayErrors.email) ? displayErrors.email[0] : displayErrors.email }}
            </span>
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-input-wrapper">
              <input 
                id="password"
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'"
                placeholder="Tu contraseña"
                required
                :class="{ 'error': displayErrors.password }"
                :disabled="authStore.loading || isSubmitting"
              >
              <button 
                type="button" 
                class="password-toggle"
                @click="showPassword = !showPassword"
                :disabled="authStore.loading || isSubmitting"
              >
                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <span v-if="displayErrors.password" class="error-message">
              {{ Array.isArray(displayErrors.password) ? displayErrors.password[0] : displayErrors.password }}
            </span>
          </div>

          <div class="form-actions">
            <button type="submit" class="login-btn" :disabled="authStore.loading || isSubmitting">
              <span v-if="authStore.loading || isSubmitting" class="loading-spinner"></span>
              {{ authStore.loading || isSubmitting ? 'Ingresando...' : 'Iniciar Sesión' }}
            </button>
          </div>

          <div class="form-footer">
            <p>¿No tienes cuenta? 
              <router-link to="/register" @click="closeModal" class="register-link">
                Regístrate aquí
              </router-link>
            </p>
            <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch, computed, nextTick } from 'vue'
import { useAuthStore } from '../../stores/authStore'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

export default {
  name: 'LoginModal',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'login-success'],
  setup(props, { emit }) {
    const authStore = useAuthStore()
    const router = useRouter()
    const toast = useToast()
    
    // Estados del componente
    const form = ref({
      email: '',
      password: ''
    })
    
    const showPassword = ref(false)
    const localErrors = ref({})
    const isSubmitting = ref(false)

    // 🔥 COMPUTED PARA MANEJAR ERRORES DE MÚLTIPLES FUENTES
    const displayErrors = computed(() => {
      // Priorizar errores locales, luego del store
      const storeErrors = (authStore.hasErrors && authStore.errors) ? authStore.errors : {}
      return {
        ...storeErrors,
        ...localErrors.value
      }
    })

    // 🔥 FUNCIÓN PARA LIMPIAR ERRORES - VERSIÓN SEGURA
    const clearAllErrors = () => {
      try {
        localErrors.value = {}
        
        // Verificar que el método existe antes de llamarlo
        if (authStore && typeof authStore.clearErrors === 'function') {
          authStore.clearErrors()
        } else {
          console.warn('⚠️ authStore.clearErrors no está disponible')
          // Fallback: limpiar errores manualmente
          if (authStore.errors) {
            authStore.errors = {}
          }
        }
      } catch (error) {
        console.error('❌ Error limpiando errores:', error)
        localErrors.value = {}
      }
    }

    // 🔥 FUNCIÓN PARA CERRAR MODAL MEJORADA
    const closeModal = () => {
      console.log('🔴 Cerrando modal de login')
      
      emit('close')
      
      // Limpiar formulario y errores después de un pequeño delay
      setTimeout(() => {
        form.value = { email: '', password: '' }
        clearAllErrors()
        showPassword.value = false
        isSubmitting.value = false
      }, 150)
    }

    // 🔥 FUNCIÓN DE LOGIN MEJORADA Y SEGURA
    const login = async () => {
      console.log('🔵 Iniciando proceso de login...')
      
      // Validaciones básicas
      if (!form.value.email || !form.value.password) {
        localErrors.value = {
          general: ['Por favor completa todos los campos']
        }
        return
      }

      // Limpiar errores previos
      clearAllErrors()
      isSubmitting.value = true
      
      try {
        console.log('📤 Enviando credenciales al servidor...')
        
        // Verificar que authStore tiene el método login
        if (!authStore || typeof authStore.login !== 'function') {
          throw new Error('AuthStore no está disponible o no tiene método login')
        }
        
        // Intentar login
        const response = await authStore.login({
          email: form.value.email.trim(),
          password: form.value.password
        })

        console.log('✅ Login exitoso:', {
          user: response.cliente?.nombre,
          email: response.cliente?.email
        })

        // Mostrar notificación de éxito
        const userName = response.cliente?.nombre || 'Usuario'
        toast.success(`¡Bienvenido, ${userName}!`, {
          position: 'top-right',
          timeout: 3000,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true
        })

        // Emitir evento de éxito
        emit('login-success', response)
        
        // Cerrar modal
        closeModal()
        
        console.log('🎉 Proceso de login completado exitosamente')
        
      } catch (error) {
        console.error('❌ Error en login:', error)
        
        // Manejar diferentes tipos de errores
        if (error.response?.status === 422) {
          // Errores de validación
          const serverErrors = error.response.data?.errors || {}
          localErrors.value = serverErrors
          console.log('🔴 Errores de validación:', serverErrors)
          
        } else if (error.response?.status === 401) {
          // Credenciales incorrectas
          localErrors.value = {
            general: ['Credenciales incorrectas. Verifica tu email y contraseña.']
          }
          
        } else if (error.response?.status >= 500) {
          // Error del servidor
          localErrors.value = {
            general: ['Error del servidor. Inténtalo de nuevo más tarde.']
          }
          
        } else {
          // Otros errores
          const errorMessage = error.response?.data?.message || 
                              error.message || 
                              'Error inesperado al iniciar sesión'
          
          localErrors.value = {
            general: [errorMessage]
          }
        }

        // Mostrar notificación de error
        const errorMsg = localErrors.value.general?.[0] || 'Error al iniciar sesión'
        toast.error(errorMsg, {
          position: 'top-right',
          timeout: 4000
        })

      } finally {
        isSubmitting.value = false
      }
    }

    // 🔥 WATCHER PARA EL MODAL
    watch(() => props.isOpen, (newValue) => {
      if (newValue) {
        console.log('🔵 Modal de login abierto')
        clearAllErrors()
        
        // Focus en el campo email después de que se abra
        nextTick(() => {
          const emailInput = document.getElementById('email')
          if (emailInput) {
            emailInput.focus()
          }
        })
      } else {
        console.log('🔴 Modal de login cerrado')
      }
    })

    // 🔥 LIMPIAR ERRORES CUANDO EL USUARIO EMPIECE A ESCRIBIR
    watch(() => form.value.email, () => {
      if (displayErrors.value.email) {
        const { email, ...rest } = localErrors.value
        localErrors.value = rest
      }
    })

    watch(() => form.value.password, () => {
      if (displayErrors.value.password) {
        const { password, ...rest } = localErrors.value
        localErrors.value = rest
      }
    })

    return {
      authStore,
      form,
      showPassword,
      displayErrors,
      isSubmitting,
      closeModal,
      login
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(5px);
}

.modal-container {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 450px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 24px 16px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  color: #1f2937;
  font-size: 24px;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 24px;
}

.general-error {
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  color: #374151;
  font-weight: 500;
  font-size: 14px;
}

.form-group input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  transition: all 0.2s;
  background: white;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #388E3C;
  box-shadow: 0 0 0 3px rgba(56, 142, 60, 0.1);
}

.form-group input.error {
  border-color: #ef4444;
}

.form-group input:disabled {
  background-color: #f9fafb;
  cursor: not-allowed;
  opacity: 0.6;
}

.password-input-wrapper {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.password-toggle:hover:not(:disabled) {
  color: #374151;
}

.password-toggle:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.error-message {
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
  display: block;
}

.form-actions {
  margin-bottom: 24px;
}

.login-btn {
  width: 100%;
  background: linear-gradient(135deg, #388E3C 0%, #2E7D32 100%);
  color: white;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(56, 142, 60, 0.3);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.form-footer {
  text-align: center;
  color: #6b7280;
  font-size: 14px;
}

.form-footer p {
  margin: 0 0 12px 0;
}

.register-link {
  color: #388E3C;
  text-decoration: none;
  font-weight: 500;
}

.register-link:hover {
  text-decoration: underline;
}

.forgot-password {
  color: #6b7280;
  text-decoration: none;
  font-size: 13px;
}

.forgot-password:hover {
  color: #374151;
  text-decoration: underline;
}

@media (max-width: 768px) {
  .modal-container {
    width: 95%;
    margin: 20px;
  }
  
  .modal-header,
  .modal-body {
    padding: 20px;
  }
}
</style>
<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-6">
          <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2">Panel de Administración</h2>
        <p class="text-gray-400">Inicia sesión para acceder al dashboard</p>
      </div>

      <!-- Formulario -->
      <div class="bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700">
        <form @submit.prevent="handleLogin" class="space-y-6">
          
          <!-- Campo Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
              Correo Electrónico
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
              </div>
              <input
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                required
                class="appearance-none relative block w-full px-3 py-3 pl-10 border border-gray-600 placeholder-gray-400 text-white bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                :class="{ 'border-red-500 ring-1 ring-red-500': errors.email }"
                placeholder="admin@tienda.com"
              />
            </div>
            <div v-if="errors.email" class="mt-2 text-sm text-red-400">
              {{ errors.email[0] }}
            </div>
          </div>

          <!-- Campo Contraseña -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
              Contraseña
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                class="appearance-none relative block w-full px-3 py-3 pl-10 pr-10 border border-gray-600 placeholder-gray-400 text-white bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                :class="{ 'border-red-500 ring-1 ring-red-500': errors.password }"
                placeholder="••••••••"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300 transition-colors"
              >
                <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                </svg>
              </button>
            </div>
            <div v-if="errors.password" class="mt-2 text-sm text-red-400">
              {{ errors.password[0] }}
            </div>
          </div>

          <!-- Recordar sesión -->
          <div class="flex items-center">
            <input
              id="remember"
              v-model="form.remember"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-600 bg-gray-700 rounded"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-300">
              Recordar sesión
            </label>
          </div>

          <!-- Error general -->
          <div v-if="errors.general || generalError" 
               class="bg-red-900/20 border border-red-500/30 rounded-lg p-4">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span class="text-red-400 text-sm">
                {{ generalError || (errors.general && errors.general[0]) }}
              </span>
            </div>
          </div>

          <!-- Mensaje de éxito -->
          <div v-if="successMessage" 
               class="bg-green-900/20 border border-green-500/30 rounded-lg p-4">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span class="text-green-400 text-sm">
                {{ successMessage }}
              </span>
            </div>
          </div>

          <!-- Botón de login -->
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-[1.02]"
          >
            <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
          </button>

        </form>

      
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAdminAuthStore } from '../../stores/adminAuthStore'
import { useToast } from 'vue-toastification'

// Composables
const router = useRouter()
const adminAuthStore = useAdminAuthStore()
const toast = useToast()

// Estado reactivo
const loading = ref(false)
const showPassword = ref(false)
const generalError = ref('')
const successMessage = ref('')

// Formulario
const form = reactive({
  email: 'admin@tienda.com', // Valor por defecto para desarrollo
  password: 'admin123456',   // Valor por defecto para desarrollo
  remember: false
})

// Errores de validación
const errors = ref({})

// Computed
const hasErrors = computed(() => Object.keys(errors.value).length > 0)

// Métodos
const clearMessages = () => {
  generalError.value = ''
  successMessage.value = ''
  errors.value = {}
  adminAuthStore.clearErrors()
}

const handleLogin = async () => {
  loading.value = true
  clearMessages()

  try {
    // 🔥 USAR EL STORE PARA MANEJAR EL LOGIN
    const response = await adminAuthStore.login({
      email: form.email,
      password: form.password,
      remember: form.remember
    })

    if (response.success) {
      successMessage.value = response.message || 'Inicio de sesión exitoso'
      toast.success('¡Login exitoso! Redirigiendo...')
      // Redirige inmediatamente al dashboard
      router.push({ name: 'admin-dashboard' })
    }

  } catch (error) {
    console.error('Error en login:', error)
    
    // Mostrar errores del store
    if (adminAuthStore.hasErrors) {
      errors.value = adminAuthStore.errors
    }
    
    // Mostrar error general
    generalError.value = error.response?.data?.message || 'Error al iniciar sesión'
    toast.error(generalError.value)
    
  } finally {
    loading.value = false
  }
}

// Limpiar errores cuando el usuario empiece a escribir
const clearFieldError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
  if (generalError.value) {
    generalError.value = ''
  }
}

// Watchers para limpiar errores
watch(() => form.email, () => clearFieldError('email'))
watch(() => form.password, () => clearFieldError('password'))

// Verificar si ya está autenticado al montar el componente
onMounted(async () => {
  try {
    // Inicializar el store si no está inicializado
    if (!adminAuthStore.initialized) {
      await adminAuthStore.initAuth()
    }
    
    // Si ya está autenticado, redirigir
    if (adminAuthStore.isLoggedIn) {
      console.log('Ya está autenticado, redirigiendo...')
      router.push({ name: 'admin-dashboard' })
    }
  } catch (error) {
    console.log('Usuario no autenticado, mostrando login')
  }
})
</script>

<style scoped>
/* Animaciones adicionales */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.space-y-8 > * + * {
  margin-top: 2rem;
}

.space-y-6 > * + * {
  margin-top: 1.5rem;
}

/* Efectos de hover personalizados */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

/* Transiciones suaves para inputs */
input:focus {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Animación para el botón */
button[type="submit"]:active {
  transform: scale(0.98);
}
</style>
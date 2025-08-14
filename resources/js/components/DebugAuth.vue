<!-- DebugAuth.vue - Coloca este componente temporalmente en tu navbar o app -->
<template>
  <div class="debug-auth" v-if="showDebug">
    <button @click="toggleDebug" class="debug-toggle">
      {{ isOpen ? '📊 Cerrar Debug' : '🔍 Debug Auth' }}
    </button>
    
    <div v-if="isOpen" class="debug-panel">
      <h3>🔍 Estado de Autenticación</h3>
      
      <div class="debug-section">
        <h4>📱 Cliente</h4>
        <div class="debug-item">
          <span class="label">isLoggedIn:</span>
          <span :class="['value', authStore.isLoggedIn ? 'success' : 'error']">
            {{ authStore.isLoggedIn }}
          </span>
        </div>
        <div class="debug-item">
          <span class="label">isAuthenticated:</span>
          <span :class="['value', authStore.isAuthenticated ? 'success' : 'error']">
            {{ authStore.isAuthenticated }}
          </span>
        </div>
        <div class="debug-item">
          <span class="label">hasToken:</span>
          <span :class="['value', !!authStore.token ? 'success' : 'error']">
            {{ !!authStore.token }}
          </span>
        </div>
        <div class="debug-item">
          <span class="label">initialized:</span>
          <span :class="['value', authStore.initialized ? 'success' : 'error']">
            {{ authStore.initialized }}
          </span>
        </div>
        <div class="debug-item">
          <span class="label">loading:</span>
          <span :class="['value', authStore.loading ? 'warning' : 'success']">
            {{ authStore.loading }}
          </span>
        </div>
        <div class="debug-item">
          <span class="label">usuario:</span>
          <span class="value">{{ authStore.currentUser?.nombre || 'N/A' }}</span>
        </div>
      </div>

      <div class="debug-section">
        <h4>💾 LocalStorage</h4>
        <div class="debug-item">
          <span class="label">auth_token:</span>
          <span class="value token">{{ tokenDisplay }}</span>
        </div>
      </div>

      <div class="debug-actions">
        <button @click="refreshAuth" class="debug-btn">🔄 Refrescar Auth</button>
        <button @click="clearAllData" class="debug-btn danger">🗑️ Limpiar Todo</button>
        <button @click="testProfile" class="debug-btn">📋 Test Profile</button>
      </div>

      <div class="debug-log">
        <h4>📝 Log Reciente</h4>
        <div class="log-entries">
          <div v-for="(log, index) in recentLogs" :key="index" class="log-entry">
            <span class="log-time">{{ log.time }}</span>
            <span :class="['log-message', log.type]">{{ log.message }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../stores/authStore'

export default {
  name: 'DebugAuth',
  setup() {
    const authStore = useAuthStore()
    const isOpen = ref(false)
    const showDebug = ref(process.env.NODE_ENV === 'development') // Solo en desarrollo
    const recentLogs = ref([])
    
    const tokenDisplay = computed(() => {
      const token = localStorage.getItem('auth_token')
      return token ? `${token.substring(0, 15)}...` : 'NULL'
    })

    const toggleDebug = () => {
      isOpen.value = !isOpen.value
    }

    const addLog = (message, type = 'info') => {
      const now = new Date()
      const time = now.toLocaleTimeString()
      recentLogs.value.unshift({
        time,
        message,
        type
      })
      // Mantener solo los últimos 10 logs
      if (recentLogs.value.length > 10) {
        recentLogs.value = recentLogs.value.slice(0, 10)
      }
    }

    const refreshAuth = async () => {
      addLog('🔄 Refrescando autenticación...', 'info')
      try {
        await authStore.forceReauth()
        addLog('✅ Autenticación refrescada', 'success')
      } catch (error) {
        addLog(`❌ Error refrescando: ${error.message}`, 'error')
      }
    }

    const clearAllData = () => {
      addLog('🗑️ Limpiando todos los datos...', 'warning')
      authStore.clearAuthData()
      addLog('✅ Datos limpiados', 'success')
    }

    const testProfile = async () => {
      addLog('📋 Probando obtener perfil...', 'info')
      try {
        await authStore.getProfile()
        addLog('✅ Perfil obtenido correctamente', 'success')
      } catch (error) {
        addLog(`❌ Error obteniendo perfil: ${error.message}`, 'error')
      }
    }

    // Interceptar console.log para capturar logs del authStore
    const originalConsoleLog = console.log
    const originalConsoleError = console.error

    onMounted(() => {
      console.log = (...args) => {
        const message = args.join(' ')
        if (message.includes('Auth') || message.includes('🔥') || message.includes('✅') || message.includes('❌')) {
          addLog(message, 'info')
        }
        originalConsoleLog.apply(console, args)
      }

      console.error = (...args) => {
        const message = args.join(' ')
        if (message.includes('Auth') || message.includes('❌')) {
          addLog(message, 'error')
        }
        originalConsoleError.apply(console, args)
      }
    })

    onUnmounted(() => {
      console.log = originalConsoleLog
      console.error = originalConsoleError
    })

    return {
      authStore,
      isOpen,
      showDebug,
      tokenDisplay,
      recentLogs,
      toggleDebug,
      refreshAuth,
      clearAllData,
      testProfile
    }
  }
}
</script>

<style scoped>
.debug-auth {
  position: fixed;
  top: 100px;
  right: 20px;
  z-index: 10000;
  font-family: 'Courier New', monospace;
}

.debug-toggle {
  background: #1f2937;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.debug-panel {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  margin-top: 8px;
  max-width: 400px;
  max-height: 500px;
  overflow-y: auto;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.debug-section {
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f3f4f6;
}

.debug-section h3,
.debug-section h4 {
  margin: 0 0 8px 0;
  font-size: 14px;
  color: #374151;
}

.debug-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
  font-size: 12px;
}

.label {
  font-weight: 600;
  color: #6b7280;
}

.value {
  font-weight: 500;
}

.value.success {
  color: #059669;
}

.value.error {
  color: #dc2626;
}

.value.warning {
  color: #d97706;
}

.value.token {
  font-family: monospace;
  font-size: 10px;
  background: #f3f4f6;
  padding: 2px 6px;
  border-radius: 4px;
}

.debug-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.debug-btn {
  font-size: 10px;
  padding: 6px 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  background: #3b82f6;
  color: white;
}

.debug-btn.danger {
  background: #dc2626;
}

.debug-btn:hover {
  opacity: 0.8;
}

.debug-log {
  max-height: 200px;
  overflow-y: auto;
}

.log-entries {
  font-size: 10px;
  background: #f9fafb;
  border-radius: 4px;
  padding: 8px;
}

.log-entry {
  display: flex;
  gap: 8px;
  margin-bottom: 4px;
  padding: 2px 0;
}

.log-time {
  color: #6b7280;
  min-width: 60px;
}

.log-message {
  flex: 1;
}

.log-message.success {
  color: #059669;
}

.log-message.error {
  color: #dc2626;
}

.log-message.warning {
  color: #d97706;
}
</style>
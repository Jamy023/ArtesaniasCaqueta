<template>
  <div class="test-container">
    <div class="test-wrapper">
      <h1>🔧 Prueba de Integración ePayco</h1>
      <p>Esta página te permite probar la integración de ePayco paso a paso</p>

      <!-- Estado del SDK -->
      <div class="test-section">
        <h3>1. Estado del SDK</h3>
        <div class="status-item">
          <span>ePayco SDK:</span>
          <span :class="sdkLoaded ? 'status-ok' : 'status-error'">
            {{ sdkLoaded ? '✅ Cargado' : '❌ No disponible' }}
          </span>
        </div>
        <div class="status-item">
          <span>Public Key:</span>
          <span :class="hasPublicKey ? 'status-ok' : 'status-error'">
            {{ hasPublicKey ? '✅ Configurada' : '❌ No configurada' }}
          </span>
        </div>
        <div class="status-item">
          <span>Customer ID:</span>
          <span :class="hasCustomerId ? 'status-ok' : 'status-error'">
            {{ customerId }}
          </span>
        </div>
      </div>

      <!-- Datos de prueba -->
      <div class="test-section">
        <h3>2. Datos de Prueba</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Monto (COP):</label>
            <input v-model="testData.amount" type="number" min="1000" step="1000" />
          </div>
          <div class="form-group">
            <label>Descripción:</label>
            <input v-model="testData.description" type="text" />
          </div>
          <div class="form-group">
            <label>Email:</label>
            <input v-model="testData.email" type="email" />
          </div>
          <div class="form-group">
            <label>Nombre:</label>
            <input v-model="testData.name" type="text" />
          </div>
        </div>
      </div>

      <!-- Botones de prueba -->
      <div class="test-section">
        <h3>3. Pruebas</h3>
        <div class="test-buttons">
          <button @click="testSDK" class="btn-test" :disabled="!sdkLoaded">
            Probar SDK
          </button>
          <button @click="testCheckout" class="btn-test" :disabled="!canTest">
            Abrir Checkout
          </button>
          <button @click="testAPI" class="btn-test">
            Probar API Backend
          </button>
        </div>
      </div>

      <!-- Logs -->
      <div class="test-section">
        <h3>4. Logs</h3>
        <div class="logs-container">
          <div v-for="(log, index) in logs" :key="index" class="log-item" :class="log.type">
            <span class="log-time">{{ log.time }}</span>
            <span class="log-message">{{ log.message }}</span>
          </div>
        </div>
        <button @click="clearLogs" class="btn-clear">Limpiar Logs</button>
      </div>

      <!-- Links útiles -->
      <div class="test-section">
        <h3>5. Enlaces Útiles</h3>
        <div class="links">
          <a href="https://docs.epayco.co/" target="_blank" class="link-item">
            📖 Documentación ePayco
          </a>
          <a href="https://dashboard.epayco.co/" target="_blank" class="link-item">
            🏢 Dashboard ePayco
          </a>
          <a href="/checkout" class="link-item">
            🛒 Ir al Checkout Real
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'

// Estado
const logs = ref([])
const testData = ref({
  amount: 10000,
  description: 'Prueba de pago',
  email: 'test@example.com',
  name: 'Usuario de Prueba'
})

// Computadas
const sdkLoaded = computed(() => typeof window.ePayco !== 'undefined')
const hasPublicKey = computed(() => window.epaycoConfig?.public_key && window.epaycoConfig.public_key.length > 0)
const customerId = computed(() => window.epaycoConfig?.customer_id || 'No configurado')
const hasCustomerId = computed(() => customerId.value !== 'No configurado')
const canTest = computed(() => sdkLoaded.value && hasPublicKey.value && hasCustomerId.value)

// Funciones
const addLog = (message, type = 'info') => {
  logs.value.unshift({
    time: new Date().toLocaleTimeString(),
    message,
    type
  })
}

const clearLogs = () => {
  logs.value = []
}

const testSDK = () => {
  addLog('🔧 Probando SDK de ePayco...', 'info')
  
  try {
    if (typeof window.ePayco === 'undefined') {
      addLog('❌ SDK no está disponible', 'error')
      return
    }

    addLog('✅ SDK disponible', 'success')
    addLog(`📊 Configuración: test=${window.epaycoConfig.test}, customer_id=${customerId.value}`, 'info')

    // Probar configuración
    const handler = window.ePayco.checkout.configure({
      key: window.epaycoConfig.public_key,
      test: window.epaycoConfig.test
    })

    addLog('✅ Configuración exitosa', 'success')

  } catch (error) {
    addLog(`❌ Error en SDK: ${error.message}`, 'error')
  }
}

const testCheckout = () => {
  addLog('💳 Iniciando checkout de prueba...', 'info')

  try {
    const handler = window.ePayco.checkout.configure({
      key: window.epaycoConfig.public_key,
      test: window.epaycoConfig.test
    })

    const data = {
      name: testData.value.description,
      description: testData.value.description,
      invoice: 'TEST-' + Date.now(),
      currency: 'COP',
      amount: testData.value.amount.toString(),
      amount_base: testData.value.amount.toString(),
      
      name_billing: testData.value.name,
      email_billing: testData.value.email,
      phone_billing: '3001234567',
      address_billing: 'Calle Test 123',
      city_billing: 'Bogotá',
      country_billing: 'CO',
      
      response: window.location.origin + '/order-confirmation',
      confirmation: window.location.origin + '/api/orders/epayco-confirmation',
      
      method_confirmation: 'POST',
      external: false,
      autoclick: false
    }

    addLog(`📋 Datos: ${JSON.stringify(data, null, 2)}`, 'info')
    addLog('🚀 Abriendo checkout...', 'info')

    handler.open(data)

  } catch (error) {
    addLog(`❌ Error en checkout: ${error.message}`, 'error')
  }
}

const testAPI = async () => {
  addLog('🔌 Probando API backend...', 'info')

  try {
    // Datos de prueba para crear orden
    const orderData = {
      items: [{
        id: 1,
        name: testData.value.description,
        price: testData.value.amount,
        quantity: 1,
        image: 'test.jpg'
      }],
      total_amount: testData.value.amount
    }

    addLog('📤 Enviando datos al backend...', 'info')
    
    const response = await api.post('/orders/create', orderData)
    
    if (response.data.success) {
      addLog('✅ Orden creada exitosamente', 'success')
      addLog(`📋 Datos ePayco recibidos: ${Object.keys(response.data.epayco_data).join(', ')}`, 'success')
    } else {
      addLog(`❌ Error en respuesta: ${response.data.message}`, 'error')
    }

  } catch (error) {
    if (error.response?.status === 401) {
      addLog('❌ Error: Usuario no autenticado. Ve a /register primero.', 'error')
    } else {
      addLog(`❌ Error en API: ${error.response?.data?.message || error.message}`, 'error')
    }
  }
}

onMounted(() => {
  addLog('🚀 Página de pruebas cargada', 'info')
  
  // Verificar configuración
  if (window.epaycoConfig) {
    addLog(`⚙️ Configuración detectada: test=${window.epaycoConfig.test}`, 'info')
  } else {
    addLog('❌ Configuración no encontrada', 'error')
  }
})
</script>

<style scoped>
.test-container {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 2rem;
}

.test-wrapper {
  max-width: 1000px;
  margin: 0 auto;
  background: white;
  border-radius: 1rem;
  padding: 2rem;
}

.test-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f9f9f9;
  border-radius: 0.5rem;
  border-left: 4px solid #4CAF50;
}

.test-section h3 {
  margin-bottom: 1rem;
  color: #2E7D32;
}

.status-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  padding: 0.5rem;
  background: white;
  border-radius: 0.25rem;
}

.status-ok {
  color: #4CAF50;
  font-weight: 600;
}

.status-error {
  color: #f44336;
  font-weight: 600;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #5D4037;
}

.form-group input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.test-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn-test {
  background: linear-gradient(135deg, #2E7D32, #4CAF50);
  color: white;
  border: none;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-test:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
}

.btn-test:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.logs-container {
  max-height: 300px;
  overflow-y: auto;
  background: #1a1a1a;
  color: white;
  padding: 1rem;
  border-radius: 0.5rem;
  font-family: monospace;
  font-size: 0.9rem;
}

.log-item {
  display: flex;
  margin-bottom: 0.5rem;
  padding: 0.25rem 0;
}

.log-time {
  color: #888;
  margin-right: 1rem;
  min-width: 100px;
}

.log-item.info .log-message {
  color: #61dafb;
}

.log-item.success .log-message {
  color: #4CAF50;
}

.log-item.error .log-message {
  color: #f44336;
}

.btn-clear {
  background: #f44336;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  cursor: pointer;
  margin-top: 1rem;
}

.links {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.link-item {
  background: #E8F5E8;
  color: #2E7D32;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.link-item:hover {
  background: #C8E6C9;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .test-container {
    padding: 1rem;
  }
  
  .test-wrapper {
    padding: 1rem;
  }
  
  .test-buttons {
    flex-direction: column;
  }
  
  .links {
    flex-direction: column;
  }
}
</style>
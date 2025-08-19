<template>
  <div class="confirmation-container">
    <div class="confirmation-wrapper">
      <!-- Estado de carga -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <h3>Verificando tu pago...</h3>
        <p>Por favor espera mientras confirmamos tu transacción</p>
      </div>

      <!-- Pago exitoso -->
      <div v-else-if="status === 'success'" class="success-state">
        <div class="success-icon">✅</div>
        <h1>¡Pago Exitoso!</h1>
        <p class="success-message">Tu pedido ha sido procesado correctamente</p>
        
        <div class="order-details">
          <div class="detail-item">
            <span class="label">Número de pedido:</span>
            <span class="value">{{ orderNumber }}</span>
          </div>
          <div class="detail-item" v-if="transactionId">
            <span class="label">ID de transacción:</span>
            <span class="value">{{ transactionId }}</span>
          </div>
          <div class="detail-item" v-if="amount">
            <span class="label">Monto pagado:</span>
            <span class="value">{{ formatPrice(amount) }}</span>
          </div>
        </div>

        <div class="success-actions">
          <router-link to="/account" class="btn-primary">
            Ver mis pedidos
          </router-link>
          <router-link to="/products" class="btn-secondary">
            Seguir comprando
          </router-link>
        </div>
      </div>

      <!-- Pago pendiente -->
      <div v-else-if="status === 'pending'" class="pending-state">
        <div class="pending-icon">⏳</div>
        <h1>Pago Pendiente</h1>
        <p class="pending-message">Tu pago está siendo procesado</p>
        
        <div class="pending-info">
          <p>Recibirás un email de confirmación una vez que se procese el pago.</p>
          <p><strong>Número de pedido:</strong> {{ orderNumber }}</p>
        </div>

        <div class="pending-actions">
          <router-link to="/account" class="btn-primary">
            Ver estado del pedido
          </router-link>
          <router-link to="/" class="btn-secondary">
            Ir al inicio
          </router-link>
        </div>
      </div>

      <!-- Pago fallido -->
      <div v-else-if="status === 'failed'" class="failed-state">
        <div class="failed-icon">❌</div>
        <h1>Pago No Procesado</h1>
        <p class="failed-message">Hubo un problema con tu pago</p>
        
        <div class="failed-info">
          <p>No te preocupes, no se realizó ningún cargo a tu tarjeta.</p>
          <p v-if="orderNumber"><strong>Número de pedido:</strong> {{ orderNumber }}</p>
        </div>

        <div class="failed-actions">
          <router-link to="/checkout" class="btn-primary">
            Intentar nuevamente
          </router-link>
          <router-link to="/products" class="btn-secondary">
            Seguir comprando
          </router-link>
        </div>
      </div>

      <!-- Error desconocido -->
      <div v-else class="error-state">
        <div class="error-icon">⚠️</div>
        <h1>Error Desconocido</h1>
        <p>Ocurrió un error inesperado. Contacta con soporte.</p>
        
        <div class="error-actions">
          <router-link to="/" class="btn-primary">
            Ir al inicio
          </router-link>
          <a href="https://wa.me/573105867601" class="btn-secondary" target="_blank">
            Contactar soporte
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

// Estado
const loading = ref(true)
const status = ref(null)
const orderNumber = ref('')
const transactionId = ref('')
const amount = ref(0)

// Determinar estado del pago
onMounted(() => {
  const { success, error, pending, order, transaction_id, amount: paymentAmount } = route.query
  
  orderNumber.value = order || 'N/A'
  transactionId.value = transaction_id || ''
  amount.value = parseFloat(paymentAmount || '0')

  setTimeout(() => {
    if (success === 'true') {
      status.value = 'success'
    } else if (pending === 'true') {
      status.value = 'pending'
    } else if (error || error === 'rejected') {
      status.value = 'failed'
    } else {
      status.value = 'unknown'
    }
    
    loading.value = false
  }, 2000) // Simular verificación
})

// Funciones auxiliares
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { 
    style: 'currency', 
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(price)
}
</script>

<style scoped>
.confirmation-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #F1F8E9 0%, #E8F5E8 100%);
  padding: 2rem;
}

.confirmation-wrapper {
  max-width: 600px;
  width: 100%;
  background: white;
  border-radius: 1.5rem;
  padding: 3rem 2rem;
  text-align: center;
  box-shadow: 0 20px 40px rgba(93, 64, 55, 0.1);
}

.loading-state .spinner {
  width: 60px;
  height: 60px;
  border: 4px solid #F1F8E9;
  border-top: 4px solid #4CAF50;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 2rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.success-icon, .pending-icon, .failed-icon, .error-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
}

.success-state h1 {
  color: #4CAF50;
  margin-bottom: 1rem;
}

.pending-state h1 {
  color: #FF9800;
  margin-bottom: 1rem;
}

.failed-state h1 {
  color: #f44336;
  margin-bottom: 1rem;
}

.error-state h1 {
  color: #5D4037;
  margin-bottom: 1rem;
}

.success-message, .pending-message, .failed-message {
  font-size: 1.1rem;
  margin-bottom: 2rem;
  color: #8D6E63;
}

.order-details, .pending-info, .failed-info {
  background: #F1F8E9;
  border-radius: 1rem;
  padding: 2rem;
  margin: 2rem 0;
  text-align: left;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #E8F5E8;
}

.detail-item:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.label {
  font-weight: 600;
  color: #5D4037;
}

.value {
  font-weight: 700;
  color: #2E7D32;
}

.success-actions, .pending-actions, .failed-actions, .error-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 2rem;
}

.btn-primary {
  background: linear-gradient(135deg, #2E7D32, #4CAF50);
  color: white;
  padding: 1rem 2rem;
  border-radius: 0.75rem;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  flex: 1;
  max-width: 200px;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(46, 125, 50, 0.3);
}

.btn-secondary {
  background: transparent;
  color: #2E7D32;
  border: 2px solid #2E7D32;
  padding: 1rem 2rem;
  border-radius: 0.75rem;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  flex: 1;
  max-width: 200px;
}

.btn-secondary:hover {
  background: #2E7D32;
  color: white;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .confirmation-container {
    padding: 1rem;
  }
  
  .confirmation-wrapper {
    padding: 2rem 1.5rem;
  }
  
  .success-actions, .pending-actions, .failed-actions, .error-actions {
    flex-direction: column;
  }
  
  .btn-primary, .btn-secondary {
    max-width: none;
  }
}
</style>
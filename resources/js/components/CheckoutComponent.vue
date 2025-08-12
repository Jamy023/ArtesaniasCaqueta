<template>
  <div class="checkout-container">
    <!-- Loading State -->
    <div v-if="processing" class="loading-overlay">
      <div class="loading-content">
        <div class="spinner"></div>
        <p>Preparando tu pedido...</p>
      </div>
    </div>

    <div class="checkout-wrapper">
      <div class="checkout-header">
        <h1>Finalizar Compra</h1>
        <p class="checkout-subtitle">Revisa tu pedido antes de proceder al pago</p>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="error-message">
        <div class="error-icon">⚠️</div>
        <p>{{ error }}</p>
        <button @click="error = null" class="close-error">×</button>
      </div>

      <div class="checkout-content">
        <!-- Resumen del Pedido -->
        <div class="order-summary">
          <h2>Resumen del Pedido</h2>
          
          <div class="cart-items">
            <div v-for="item in cartItems" :key="item.id" class="cart-item">
              <img 
                :src="getProductImageUrl(item.main_image)" 
                :alt="item.name"
                class="item-image"
                @error="handleImageError"
              />
              <div class="item-details">
                <h4 class="item-name">{{ item.name }}</h4>
                <p class="item-price">{{ formatPrice(item.price) }}</p>
                <p class="item-quantity">Cantidad: {{ item.quantity }}</p>
                <p class="item-subtotal">Subtotal: {{ formatPrice(item.price * item.quantity) }}</p>
              </div>
            </div>
          </div>

          <div class="order-totals">
            <div class="total-row">
              <span>Subtotal:</span>
              <span>{{ formatPrice(subtotal) }}</span>
            </div>
            <div class="total-row">
              <span>Envío:</span>
              <span>Gratis</span>
            </div>
            <div class="total-row total-final">
              <span>Total:</span>
              <span>{{ formatPrice(total) }}</span>
            </div>
          </div>
        </div>

        <!-- Datos del Cliente -->
        <div class="customer-info">
          <h2>Datos de Envío</h2>
          
          <div class="customer-card">
            <div class="customer-header">
              <div class="customer-avatar">
                {{ customerData.nombre.charAt(0) }}{{ customerData.apellido.charAt(0) }}
              </div>
              <div class="customer-details">
                <h3>{{ customerData.nombre }} {{ customerData.apellido }}</h3>
                <p>{{ customerData.email }}</p>
              </div>
            </div>
            
            <div class="customer-data">
              <div class="data-row">
                <span class="data-label">Documento:</span>
                <span>{{ customerData.tipo_documento }} {{ customerData.numero_documento }}</span>
              </div>
              <div class="data-row">
                <span class="data-label">Teléfono:</span>
                <span>{{ customerData.telefono }}</span>
              </div>
              <div class="data-row">
                <span class="data-label">Dirección:</span>
                <span>{{ customerData.direccion }}</span>
              </div>
              <div class="data-row">
                <span class="data-label">Ciudad:</span>
                <span>{{ customerData.ciudad }}, {{ customerData.departamento }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Información de Pago -->
        <div class="payment-info">
          <h2>Método de Pago</h2>
          
          <div class="payment-methods">
            <div class="payment-method active">
              <div class="method-icon">
                <img src="https://multimedia.epayco.co/dashboard/images/logo.png" alt="ePayco" class="epayco-logo" />
              </div>
              <div class="method-details">
                <h4>Pago Seguro con ePayco</h4>
                <p>Tarjetas de crédito, débito, PSE, Efecty y más</p>
                <div class="payment-badges">
                  <span class="badge">Visa</span>
                  <span class="badge">Mastercard</span>
                  <span class="badge">PSE</span>
                  <span class="badge">Efecty</span>
                </div>
              </div>
            </div>
          </div>

          <div class="security-info">
            <div class="security-item">
              <span class="security-icon">🔒</span>
              <span>Conexión segura SSL</span>
            </div>
            <div class="security-item">
              <span class="security-icon">🛡️</span>
              <span>Datos protegidos</span>
            </div>
            <div class="security-item">
              <span class="security-icon">✅</span>
              <span>Transacciones verificadas</span>
            </div>
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="checkout-actions">
          <button @click="goBack" class="btn-secondary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Continuar Comprando
          </button>
          
          <button 
            @click="processCheckout" 
            :disabled="processing || cartItems.length === 0"
            class="btn-primary"
          >
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            {{ processing ? 'Procesando...' : 'Pagar con ePayco' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ePayco Form (Hidden) -->
    <form ref="epaycoForm" :action="epaycoUrl" method="POST" style="display: none;">
      <input v-for="(value, key) in epaycoData" :key="key" :name="key" :value="value" type="hidden" />
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cartStore'
import { useAuthStore } from '../stores/authStore'
import api from '../axios'

const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()

// State
const processing = ref(false)
const error = ref(null)
const epaycoData = ref({})
const epaycoUrl = 'https://checkout.epayco.co/checkout.php'

// Computed
const cartItems = computed(() => cartStore.items)
const subtotal = computed(() => cartStore.totalPrice)
const total = computed(() => subtotal.value) // Por ahora sin envío
const customerData = computed(() => authStore.user || {})

// Methods
const processCheckout = async () => {
  if (cartItems.value.length === 0) {
    error.value = 'Tu carrito está vacío'
    return
  }

  if (!customerData.value.id) {
    error.value = 'Debes iniciar sesión para continuar'
    return
  }

  processing.value = true
  error.value = null

  try {
    // Preparar datos del pedido
    const orderData = {
      items: cartItems.value.map(item => ({
        id: item.id,
        name: item.name,
        price: item.price,
        quantity: item.quantity,
        image: item.main_image
      })),
      total_amount: total.value
    }

    // Crear pedido y obtener datos de ePayco
    const response = await api.post('/orders/create', orderData)
    
    if (response.data.success) {
      // Guardar datos de ePayco
      epaycoData.value = response.data.epayco_data
      
      // Limpiar carrito
      cartStore.clearCart()
      
      // Enviar formulario a ePayco
      setTimeout(() => {
        submitToEpayco()
      }, 1000)
      
    } else {
      throw new Error(response.data.message || 'Error al crear el pedido')
    }

  } catch (err) {
    console.error('Checkout error:', err)
    error.value = err.response?.data?.message || 'Error al procesar el pedido'
    processing.value = false
  }
}

const submitToEpayco = () => {
  // Submit automático del formulario hacia ePayco
  if (Object.keys(epaycoData.value).length > 0) {
    const form = document.querySelector('form')
    if (form) {
      form.submit()
    }
  }
}

const goBack = () => {
  router.push('/products')
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { 
    style: 'currency', 
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(price)
}

const getProductImageUrl = (image) => {
  if (!image) return '/img/logo.png'
  if (image.startsWith('http')) return image
  if (image.startsWith('public/')) return '/storage/' + image.replace('public/', '')
  if (image.startsWith('storage/app/public/')) return '/storage/' + image.replace('storage/app/public/', '')
  if (image.startsWith('products/')) return '/storage/' + image
  return image
}

const handleImageError = (event) => {
  event.target.src = '/img/logo.png'
}

// Lifecycle
onMounted(() => {
  // Verificar si hay items en el carrito
  if (cartItems.value.length === 0) {
    router.push('/products')
    return
  }

  // Verificar si el usuario está autenticado
  if (!customerData.value.id) {
    router.push('/login')
    return
  }
})
</script>

<style scoped>
.checkout-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 2rem 1rem;
  position: relative;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #2E7D32;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.checkout-wrapper {
  max-width: 1200px;
  margin: 0 auto;
}

.checkout-header {
  text-align: center;
  margin-bottom: 2rem;
}

.checkout-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #5D4037;
  margin-bottom: 0.5rem;
}

.checkout-subtitle {
  color: #666;
  font-size: 1.1rem;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  background: #ffebee;
  border: 1px solid #ffcdd2;
  border-radius: 0.75rem;
  color: #c62828;
  margin-bottom: 2rem;
}

.error-icon {
  font-size: 1.5rem;
}

.close-error {
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #c62828;
    cursor: pointer;
    transition: color 0.2s;
    &:hover {
        color: #b71c1c;
        }
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cart-item {
  display: flex;
  gap: 1rem;
  background: #fff;
  border-radius: 0.75rem;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 0.5rem;
}

.item-details {
  flex: 1;
}

.item-name {
  font-weight: 600;
  color: #333;
}

.item-price,
.item-quantity,
.item-subtotal {
  font-size: 0.9rem;
  color: #666;
}

.order-totals {
  margin-top: 1.5rem;
  border-top: 1px solid #ddd;
  padding-top: 1rem;
}

.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.total-final {
  font-weight: 700;
  font-size: 1.2rem;
  color: #2E7D32;
}

.customer-info,
.payment-info {
  background: #fff;
  border-radius: 0.75rem;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.customer-card {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.customer-header {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.customer-avatar {
  width: 50px;
  height: 50px;
  background: #2E7D32;
  color: #fff;
  font-weight: bold;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.customer-details h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
}

.customer-data {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.data-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
}

.data-label {
  font-weight: 600;
  color: #555;
}

.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.payment-method {
  display: flex;
  gap: 1rem;
  border: 1px solid #ddd;
  border-radius: 0.75rem;
  padding: 1rem;
  align-items: center;
}

.payment-method.active {
  border-color: #2E7D32;
  background: #f1f8f4;
}

.method-icon img {
  height: 40px;
}

.payment-badges {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.badge {
  background: #eee;
  padding: 0.2rem 0.5rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
}

.security-info {
  margin-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.security-item {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.9rem;
}

.checkout-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 2rem;
  gap: 1rem;
}

.btn-primary {
  background: #2E7D32;
  color: #fff;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 0.75rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.btn-primary:disabled {
  background: #9e9e9e;
  cursor: not-allowed;
}

.btn-secondary {
  background: #fff;
  color: #2E7D32;
  padding: 0.8rem 1.5rem;
  border: 1px solid #2E7D32;
  border-radius: 0.75rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.btn-icon {
  width: 20px;
  height: 20px;
}
</style >
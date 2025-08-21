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
          <h3>Resumen del Pedido</h3>
          
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
                <div class="item-info">
                  <span class="item-price">{{ formatPrice(item.price) }}</span>
                  <span class="item-quantity">× {{ item.quantity }}</span>
                  <span class="item-subtotal">{{ formatPrice(item.price * item.quantity) }}</span>
                </div>
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
          <h3>Datos de Envío</h3>
          
          <div class="customer-card">
            <div class="customer-header">
              <div class="customer-avatar">
                {{ customerData.nombre?.charAt(0) || '' }}{{ customerData.apellido?.charAt(0) || '' }}
              </div>
              <div class="customer-details">
                <h4>{{ customerData.nombre || '' }} {{ customerData.apellido || '' }}</h4>
                <p>{{ customerData.email || '' }}</p>
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
          <h3>Método de Pago</h3>
          
          <div class="payment-methods">
            <div class="payment-method active">
              <div class="method-header">
                <img src="https://multimedia.epayco.co/dashboard/images/logo.png" alt="ePayco" class="epayco-logo" />
                <div class="method-text">
                  <h4>Pago Seguro con ePayco</h4>
                  <p>Múltiples opciones disponibles</p>
                </div>
              </div>
              
              <div class="payment-badges">
                <img src="/resources/img/PagoMedios/visa-10.svg" alt="Visa" class="payment-icon" />
                <img src="/img/PagoMedios/mastercard-6.svg" alt="Mastercard" class="payment-icon" />
                <div class="payment-icon pse-icon">PSE</div>
                <img src="/img/PagoMedios/efecty.svg" alt="Efecty" class="payment-icon" />
                <img src="/img/PagoMedios/nequi-2.svg" alt="Nequi" class="payment-icon" />
                <img src="/img/PagoMedios/daviplata.svg" alt="Daviplata" class="payment-icon" />
              </div>
            </div>
          </div>

          <div class="security-info">
            <span class="security-text">🔒 Transacción segura SSL</span>
            <span class="security-text">🛡️ Datos protegidos</span>
            <span class="security-text">✓ Garantizado</span>
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="checkout-actions">
          <button @click="goBack" class="btn-secondary">
            ← Continuar Comprando
          </button>
          
          <button 
            @click="processCheckout" 
            :disabled="processing || cartItems.length === 0"
            class="btn-primary"
          >
            {{ processing ? 'Procesando...' : 'Pagar Ahora' }}
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
const customerData = computed(() => authStore.cliente || {})

// Methods
const processCheckout = async () => {
  if (cartItems.value.length === 0) {
    error.value = 'Tu carrito está vacío'
    return
  }

  if (!authStore.isLoggedIn) {
    error.value = 'Debes iniciar sesión para continuar'
    router.push('/register?redirect=/checkout')
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
  if (Object.keys(epaycoData.value).length === 0) {
    console.error('❌ Datos de ePayco incompletos')
    error.value = 'Error: datos de pago incompletos'
    processing.value = false
    return
  }

  console.log('🚀 Iniciando Checkout Onpage con ePayco:', epaycoData.value)
  
  try {
    // Verificar que el SDK esté disponible
    if (typeof window.ePayco === 'undefined') {
      throw new Error('SDK de ePayco no está disponible')
    }

    // Configurar ePayco
    const handler = window.ePayco.checkout.configure({
      key: window.epaycoConfig.public_key,
      test: window.epaycoConfig.test
    })

    // Preparar datos para Checkout Onpage
    const checkoutData = {
      // Datos básicos del pago
      name: epaycoData.value.p_description,
      description: epaycoData.value.p_description,
      invoice: epaycoData.value.p_id_invoice,
      currency: epaycoData.value.p_currency_code,
      amount: epaycoData.value.p_amount,
      amount_base: epaycoData.value.p_amount_base,
      
      // Datos del cliente (requeridos)
      name_billing: epaycoData.value.p_billing_customer,
      email_billing: epaycoData.value.p_customer_email,
      phone_billing: epaycoData.value.p_customer_phone || '3001234567',
      address_billing: epaycoData.value.p_customer_address,
      city_billing: epaycoData.value.p_customer_city,
      country_billing: epaycoData.value.p_customer_country,
      
      // URLs de respuesta
      response: epaycoData.value.p_url_response,
      confirmation: epaycoData.value.p_url_confirmation,
      
      // Configuración adicional
      method_confirmation: 'POST',
      external: false, // Para usar modal onpage
      autoclick: false
    }

    console.log('📋 Datos para Checkout Onpage:', checkoutData)
    
    // Abrir Checkout Onpage
    handler.open(checkoutData)
    console.log('✅ Checkout Onpage abierto exitosamente')
    
  } catch (error) {
    console.error('❌ Error en Checkout Onpage:', error)
    
    // FALLBACK: usar formulario POST si el SDK falla
    console.log('🔄 Usando fallback POST form')
    setTimeout(() => {
      const form = document.createElement('form')
      form.method = 'POST'
      form.action = 'https://checkout.epayco.co/checkout.php'
      form.target = '_self'
      
      for (const [key, value] of Object.entries(epaycoData.value)) {
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = key
        input.value = value
        form.appendChild(input)
      }
      
      document.body.appendChild(form)
      form.submit()
      document.body.removeChild(form)
    }, 500)
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
  if (!authStore.isLoggedIn) {
    console.log('Usuario no autenticado, redirigiendo a registro')
    router.push('/register?redirect=/checkout')
    return
  }
  
  console.log('Usuario autenticado:', authStore.cliente)
})
</script>

<style scoped>
.checkout-container {
  min-height: 100vh;
  background: #f8fafc;
  padding: 1rem;
  position: relative;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.9);
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
  width: 32px;
  height: 32px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #2E7D32;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.checkout-wrapper {
  max-width: 1000px;
  margin: 0 auto;
}

.checkout-header {
  text-align: center;
  margin-bottom: 1.5rem;
}

.checkout-header h1 {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.25rem;
}

.checkout-subtitle {
  color: #64748b;
  font-size: 0.95rem;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  color: #dc2626;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

.close-error {
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #dc2626;
  cursor: pointer;
}

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.order-summary,
.customer-info,
.payment-info {
  background: #fff;
  border-radius: 0.5rem;
  padding: 1.25rem;
  border: 1px solid #e2e8f0;
}

.order-summary {
  grid-column: 1 / -1;
}

.order-summary h3,
.customer-info h3,
.payment-info h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 1rem 0;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.cart-item {
  display: flex;
  gap: 0.75rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 0.5rem;
  border: 1px solid #f1f5f9;
}

.item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 0.375rem;
  flex-shrink: 0;
}

.item-details {
  flex: 1;
}

.item-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 0.375rem 0;
}

.item-info {
  display: flex;
  gap: 0.75rem;
  font-size: 0.85rem;
  color: #64748b;
}

.order-totals {
  border-top: 1px solid #e2e8f0;
  padding-top: 0.75rem;
}

.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.375rem;
  font-size: 0.9rem;
}

.total-final {
  font-weight: 700;
  font-size: 1rem;
  color: #2E7D32;
  border-top: 1px solid #e2e8f0;
  padding-top: 0.375rem;
}

.customer-card {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.customer-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.customer-avatar {
  width: 40px;
  height: 40px;
  background: #2E7D32;
  color: #fff;
  font-weight: bold;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.customer-details h4 {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
}

.customer-details p {
  margin: 0.25rem 0 0 0;
  color: #64748b;
  font-size: 0.85rem;
}

.customer-data {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.data-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.85rem;
}

.data-label {
  font-weight: 500;
  color: #475569;
}

/* Payment Section - Minimal Design */
.payment-method {
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 1rem;
  background: #fff;
}

.payment-method.active {
  border-color: #2E7D32;
}

.method-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.epayco-logo {
  height: 28px;
  width: auto;
}

.method-text h4 {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: #1e293b;
}

.method-text p {
  margin: 0.125rem 0 0 0;
  color: #64748b;
  font-size: 0.8rem;
}

/* Payment Icons - Minimal */
.payment-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
}

.payment-icon {
  height: 24px;
  width: auto;
  max-width: 40px;
  object-fit: contain;
  border-radius: 0.25rem;
  border: 1px solid #e2e8f0;
  padding: 0.25rem;
  background: #fff;
}

.pse-icon {
  background: #00a651;
  color: white;
  font-size: 0.7rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 32px;
  height: 24px;
  border: none;
}

.security-info {
  display: flex;
  gap: 1rem;
  margin-top: 0.75rem;
  flex-wrap: wrap;
}

.security-text {
  font-size: 0.75rem;
  color: #64748b;
}

.checkout-actions {
  grid-column: 1 / -1;
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-primary {
  background: #2E7D32;
  color: #fff;
}

.btn-primary:hover:not(:disabled) {
  background: #1B5E20;
}

.btn-primary:disabled {
  background: #9e9e9e;
  cursor: not-allowed;
}

.btn-secondary {
  background: #fff;
  color: #475569;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #f8fafc;
}

/* Responsive */
@media (max-width: 768px) {
  .checkout-content {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .checkout-actions {
    flex-direction: column;
  }
  
  .cart-item {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .payment-badges {
    justify-content: center;
  }
}
</style>
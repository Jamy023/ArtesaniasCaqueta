<template>
  <transition name="slide">
    <div v-if="isOpen" class="cart-sidebar">
      <div class="cart-header">
        <h2>Carrito de Compras</h2>
        <button @click="closeSidebar" class="close-btn">×</button>
      </div>
      
      <div class="cart-content">
        <!-- Carrito vacío -->
        <div v-if="productSelect.length === 0" class="empty-cart">
          <div class="empty-icon">🛒</div>
          <h3>Tu carrito está vacío</h3>
          <p>Agrega algunos productos para continuar</p>
          <button @click="goToProducts" class="browse-btn">
            Ver Productos
          </button>
        </div>

        <!-- Carrito con productos -->
        <div v-else class="cart-full">
          <div class="cart-items">
            <div v-for="product in productSelect" :key="product.id" class="cart-item">
              <img 
                :src="getProductImageUrl(product.main_image)" 
                :alt="product.name" 
                class="item-image" 
                @error="handleImageError"
              />
              <div class="item-details">
                <p class="item-name">{{ product.name }}</p>
                <p class="item-price">{{ formatPrice(product.price) }}</p>
                <div class="item-quantity">
                  <button @click="decreaseQuantity(product.id)" class="qty-btn">-</button>
                  <span class="qty-display">{{ product.quantity }}</span>
                  <button @click="increaseQuantity(product.id)" class="qty-btn">+</button>
                </div>
                <p class="item-total">Total: {{ formatPrice(product.price * product.quantity) }}</p>
              </div>
              <button @click="removeItem(product.id)" class="delete-btn" title="Quitar del carrito">×</button>
            </div>
          </div>

          <div class="cart-summary">
            <div class="summary-row">
              <span>Subtotal:</span>
              <span>{{ formatPrice(totalPrice) }}</span>
            </div>
            <div class="summary-row">
              <span>Envío:</span>
              <span>Gratis</span>
            </div>
            <div class="summary-row total-row">
              <span>Total:</span>
              <span>{{ formatPrice(totalPrice) }}</span>
            </div>
          </div>

          <div class="cart-actions">
            <button @click="goToCheckout" class="checkout-btn">
              <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
              Finalizar Compra
            </button>
            <button @click="continueShopping" class="continue-btn">
              Seguir Comprando
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
  
  <!-- Overlay -->
  <div v-if="isOpen" class="cart-overlay" @click="closeSidebar"></div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cartStore';
import { useAuthStore } from '../stores/authStore';

const router = useRouter()
const cartStore = useCartStore();
const authStore = useAuthStore();

const props = defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close']);

// Computed
const productSelect = computed(() => cartStore.items);
const totalPrice = computed(() => cartStore.totalPrice);

// Methods
const closeSidebar = () => {
  emit('close');
};

const removeItem = (productId) => {
  cartStore.removeFromCart(productId);
};

const increaseQuantity = (productId) => {
  cartStore.increaseQuantity(productId);
};

const decreaseQuantity = (productId) => {
  cartStore.decreaseQuantity(productId);
};

const goToProducts = () => {
  router.push('/products');
  closeSidebar();
};

const continueShopping = () => {
  closeSidebar();
};

const goToCheckout = () => {
  // Verificar si el usuario está autenticado
  if (!authStore.isAuthenticated) {
    // Guardar la intención de ir al checkout
    router.push('/register?redirect=/checkout');
  } else {
    router.push('/checkout');
  }
  closeSidebar();
};

// Formatear precios

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { 
    style: 'currency', 
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(price);
};

//metodo mara manejar las imagenes de los productos , por si se guardan de diferentes formas en el backend

const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png';
  if (main_image.startsWith('http')) return main_image;
  if (main_image.startsWith('public/')) return '/storage/' + main_image.replace('public/', '');
  if (main_image.startsWith('storage/app/public/')) return '/storage/' + main_image.replace('storage/app/public/', '');
  if (main_image.startsWith('products/')) return '/storage/' + main_image;
  return main_image;
};

const handleImageError = (event) => {
  event.target.src = '/img/logo.png';
};
</script>

<style scoped>
.cart-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.cart-sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 420px;
  height: 100vh; /* Asegurar altura completa */
  background: white;
  border-left: 1px solid #e9ecef;
  box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
  z-index: 1001;
  display: flex;
  flex-direction: column;
}

.cart-header {
  padding: 1.5rem;
  border-bottom: 2px solid #f8f9fa;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8f9fa;
  flex-shrink: 0; /* Evitar que se encoja */
}

.cart-header h2 {
  color: #5D4037;
  font-size: 1.3rem;
  font-weight: 600;
  margin: 0;
}

.close-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #e9ecef;
  border-radius: 50%;
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: #dee2e6;
  transform: scale(1.1);
}

.cart-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 0; /* Importante para el scroll */
}

/* Empty Cart Styles */
.empty-cart {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-cart h3 {
  color: #5D4037;
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
}

.empty-cart p {
  color: #666;
  margin-bottom: 2rem;
}

.browse-btn {
  background: #2E7D32;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.browse-btn:hover {
  background: #1B5E20;
}

/* Cart Full - Contenedor cuando hay productos */
.cart-full {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 0; /* Importante para el scroll */
}

/* Cart Items - Zona scrolleable */
.cart-items {
  flex: 1;
  overflow-y: auto; /* Habilitar scroll vertical */
  padding: 1rem;
  min-height: 0; /* Importante para que funcione el scroll */
  /* Estilos personalizados para la scrollbar */
  scrollbar-width: thin;
  scrollbar-color: #2E7D32 #f1f1f1;
}

/* Webkit scrollbar para Chrome, Safari, Edge */
.cart-items::-webkit-scrollbar {
  width: 6px;
}

.cart-items::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.cart-items::-webkit-scrollbar-thumb {
  background: #2E7D32;
  border-radius: 3px;
}

.cart-items::-webkit-scrollbar-thumb:hover {
  background: #1B5E20;
}

.cart-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid #e9ecef;
  border-radius: 0.75rem;
  margin-bottom: 1rem;
  background: #f8f9fa;
  position: relative;
  flex-shrink: 0; /* Evitar que los items se encojan */
}

.item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 0.5rem;
  flex-shrink: 0;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.item-name {
  font-weight: 600;
  color: #5D4037;
  font-size: 0.95rem;
  line-height: 1.3;
}

.item-price {
  color: #2E7D32;
  font-weight: 600;
  font-size: 1rem;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border: 1px solid #dee2e6;
  background: white;
  border-radius: 0.25rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  transition: all 0.2s ease;
}

.qty-btn:hover {
  background: #f8f9fa;
  border-color: #2E7D32;
}

.qty-display {
  min-width: 30px;
  text-align: center;
  font-weight: 600;
}

.item-total {
  color: #666;
  font-size: 0.9rem;
  font-weight: 500;
}

.delete-btn {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 24px;
  height: 24px;
  background: transparent;
  border: none;
  color: #999;
  font-size: 16px;
  cursor: pointer;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  background: #ffebee;
  color: #d32f2f;
  transform: scale(1.1);
}


.cart-summary {
  padding: 1rem 1.5rem;
  border-top: 2px solid #f8f9fa;
  background: #f8f9fa;
  flex-shrink: 0; /* Evitar que se encoja */
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.total-row {
  font-size: 1.1rem;
  font-weight: 700;
  color: #2E7D32;
  border-top: 1px solid #dee2e6;
  padding-top: 0.5rem;
  margin-top: 0.5rem;
}


.cart-actions {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  flex-shrink: 0; 
}

.checkout-btn {
  background: linear-gradient(135deg, #2E7D32, #4CAF50);
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
}

.checkout-btn:hover {
  background: linear-gradient(135deg, #1B5E20, #388E3C);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
}

.continue-btn {
  background: transparent;
  color: #666;
  border: 2px solid #e9ecef;
  padding: 0.75rem;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.continue-btn:hover {
  background: #f8f9fa;
  border-color: #dee2e6;
}

.btn-icon {
  width: 18px;
  height: 18px;
}

/* Animations */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from {
  transform: translateX(100%);
}

.slide-leave-to {
  transform: translateX(100%);
}


@media (max-width: 768px) {
  .cart-sidebar {
    width: 100%;
    max-width: 400px;
  }
  
  .cart-item {
    padding: 0.75rem;
  }
  
  .item-image {
    width: 60px;
    height: 60px;
  }
}
</style>
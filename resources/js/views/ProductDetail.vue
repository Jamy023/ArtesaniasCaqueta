<template>
  <div class="product-detail-container">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando producto...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h3>Error al cargar el producto</h3>
      <p>{{ error }}</p>
      <button @click="fetchProduct" class="retry-btn">Intentar de nuevo</button>
    </div>

    <!-- Product Detail -->
    <div v-else-if="product" class="product-detail">    
      <div class="product-main">
        <!-- Image Gallery -->
        <div class="product-gallery">
          <div class="main-image-container">
            <img 
              :src="getProductImageUrl(selectedImage)" 
              :alt="product.name"
              class="main-image"
              @error="handleImageError"
            />
            <div class="image-zoom-overlay" @click="openImageModal">
              <svg class="zoom-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
              </svg>
            </div>
          </div>
          
          <div class="thumbnail-gallery" v-if="allImages.length > 1">
            <button 
              v-for="(image, index) in allImages" 
              :key="index"
              @click="selectImage(image)"
              class="thumbnail-btn"
              :class="{ active: selectedImage === image }"
            >
              <img 
                :src="getProductImageUrl(image)" 
                :alt="`${product.name} - imagen ${index + 1}`"
                class="thumbnail-image"
                @error="handleImageError"
              />
            </button>
          </div>
        </div>

        <!-- Product Info -->
        <div class="product-info">
          <div class="product-header">
            <div class="product-category" v-if="product.category">
              <!-- 🔥 PROBLEMA SOLUCIONADO: Evitar navegación automática -->
              <span class="category-badge">
                {{ product.category.name }}
              </span>
            </div>
            
            <h1 class="product-title">{{ product.name }}</h1>
            
            <div class="product-rating">
              <div class="stars">
                <span v-for="i in 5" :key="i" class="star">★</span>
              </div>
              <span class="rating-text">(4.8) • 24 reseñas</span>
            </div>
          </div>

          <div class="product-pricing">
            <div class="price-main">{{ formatPrice(product.price) }}</div>
            <div class="price-info">
              <span class="price-description">{{ product.description }}</span>
            </div>
          </div>

          <div class="product-stock">
            <div class="stock-info" :class="stockClass">
              <span class="stock-icon">{{ stockIcon }}</span>
              <span class="stock-text">{{ stockText }}</span>
            </div>
            <div class="stock-bar" v-if="product.stock > 0">
              <div class="stock-fill" :style="{ width: stockPercentage + '%' }"></div>
            </div>
          </div>

          <!-- Quantity and Actions -->
          <div class="product-actions">
            <div class="quantity-selector">
              <label for="quantity">Cantidad:</label>
              <div class="quantity-controls">
                <button 
                  @click="decreaseQuantity" 
                  :disabled="quantity <= 1"
                  class="quantity-btn"
                >
                  -
                </button>
                <input 
                  v-model.number="quantity" 
                  type="number" 
                  min="1" 
                  :max="product.stock"
                  class="quantity-input"
                  id="quantity"
                />
                <button 
                  @click="increaseQuantity" 
                  :disabled="quantity >= product.stock"
                  class="quantity-btn"
                >
                  +
                </button>
              </div>
            </div>

            <div class="action-buttons">
              <button @click="addToCartWithQuantity" class="add-to-cart-btn" :disabled="product.stock === 0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <circle cx="9" cy="21" r="1" />
                  <circle cx="20" cy="21" r="1" />
                  <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />
                </svg>
                {{ product.stock === 0 ? 'Agotado' : 'Agregar' }}
              </button>
              
              <button 
                @click="buyNow" 
                :disabled="product.stock === 0"
                class="buy-now-btn"
              >
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Comprar ahora
              </button>
            </div>
          </div>

          <div class="product-details-section">
            <div class="product-specs" v-if="product.dimensions">
              <h3>Especificaciones</h3>
              <div class="specs-grid">
                <div v-for="(value, key) in product.dimensions" :key="key" class="spec-item">
                  <span class="spec-label">{{ formatSpecLabel(key) }}:</span>
                  <span class="spec-value">{{ value }}</span>
                </div>
              </div>
            </div>

            <div class="shipping-info-section">
              <h3>Información de envío</h3>
              <div class="shipping-options">
                <div class="shipping-option">
                  <span class="shipping-icon">🚚</span>
                  <div class="shipping-details">
                    <span class="shipping-title">Envío estándar</span>
                    <span class="shipping-time">3-5 días hábiles</span>
                  </div>
                </div>
                <div class="shipping-option">
                  <span class="shipping-icon">⚡</span>
                  <div class="shipping-details">
                    <span class="shipping-title">Envío express</span>
                    <span class="shipping-time">1-2 días hábiles</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div class="related-products-section" v-if="relatedProducts.length > 0">
        <h2>También te podría interesar</h2>
        <div class="related-products-grid">
          <div 
            v-for="relatedProduct in relatedProducts" 
            :key="relatedProduct.id"
            class="related-product-card"
            @click="goToProduct(relatedProduct.slug)"
          >
            <div class="related-product-image">
              <img 
                :src="getProductImageUrl(relatedProduct.main_image)" 
                :alt="relatedProduct.name"
                class="related-product-img"
                @error="handleImageError"
              />
            </div>
            <div class="related-product-info">
              <h4 class="related-product-name">{{ relatedProduct.name }}</h4>
              <div class="related-product-price">{{ formatPrice(relatedProduct.price) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="showImageModal" class="image-modal-overlay" @click="closeImageModal">
      <div class="image-modal">
        <button @click="closeImageModal" class="modal-close-btn">×</button>
        <img 
          :src="getProductImageUrl(selectedImage)" 
          :alt="product.name"
          class="modal-image"
        />
      </div>
    </div>

    <!-- WhatsApp Float Button -->
    <a :href="whatsappUrl" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
    </a>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../axios'
import { useCartStore } from '../stores/cartStore'

// Props and route
const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

// Reactive state
const product = ref(null)
const loading = ref(true)
const error = ref(null)
const selectedImage = ref('')
const quantity = ref(1)
const showImageModal = ref(false)
const relatedProducts = ref([])

// 🔥 FUNCIÓN MEJORADA - Navegar a producto relacionado
const goToProduct = (slug) => {
  // Navegación programática sin filtros
  router.push(`/products/${slug}`)
}

// FUNCIÓN CORREGIDA PARA AGREGAR AL CARRITO
const addToCartWithQuantity = () => {
  if (!product.value || product.value.stock === 0) return;
  
  // Agregar al carrito con la cantidad seleccionada
  cartStore.addToCart(product.value, quantity.value);
  
  console.log(`${quantity.value} x ${product.value.name} agregado al carrito`);
  
  // Opcional: Mostrar feedback visual o resetear cantidad
  // quantity.value = 1;
};

// Computed properties
const allImages = computed(() => {
  if (!product.value) return []
  
  const images = []
  if (product.value.main_image) {
    images.push(product.value.main_image)
  }
  if (product.value.gallery && Array.isArray(product.value.gallery)) {
    images.push(...product.value.gallery)
  }
  
  return [...new Set(images)] // Remove duplicates
})

const stockClass = computed(() => {
  if (!product.value) return ''
  
  if (product.value.stock === 0) return 'out-of-stock'
  if (product.value.stock < 5) return 'low-stock'
  return 'in-stock'
})

const stockIcon = computed(() => {
  if (!product.value) return ''
  
  if (product.value.stock === 0) return '❌'
  if (product.value.stock < 5) return '⚠️'
  return '✅'
})

const stockText = computed(() => {
  if (!product.value) return ''
  
  if (product.value.stock === 0) return 'Agotado'
  if (product.value.stock < 5) return `Solo quedan ${product.value.stock} unidades`
  return `${product.value.stock} disponibles`
})

const stockPercentage = computed(() => {
  if (!product.value || product.value.stock === 0) return 0
  
  const maxStock = 20 // Assuming max stock for visual representation
  return Math.min((product.value.stock / maxStock) * 100, 100)
})

const whatsappUrl = computed(() => {
  if (!product.value) return 'https://wa.me/573105867601'
  
  const message = `Hola! Me interesa el producto: ${product.value.name} - ${formatPrice(product.value.price)}`
  return `https://wa.me/573105867601?text=${encodeURIComponent(message)}`
})

// Methods
const fetchProduct = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await api.get(`/products/${route.params.slug}`)
    product.value = response.data
    
    // Set initial selected image
    if (product.value.main_image) {
      selectedImage.value = product.value.main_image
    }
    
    // Fetch related products
    await fetchRelatedProducts()
    
  } catch (err) {
    console.error('Error fetching product:', err)
    error.value = err.response?.data?.message || 'Error al cargar el producto'
  } finally {
    loading.value = false
  }
}

const fetchRelatedProducts = async () => {
  if (!product.value?.category) return
  
  try {
    const response = await api.get(`/products?category=${product.value.category.slug}&limit=4`)
    relatedProducts.value = (response.data.data || response.data)
      .filter(p => p.id !== product.value.id)
      .slice(0, 4)
  } catch (err) {
    console.error('Error fetching related products:', err)
  }
}

const selectImage = (image) => {
  selectedImage.value = image
}

const openImageModal = () => {
  showImageModal.value = true
}

const closeImageModal = () => {
  showImageModal.value = false
}

const increaseQuantity = () => {
  if (quantity.value < product.value.stock) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const buyNow = () => {
  if (product.value.stock === 0) return
  
  // Here you would implement direct purchase logic
  console.log('Buy now:', {
    product: product.value,
    quantity: quantity.value
  })
  
  // Redirect to checkout or show purchase modal
  router.push('/checkout')
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { 
    style: 'currency', 
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(price)
}

const formatSpecLabel = (key) => {
  const labels = {
    'material': 'Material',
    'size': 'Tamaño',
    'weight': 'Peso',
    'color': 'Color',
    'origin': 'Origen',
    'technique': 'Técnica'
  }
  return labels[key] || key.charAt(0).toUpperCase() + key.slice(1)
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

// Watch for route changes
watch(() => route.params.slug, fetchProduct)

// Lifecycle
onMounted(() => {
  fetchProduct();
  cartStore.loadFromLocalStorage();
});
</script>

<style scoped>
.product-detail-container {
  min-height: 100vh;
  background: #fff;
  padding: 2rem 1rem;
}

.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #2E7D32;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state {
  color: #d32f2f;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.retry-btn {
  background: #2E7D32;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.3s ease;
}

.retry-btn:hover {
  background: #1B5E20;
}

.product-detail {
  max-width: 1200px;
  margin: 0 auto;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
  font-size: 0.9rem;
}

.breadcrumb-link {
  color: #2E7D32;
  text-decoration: none;
  transition: color 0.3s ease;
}

.breadcrumb-link:hover {
  color: #1B5E20;
}

.breadcrumb-separator {
  margin: 0 0.5rem;
  color: #666;
}

.breadcrumb-current {
  color: #666;
}

.product-main {
  display: grid;
  grid-template-columns: 1fr 1fr ;
  gap: 4rem;
  margin-bottom: 4rem;
}

.product-gallery {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.main-image-container {
  position: relative;
  background: #f8f9fa;
  border-radius: 1rem;


}

.main-image {

  object-fit: contain;
  transition: transform 0.3s ease;
}

.image-zoom-overlay {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.5rem;
  border-radius: 0.5rem;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.main-image-container:hover .image-zoom-overlay {
  opacity: 1;
}

.zoom-icon {
  width: 20px;
  height: 20px;
}

.thumbnail-gallery {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
  padding: 0.5rem 0;
}

.thumbnail-btn {
  flex: 0 0 80px;
  height: 80px;
  border: 2px solid transparent;
  border-radius: 0.5rem;
  overflow: hidden;
  cursor: pointer;
  transition: border-color 0.3s ease;
  background: #f8f9fa;
}

.thumbnail-btn.active {
  border-color: #2E7D32;
}

.thumbnail-btn:hover {
  border-color: #4CAF50;
}

.thumbnail-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 1rem 0;
}

.product-header {
  margin-bottom: 2rem;
}

.product-category {
  margin-bottom: 0.5rem;
}

.category-link {
  color: #2E7D32;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.category-link:hover {
  color: #1B5E20;
}

.product-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #5D4037;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.product-descSend{
   
    display: flex;
}


.stars {
  color: #FFB300;
}

.star {
  font-size: 1.2rem;
}

.rating-text {
  color: #666;
  font-size: 0.9rem;
}

.product-pricing {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #F1F8E9 0%, #E8F5E8 100%);
  border-radius: 1rem;
  border: 1px solid #E8F5E8;
}

.price-main {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2E7D32;
  margin-bottom: 0.5rem;
}

.price-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.tax-info, .shipping-info {
  font-size: 0.9rem;
  color: #666;
}

.product-stock {
  margin-bottom: 2rem;
}

.stock-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.stock-info.in-stock {
  color: #2E7D32;
}

.stock-info.low-stock {
  color: #FF8F00;
}

.stock-info.out-of-stock {
  color: #d32f2f;
}

.stock-bar {
  width: 100%;
  height: 6px;
  background: #E0E0E0;
  border-radius: 3px;
  overflow: hidden;
}

.stock-fill {
  height: 100%;
  background: linear-gradient(90deg, #2E7D32, #4CAF50);
  transition: width 0.3s ease;
}

.product-features {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 0.5rem;
  border: 1px solid #E0E0E0;
}

.feature-icon {
  font-size: 1.2rem;
}

.feature-text {
  font-size: 0.9rem;
  font-weight: 500;
  color: #5D4037;
}

.product-actions {
  margin-bottom: 2rem;
}

.quantity-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  border: 2px solid #E0E0E0;
  border-radius: 0.5rem;
  overflow: hidden;
}

.quantity-btn {
  background: #f8f9fa;
  border: none;
  width: 40px;
  height: 40px;
  cursor: pointer;
  font-size: 1.2rem;
  font-weight: 600;
  transition: background 0.3s ease;
}

.quantity-btn:hover:not(:disabled) {
  background: #E0E0E0;
}

.quantity-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-input {
  width: 60px;
  height: 40px;
  text-align: center;
  border: none;
  font-weight: 600;
  background: white;
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.add-to-cart-btn, .buy-now-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem;
  border: none;
  border-radius: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-to-cart-btn {
  background: transparent;
  color: #2E7D32;
  border: 2px solid #2E7D32;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: #2E7D32;
  color: white;
}

.buy-now-btn {
  background: #2E7D32;
  color: white;
}

.buy-now-btn:hover:not(:disabled) {
  background: #1B5E20;
}

.add-to-cart-btn:disabled, .buy-now-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-icon {
  width: 20px;
  height: 20px;
}

.product-specs {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 1rem;
  border: 1px solid #E0E0E0;
}

.product-specs h3 {
  color: #5D4037;
  margin-bottom: 1rem;
  font-size: 1.25rem;
}

.specs-grid {
  display: grid;
  gap: 0.75rem;
}

.spec-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid #E0E0E0;
}

.spec-item:last-child {
  border-bottom: none;
}

.spec-label {
  font-weight: 600;
  color: #5D4037;
}

.spec-value {
  color: #666;
}

.shipping-info-section {
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 1rem;
  border: 1px solid #E0E0E0;
}

.shipping-info-section h3 {
  color: #5D4037;
  margin-bottom: 1rem;
  font-size: 1.25rem;
}

.shipping-options {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.shipping-option {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: white;
  border-radius: 0.5rem;
  border: 1px solid #E0E0E0;
}

.shipping-icon {
  font-size: 1.5rem;
}

.shipping-details {
  display: flex;
  flex-direction: column;
}

.shipping-title {
  font-weight: 600;
  color: #5D4037;
}

.shipping-time {
  color: #666;
  font-size: 0.9rem;
}

.product-description-section {
  margin-bottom: 4rem;

  background: #f8f9fa;
  border-radius: 1rem;
  border: 1px solid #E0E0E0;
}

.product-description-section h2 {
  color: #5D4037;
  margin-bottom: 1.5rem;
  font-size: 1.75rem;
  font-weight: 600;
}

.description-content {
  line-height: 1.6;
  color: #333;
  font-size: 1rem;
}

.description-content p {
  margin-bottom: 1.5rem;
}

.description-highlights {
  margin-top: 2rem;
}

.description-highlights h3 {
  color: #5D4037;
  margin-bottom: 1rem;
  font-size: 1.25rem;
  font-weight: 600;
}

.description-highlights ul {
  margin-left: 1.5rem;
  margin-bottom: 1rem;
}

.description-highlights li {
  margin-bottom: 0.75rem;
  color: #333;
}

.product-des+send{
    width: 100%;
   display: flex; 
}
/* Related Products Section */
.related-products-section {
  margin-bottom: 4rem;
  padding: 2rem;
  background: #f8f9fa;
  border-radius: 1rem;
  border: 1px solid #E0E0E0;
}

.related-products-section h2 {
  color: #5D4037;
  margin-bottom: 2rem;
  font-size: 1.75rem;
  font-weight: 600;
  text-align: center;
}

.related-products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.related-product-card {
  background: white;
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid #E0E0E0;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.related-product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.related-product-link {
  display: block;
  text-decoration: none;
  color: inherit;
}

.related-product-image {
  aspect-ratio: 1;
  overflow: hidden;
  background: #f8f9fa;
}

.related-product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.related-product-card:hover .related-product-img {
  transform: scale(1.05);
}

.related-product-info {
  padding: 1.5rem;
}

.related-product-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #5D4037;
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.related-product-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2E7D32;
}

/* Image Modal */
.image-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 2rem;
}

.image-modal {
  position: relative;
  max-width: 90%;
  max-height: 90%;
  background: white;
  border-radius: 1rem;
  padding: 1rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-close-btn {
  position: absolute;
  top: -15px;
  right: -15px;
  width: 40px;
  height: 40px;
  background: #2E7D32;
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s ease;
  z-index: 1001;
}

.modal-close-btn:hover {
  background: #1B5E20;
}

.modal-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 0.5rem;
  max-height: 80vh;
  max-width: 80vw;
}

/* WhatsApp Float Button */
.whatsapp-float {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  width: 60px;
  height: 60px;
  background: #25D366;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
  z-index: 100;
  transition: all 0.3s ease;
  animation: pulse 2s infinite;
}

.whatsapp-float:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 25px rgba(37, 211, 102, 0.4);
}

.whatsapp-float img {
  width: 32px;
  height: 32px;
  filter: brightness(0) invert(1);
}

.product-detail-section {
  display: flex;
}

@keyframes pulse {
  0% {
    transform: scale(1);
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 6px 25px rgba(37, 211, 102, 0.4);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .product-detail-container {
    padding: 1rem;
  }
  
  .product-main {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .product-title {
    font-size: 2rem;
  }
  
  .price-main {
    font-size: 2rem;
  }
  
  .product-features {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .related-products-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }
  
  .whatsapp-float {
    bottom: 1rem;
    right: 1rem;
    width: 50px;
    height: 50px;
  }
  
  .whatsapp-float img {
    width: 24px;
    height: 24px;
  }
  
  .image-modal-overlay {
    padding: 1rem;
  }
  
  .modal-image {
    max-height: 70vh;
    max-width: 90vw;
  }
}

@media (max-width: 480px) {
  .product-detail-container {
    padding: 0.5rem;
  }
  
  .product-title {
    font-size: 1.5rem;
  }
  
  .price-main {
    font-size: 1.75rem;
  }
  
  .thumbnail-gallery {
    flex-wrap: wrap;
  }
  
  .thumbnail-btn {
    flex: 0 0 60px;
    height: 60px;
  }
  
  .related-products-grid {
    grid-template-columns: 1fr;
  }
}

.product-details-section {
  display: flex;
  gap: 2rem;
  margin-top: 2rem;
}
.product-specs,
.shipping-info-section {
  flex: 1 1 0;
  min-width: 0;
}
@media (max-width: 900px) {
  .product-details-section {
    flex-direction: column;
    gap: 1.5rem;
  }
}
</style>
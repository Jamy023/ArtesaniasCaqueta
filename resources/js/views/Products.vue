<template>
  <div class="products-page">
    <!-- Header -->
    <div class="page-header">
      <img src="https://res.cloudinary.com/dbjmhh4wr/image/upload/v1756001294/fondo_y3qqeq.png" alt="Fondo Amazonía" 
      
      class="page-header-bg-img" />
      <h1 class="page-title">Catálogo de Artesanías</h1>
      <p class="page-subtitle">Descubre la riqueza cultural de la Amazonía a través de nuestras artesanías únicas</p>
    </div>
     <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/573105867601" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
    </a>

    <div class="filters-section">
      <div class="filters-container">
        <div class="search-box">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Buscar artesanías..."
            @input="handleSearch"
            class="search-input"
          >
          <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        
        <select v-model="selectedCategory" @change="handleCategoryChange" class="category-filter">
          <option value="">Todas las categorías</option>
          <option v-for="category in categories" :key="category.id" :value="category.slug">
            {{ category.name }}
          </option>
        </select>

        <select v-model="sortBy" @change="handleSortChange" class="sort-filter">
          <option value="">Ordenar por</option>
          <option value="name_asc">Nombre (A-Z)</option>
          <option value="name_desc">Nombre (Z-A)</option>
          <option value="price_asc">Precio (Menor a Mayor)</option>
          <option value="price_desc">Precio (Mayor a Menor)</option>
          <option value="stock_desc">Stock (Mayor a Menor)</option>
        </select>
      </div>
    </div>

    <div class="products-container">
      <!-- Loading state -->
      <div v-if="productsState.loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando artesanías...</p>
      </div>

      <!-- Error state -->
      <div v-else-if="productsState.error" class="error-state">
        <div class="error-icon">⚠️</div>
        <h3>Error al cargar productos</h3>
        <p>{{ productsState.error }}</p>
        <button @click="fetchProducts" class="retry-btn">Reintentar</button>
      </div>

      <!-- Products grid -->
      <div v-else-if="filteredProducts.length > 0" class="products-grid">
        <article 
          v-for="product in filteredProducts" 
          :key="product.id" 
          class="product-card"
        >
          <!-- Product image -->
          <div class="product-image-container">
            <img 
              :src="getProductImageUrl(product.main_image)" 
              :alt="product.name" 
              class="product-img"
              @error="handleImageError"
              loading="lazy"
            />
            <div class="product-overlay">
              <div class="product-actions">
                <router-link 
                  :to="{ name: 'ProductDetail', params: { slug: product.slug } }" 
                  class="view-btn"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </router-link>
              </div>
            </div>
            <div class="product-badge" v-if="product.stock === 0">
              <span>Agotado</span>
            </div>
          </div>
          
          <div class="product-info">
            <div class="product-category-badge" v-if="product.category">
              {{ product.category.name }}
            </div>
            
            <h3 class="product-name">{{ product.name }}</h3>
            
            <div class="product-bottom">
              <div class="product-pricing">
                <span class="product-price">{{ formatPrice(product.price) }}</span>
                <span class="product-stock" :class="{ 'out-of-stock': product.stock === 0 }">
                  {{ product.stock > 0 ? `${product.stock} disponibles` : 'Sin stock' }}
                </span>
              </div>
              
              <div class="product-actions-new">
                <button @click="addToCart(product)" class="add-to-cart-btn-new" :disabled="product.stock === 0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="20" cy="21" r="1" />
                    <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />
                  </svg>
                  {{ product.stock === 0 ? 'Agotado' : 'Agregar' }}
                </button>
              </div>
            </div>
          </div>
        </article>
      </div>

      <!-- No products found -->
      <div v-else class="no-products">
        <div class="no-products-icon">🎨</div>
        <h3>No se encontraron productos</h3>
        <p v-if="searchQuery || selectedCategory">
          Intenta ajustar los filtros o buscar con otros términos.
        </p>
        <p v-else>
          Estamos trabajando en traerte las mejores artesanías amazónicas.
        </p>
        <button @click="clearFilters" class="clear-filters-btn" v-if="searchQuery || selectedCategory">
          Limpiar filtros
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../axios'
import { useCartStore } from '../stores/cartStore'
import { getProductImageUrl, handleImageError } from '../utils/imageUtils'
import fondoImage from '/public/img/fondo.webp'

const cartStore = useCartStore();
const route = useRoute();
const router = useRouter();

// Estado reactivo
const productsState = reactive({
  data: [],
  loading: false,
  error: null
})

const categoriesState = reactive({
  data: [],
  loading: false,
  error: null
})

// Filtros
const searchQuery = ref('')
const selectedCategory = ref('')
const sortBy = ref('')

//  FUNCIÓN PARA INICIALIZAR FILTROS DESDE LA URL
const initializeFiltersFromUrl = () => {
  const urlParams = route.query;
  
  // Aplicar filtro de búsqueda desde URL
  if (urlParams.search) {
    searchQuery.value = urlParams.search;
  }
  
  // Aplicar filtro de categoría desde URL
  if (urlParams.category) {
    selectedCategory.value = urlParams.category;
  }
  
  // Aplicar ordenamiento desde URL
  if (urlParams.sort) {
    sortBy.value = urlParams.sort;
  }
};

//  FUNCIÓN PARA ACTUALIZAR LA URL SIN NAVEGACIÓN
const updateUrlParams = () => {
  const query = {};
  
  if (searchQuery.value.trim()) {
    query.search = searchQuery.value.trim();
  }
  
  if (selectedCategory.value) {
    query.category = selectedCategory.value;
  }
  
  if (sortBy.value) {
    query.sort = sortBy.value;
  }
  
  // Solo actualizar si los parámetros han cambiado
  const currentQuery = route.query;
  const queryChanged = JSON.stringify(currentQuery) !== JSON.stringify(query);
  
  if (queryChanged) {
    router.replace({ query });
  }
};

// Computed para productos filtrados
const filteredProducts = computed(() => {
  let filtered = [...productsState.data]

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(query) ||
      (product.description && product.description.toLowerCase().includes(query)) ||
      (product.category && product.category.name.toLowerCase().includes(query))
    )
  }

  // Filtrar por categoría
  if (selectedCategory.value) {
    filtered = filtered.filter(product => 
      product.category && product.category.slug === selectedCategory.value
    )
  }

  // Ordenar
  if (sortBy.value) {
    switch (sortBy.value) {
      case 'name_asc':
        filtered.sort((a, b) => a.name.localeCompare(b.name))
        break
      case 'name_desc':
        filtered.sort((a, b) => b.name.localeCompare(a.name))
        break
      case 'price_asc':
        filtered.sort((a, b) => a.price - b.price)
        break
      case 'price_desc':
        filtered.sort((a, b) => b.price - a.price)
        break
      case 'stock_desc':
        filtered.sort((a, b) => b.stock - a.stock)
        break
    }
  }

  return filtered
})

// Computed para categorías
const categories = computed(() => categoriesState.data)

// Obtener productos
const fetchProducts = async () => {
  productsState.loading = true
  productsState.error = null

  try {
    const res = await api.get('/products')

    if (res.data?.data) {
      productsState.data = res.data.data
    } else if (Array.isArray(res.data)) {
      productsState.data = res.data
    } else {
      productsState.data = []
    }

  } catch (error) {
    console.error('Error fetching products:', error)
    productsState.error = 'Error al cargar los productos. Verifica la conexión con el servidor.'
    productsState.data = []
  } finally {
    productsState.loading = false
  }
}

// Obtener categorías
const fetchCategories = async () => {
  categoriesState.loading = true
  categoriesState.error = null

  try {
    const res = await api.get('/categories')

    if (res.data?.data) {
      categoriesState.data = res.data.data
    } else if (Array.isArray(res.data)) {
      categoriesState.data = res.data
    } else {
      categoriesState.data = []
    }

  } catch (error) {
    console.error('Error fetching categories:', error)
    categoriesState.error = 'Error al cargar las categorías.'
    categoriesState.data = []
  } finally {
    categoriesState.loading = false
  }
}

// Handlers
const handleSearch = () => {
  updateUrlParams();
}

const handleCategoryChange = () => {
  updateUrlParams();
}

const handleSortChange = () => {
  updateUrlParams();
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  sortBy.value = ''
  updateUrlParams();
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(price)
}


const addToCart = (producto) => {
  cartStore.addToCart(producto);
};

// 🔥 WATCHER PARA CAMBIOS EN LA RUTA
watch(() => route.query, (newQuery) => {
  // Solo actualizar si los filtros locales no coinciden con la URL
  if (newQuery.search !== searchQuery.value) {
    searchQuery.value = newQuery.search || '';
  }
  if (newQuery.category !== selectedCategory.value) {
    selectedCategory.value = newQuery.category || '';
  }
  if (newQuery.sort !== sortBy.value) {
    sortBy.value = newQuery.sort || '';
  }
}, { deep: true });

// Cargar datos al montar
onMounted(async () => {
  // Primero cargar las categorías
  await fetchCategories();
  
  // Luego inicializar filtros desde URL
  initializeFiltersFromUrl();
  
  // Finalmente cargar productos
  await fetchProducts();
});
</script>

<style scoped>

.whatsapp-float {
  position: fixed;
  width: 60px;
  height: 60px;
  bottom: 20px;
  right: 20px;
  background-color: #25D366;
  color: white;
  border-radius: 50%;
  text-align: center;
  font-size: 30px;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: transform 0.3s ease;
}

.whatsapp-float img {
  width: 35px;
  height: 35px;
}

.whatsapp-float:hover {
  transform: scale(1.1);
}
.products-page {
  background: #fff;
  min-height: 100vh;
  width: 100%;
  overflow-x: hidden;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, rgb(59, 63, 59) 0%, rgb(48, 63, 49) 100%);
  padding: 3rem 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.page-header-bg-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
}

.page-header::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  z-index: 1;
}

.page-title {
  color: white;
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
  position: relative;
  z-index: 2;
}

.page-subtitle {
  color: #E8F5E8;
  font-size: 1.25rem;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

/* Filters Section */
.filters-section {
  background: #F1F8E9;
  padding: 2rem;
  border-bottom: 1px solid #E8F5E8;
}

.filters-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 1rem;
  align-items: center;
}

.search-box {
  position: relative;
  flex: 1;
}

.search-input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid #E8F5E8;
  border-radius: 0.75rem;
  font-size: 1rem;
  background: white;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #8D6E63;
}

.category-filter, .sort-filter {
  padding: 1rem;
  border: 2px solid #E8F5E8;
  border-radius: 0.75rem;
  font-size: 1rem;
  background: white;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 180px;
}

.category-filter:focus, .sort-filter:focus {
  outline: none;
  border-color: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

/* Products Container */
.products-container {
  max-width: 90%;
  margin: 0 auto;
  padding: 2rem;
}

/* GRID OPTIMIZADO PARA TODAS LAS PANTALLAS */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.product-card {
  background: #fff;
  border-radius: 1.5rem;
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid #E8F5E8;
  box-shadow: 0 4px 20px rgba(93, 64, 55, 0.08);
  position: relative;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(46, 125, 50, 0.15);
  border-color: #4CAF50;
}

.product-image-container {
  position: relative;
  overflow: hidden;
  height: 200px;
  background: linear-gradient(45deg, #F1F8E9, #E8F5E8);
}

.product-image-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  z-index: -1;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-img {
  transform: scale(1.1);
}

.product-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(46, 125, 50, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
  opacity: 1;
}

.product-actions {
  display: flex;
  gap: 1rem;
}

.view-btn {
  background: white;
  color: #2E7D32;
  border: none;
  padding: 0.75rem;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  display: flex;
  align-items: center;  
  justify-content: center;
  text-decoration: none;
}

.view-btn:hover {
  background: #4CAF50;
  color: white;
  transform: scale(1.1);
}

.product-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: #f44336;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.product-info {
  padding: 1.5rem;
}

.product-category-badge {
  background: #E8F5E8;
  color: #2E7D32;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-block;
  margin-bottom: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.product-name {
  color: #5D4037;
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.product-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.product-pricing {
  flex: 1;
}

.product-price {
  color: #2E7D32;
  font-size: 1rem;
  font-weight: 700;
  display: block;
  margin-bottom: 0.25rem;
}

.product-stock {
  font-size: 0.85rem;
  color: #4CAF50;
  font-weight: 500;
}

.product-stock.out-of-stock {
  color: #f44336;
}

.add-to-cart-btn-new {
  background: #2E7D32;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.add-to-cart-btn-new:hover:not(:disabled) {
  background: #1B5E20;
  transform: translateY(-2px);
}

.add-to-cart-btn-new:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* Loading, Error, and No Products */
.loading-state, .error-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #5D4037;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #2E7D32;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.no-products {
  text-align: center;
  padding: 4rem 2rem;
  color: #5D4037;
}

.no-products-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.no-products h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.no-products p {
  color: #8D6E63;
  margin-bottom: 1rem;
}

.retry-btn, .clear-filters-btn {
  background: #2E7D32;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.75rem;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  margin-top: 1rem;
  transition: all 0.3s ease;
  font-weight: 600;
}

.retry-btn:hover, .clear-filters-btn:hover {
  background: #1B5E20;
  transform: translateY(-2px);
}

/* =================== RESPONSIVE MEJORADO =================== */

/* Tablet */
@media (max-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
  }
}

/* Móviles grandes (768px y menos) - Grid 2x2 OPTIMIZADO */
@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
  
  .page-subtitle {
    font-size: 1.1rem;
  }
  
  .filters-container {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  /* GRID 2x2 OPTIMIZADO */
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .product-card {
    border-radius: 1rem;
    box-shadow: 0 2px 12px rgba(93, 64, 55, 0.08);
  }
  
  .product-image-container {
    height: 180px; /* Altura adecuada para 2 columnas */
  }
  
  .product-info {
    padding: 1rem 0.75rem; /* Menos padding lateral */
  }
  
  .product-category-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.6rem;
    margin-bottom: 0.5rem;
  }
  
  .product-name {
    font-size: 0.9rem;
    line-height: 1.2;
    margin-bottom: 0.5rem;
    /* Limitar a 2 líneas máximo */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .product-bottom {
    flex-direction: column;
    align-items: stretch;
    gap: 0.75rem;
  }
  
  .product-price {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  
  .product-stock {
    font-size: 0.75rem;
  }
  
  /* Botón más compacto pero bien visible */
  .add-to-cart-btn-new {
    width: 100%;
    padding: 0.6rem;
    font-size: 0.8rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
  }
  
  .add-to-cart-btn-new svg {
    width: 14px;
    height: 14px;
  }
}

/* Móviles pequeños (480px y menos) */
@media (max-width: 480px) {
  .page-header {
    padding: 2rem 1rem;
  }
  
  .page-title {
    font-size: 1.75rem;
  }
  
  .filters-section {
    padding: 1rem;
  }
  
  .products-container {
    padding: 1rem;
  }
  
  /* Mantener 2 columnas pero más compacto */
  .products-grid {
    gap: 0.75rem;
  }
  
  .product-image-container {
    height: 160px;
  }
  
  .product-info {
    padding: 0.75rem 0.5rem;
  }
  
  .product-name {
    font-size: 0.85rem;
  }
  
  .product-price {
    font-size: 0.9rem;
  }
  
  .add-to-cart-btn-new {
    padding: 0.5rem;
    font-size: 0.75rem;
  }
  
  .add-to-cart-btn-new svg {
    width: 12px;
    height: 12px;
  }
}

/* Móviles muy pequeños (400px y menos) */
@media (max-width: 400px) {
  .products-grid {
    gap: 0.5rem;
  }
  
  .product-image-container {
    height: 140px;
  }
  
  .product-info {
    padding: 0.5rem 0.4rem;
  }
  
  .product-name {
    font-size: 0.8rem;
  }
  
  .product-price {
    font-size: 0.85rem;
  }
  
  .product-category-badge {
    font-size: 0.65rem;
    padding: 0.15rem 0.5rem;
  }
  
  .add-to-cart-btn-new {
    padding: 0.4rem;
    font-size: 0.7rem;
  }
}
</style>
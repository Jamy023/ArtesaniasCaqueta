<template>
  <div class="products-page">
    <!-- Header -->
    <div class="page-header">
      <h1 class="page-title">Catálogo de Artesanías</h1>
      <p class="page-subtitle">Descubre la riqueza cultural de la Amazonía a través de nuestras artesanías únicas</p>
    </div>


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
import '../css/Product.css'
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../axios'
import { useCartStore } from '../stores/cartStore'

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

 const getProductImageUrl = (main_image) => {
    if (!main_image) return '/img/logo.png';
    if (main_image.startsWith('http')) return main_image;

    // Limpiar cualquier prefijo y construir ruta correcta
    const cleanImage = main_image.replace(/^(public\/|storage\/|storage\/app\/public\/|products\/)/, '');
    return `/storage/products/${cleanImage}`;
  };



const handleImageError = (event) => {
  event.target.src = '/img/logo.png';
};

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
/* Los estilos permanecen igual que en tu archivo original */
</style>
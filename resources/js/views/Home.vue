<template>
  <div class="bg-white min-h-screen w-full overflow-x-hidden">
    <section class="hero hero-enhanced ">
      <div class="hero-content">
        <div class="hero-text fade-in-up">
          <h1 class="hero-title">Artesanías auténticas del corazón de la Amazonía</h1>
          <p class="hero-subtitle">
            Descubre piezas únicas creadas por artesanos locales.
            Cada producto cuenta una historia de tradición y cultura.
            <br>
            <br>
            📦 Envíos a todo el país
            .

          </p>
          <router-link to="/products" class="hero-cta">
            <span>Explorar catálogo</span>
            <svg class="hero-cta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
          </router-link>
        </div>
        <div class="hero-image fade-in-up delay-1">
          <img src="@img/hero.webp" alt="Artesanías amazónicas" class="hero-img" />
        </div>
      </div>
    </section>
    <div class="hero-divider"></div>
    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/573105867601" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
    </a>

   


    <section class="products-section">
      <div class="section-header">
        <h2 class="products-title">Productos destacados</h2>
        <p class="products-subtitle">Selección especial de nuestras mejores artesanías</p>
      </div>

      <!-- Estados de carga -->
      <div v-if="productsState.loading" class="loading">
        <div class="spinner"></div>
        <p>Cargando productos...</p>
      </div>

      <div v-else-if="productsState.error" class="error">
        <p>{{ productsState.error }}</p>
        <button @click="fetchProducts" class="retry-btn">Reintentar</button>
      </div>

      <!-- Carrusel de productos -->
      <div v-else-if="productsState.data.length > 0" class="carousel-container">
        <div class="carousel-wrapper">
          <button class="carousel-btn carousel-btn-prev" @click="prevSlide" :disabled="currentSlide === 0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>

          <div class="carousel-track-container fade-in-up">
            <div class="carousel-track" :style="{ transform: `translateX(-${currentSlide * (100 / slidesToShow)}%)` }">
              <article v-for="product in productsState.data" :key="product.id" class="carousel-slide">
                <div class="product-card-new">
                  <div class="product-image-container-new">
                    <img :src="getProductImageUrl(product.main_image)" :alt="product.name" class="product-img-new"
                      @error="handleImageError" />
                    <div class="product-badge-new" v-if="product.stock === 0">
                      <span>Agotado</span>
                    </div>
                  </div>

                  <div class="product-info-new">
                    <div class="product-category-badge-new" v-if="product.category">
                      {{ product.category.name }}
                    </div>

                    <h3 class="product-name-new">{{ product.name }}</h3>

                    <div class="product-bottom-new">
                      <div class="product-pricing-new">
                        <span class="product-price-new">{{ formatPrice(product.price) }}</span>
                        <span class="product-stock-new" :class="{ 'out-of-stock': product.stock === 0 }">
                          {{ product.stock > 0 ? `${product.stock} disponibles` : 'Sin stock' }}
                        </span>
                      </div>

                      <div class="product-actions-new">
                        <router-link :to="{ name: 'ProductDetail', params: { slug: product.slug } }"
                          class="view-btn-new">
                          Ver
                        </router-link>
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
                </div>
              </article>
            </div>
          </div>


          <button class="carousel-btn carousel-btn-next" @click="nextSlide" :disabled="currentSlide >= maxSlides">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>

        <!-- Indicadores -->
        <div class="carousel-indicators">
          <button v-for="(slide, index) in Math.ceil(productsState.data.length / slidesToShow)" :key="index"
            class="carousel-indicator" :class="{ active: currentSlide === index }" @click="goToSlide(index)"></button>
        </div>
      </div>

      <!-- Sin productos -->
      <div v-else class="no-products">
        <div class="no-products-icon">🎨</div>
        <h3>No hay productos disponibles</h3>
        <p>Estamos trabajando en traerte las mejores artesanías amazónicas.</p>
        <router-link to="/products" class="no-products-link">
          Explorar catálogo completo
        </router-link>
      </div>

      <!-- Call to action para ver más productos -->
      <div v-if="productsState.data.length > 0" class="view-all-section">
        <router-link to="/products" class="view-all-btn">
          Ver todos los productos
          <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
          </svg>
        </router-link>
      </div>
    </section>

    <!-- Sección de Categorías Destacadas -->
    <section class="categories-section">
      <div class="section-header">
        <h2 class="products-title">Nuestras Colecciones</h2>
        <p class="products-subtitle">Explora nuestra diversa selección de artesanías tradicionales</p>
      </div>

      <div class="categories-grid">
        <!-- Categoría 1 -->
        <div class="category-card">
          <img src="https://images.pexels.com/photos/6438428/pexels-photo-6438428.jpeg" alt="Tejidos Tradicionales" class="category-image">
          <div class="category-overlay">
            <h3 class="category-title">Tejidos Tradicionales</h3>
            <p class="category-description">Descubre nuestras telas y tejidos hechos a mano</p>
          </div>
          <router-link to="/category/tejidos" class="category-link">
            Explorar Colección
          </router-link>
        </div>

        <!-- Categoría 2 -->
        <div class="category-card">
          <img src="https://images.pexels.com/photos/12456216/pexels-photo-12456216.jpeg" alt="Cerámica Artesanal" class="category-image">
          <div class="category-overlay">
            <h3 class="category-title">Cerámica Artesanal</h3>
            <p class="category-description">Piezas únicas moldeadas por manos expertas</p>
          </div>
          <router-link to="/category/ceramica" class="category-link">
            Explorar Colección
          </router-link>
        </div>

        <!-- Categoría 3 -->
        <div class="category-card">
          <img src="https://images.pexels.com/photos/6438366/pexels-photo-6438366.jpeg" alt="Joyería Étnica" class="category-image">
          <div class="category-overlay">
            <h3 class="category-title">Joyería Étnica</h3>
            <p class="category-description">Accesorios que cuentan historias ancestrales</p>
          </div>
          <router-link to="/category/joyeria" class="category-link">
            Explorar Colección
          </router-link>
        </div>
      </div>
    </section>

    <!-- Sección Historia - Agregar después de products-section -->

    <template>
      <div class="story-container">
        <!-- Opción 1: Timeline Interactiva -->
        <section class="story-section">
          <div class="story-container">
            <div class="story-content">
              <div class="story-text">
                <h2 class="story-title">Nuestra Historia</h2>
                <p class="story-subtitle">Del corazón de la Amazonía a tu hogar</p>
                <p class="story-description"> En el año 2020, en pleno corazón de la Amazonía colombiana, nació nuestra
                  pasión por preservar y compartir la riqueza artesanal de nuestros ancestros. Somos más que una tienda;
                  somos un puente entre las tradiciones milenarias y el mundo moderno. </p>
                <div class="story-highlight">
                  <p>"Cada pieza que encuentras aquí lleva consigo el alma de nuestros artesanos, la sabiduría de
                    generaciones y el amor por nuestra tierra amazónica."</p>
                </div>
                <p class="story-description"> Trabajamos directamente con comunidades indígenas y artesanos locales,
                  garantizando que cada compra contribuya al desarrollo sostenible de nuestras comunidades. Nuestro
                  compromiso es mantener vivas estas tradiciones mientras brindamos a los artesanos una fuente de
                  ingresos justa y digna. </p>
                <div class="story-stats">
                  <div class="story-stat"> <span class="story-stat-number">50+</span> <span
                      class="story-stat-label">Artesanos</span> </div>
                  <div class="story-stat"> <span class="story-stat-number">15</span> <span
                      class="story-stat-label">Comunidades</span> </div>
                  <div class="story-stat"> <span class="story-stat-number">200+</span> <span
                      class="story-stat-label">Productos</span> </div>
                </div>
              </div>
              <div class="story-images">
                <div class="story-image-main"> <img
                    src="https://sdmntprnortheu.oaiusercontent.com/files/00000000-fc64-61f4-87e5-665114806844/raw?se=2025-07-11T05%3A20%3A02Z&sp=r&sv=2024-08-04&sr=b&scid=bf8f26fc-272c-5440-bcea-04b803ac1007&skoid=a3412ad4-1a13-47ce-91a5-c07730964f35&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2025-07-10T18%3A51%3A35Z&ske=2025-07-11T18%3A51%3A35Z&sks=b&skv=2024-08-04&sig=wSoV4LIk6kcxDzscXqa0wLVsxGy31CC2V3qvJIfi/8I%3D"
                    alt="Artesanos trabajando en la Amazonía" class="story-img" />
                  <div class="story-image-overlay"> Artesanos de la comunidad trabajando cerámica tradicional </div>
                </div>
                <div class="story-image-secondary"> <img
                    src="https://i.pinimg.com/736x/00/5a/42/005a427e139e21607586f71a8125bafb.jpg"
                    alt="Proceso de tejido tradicional" class="story-img" />
                  <div class="story-image-overlay"> Proceso de tejido en fibras naturales </div>
                </div>
                <div class="story-image-secondary"> <img
                    src="https://i.pinimg.com/736x/00/5a/42/005a427e139e21607586f71a8125bafb.jpg"
                    alt="Productos terminados" class="story-img" />
                  <div class="story-image-overlay"> Productos listos para llegar a tu hogar </div>
                </div>
              </div>
            </div>
          </div>
        </section>



      </div>
    </template>
     </div>
    </template>
    <script setup>
import { ref, reactive, onMounted, onUnmounted, watch, computed } from 'vue'


import api from '../axios'


import '../css/Home.css'


// Agregar estas variables reactivas al script setup existente
const currentSlide = ref(0)
const slidesToShow = ref(3) // Productos visibles por slide
const autoPlayInterval = ref(null)

// Computed para calcular el número máximo de slides
const maxSlides = computed(() => {
  return Math.max(0, Math.ceil(productsState.data.length / slidesToShow.value) - 1)
})

// Funciones del carrusel
const nextSlide = () => {
  if (currentSlide.value < maxSlides.value) {
    currentSlide.value++
  } else {
    currentSlide.value = 0 // Volver al inicio
  }
}

const prevSlide = () => {
  if (currentSlide.value > 0) {
    currentSlide.value--
  } else {
    currentSlide.value = maxSlides.value // Ir al final
  }
}

const goToSlide = (slideIndex) => {
  currentSlide.value = slideIndex
}

// Auto-play del carrusel
const startAutoPlay = () => {
  autoPlayInterval.value = setInterval(() => {
    nextSlide()
  }, 2000) // Cambiar cada 5 segundos
}

const stopAutoPlay = () => {
  if (autoPlayInterval.value) {
    clearInterval(autoPlayInterval.value)
    autoPlayInterval.value = null
  }
}

// Responsive - ajustar número de slides según el tamaño de pantalla
const updateSlidesToShow = () => {
  if (window.innerWidth <= 768) {
    slidesToShow.value = 1
  } else if (window.innerWidth <= 1024) {
    slidesToShow.value = 2
  } else {
    slidesToShow.value = 3
  }
}

// Lifecycle hooks
onMounted(() => {
  fetchProducts()
  updateSlidesToShow()
  startAutoPlay()
  
  // Listener para resize
  window.addEventListener('resize', updateSlidesToShow)
})

onUnmounted(() => {
  stopAutoPlay()
  window.removeEventListener('resize', updateSlidesToShow)
})

// Watch para reiniciar autoplay cuando cambian los productos
watch(() => productsState.data, (newData) => {
  if (newData.length > 0) {
    currentSlide.value = 0
    stopAutoPlay()
    startAutoPlay()
  }
})

// Estado reactivo
const productsState = reactive({
  data: [],
  loading: false,
  error: null
})

// Obtener productos destacados (limitado)
const fetchProducts = async () => {
  productsState.loading = true
  productsState.error = null

  try {
    const res = await api.get('/products?limit=3') // Limitar a 8 productos

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

// Truncar texto
const truncateText = (text, maxLength) => {
  if (!text) return ''
  return text.length > maxLength ? text.slice(0, maxLength) + '...' : text
}

// Formatear precios
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(price)
}





const addToCart = (product) => {
  if (product.stock === 0) return
  
  // Aquí implementarías la lógica del carrito
  console.log('Agregando al carrito:', product)
  
  // Ejemplo de notificación
  alert(`${product.name} agregado al carrito`)
}

// Cargar productos al montar
onMounted(fetchProducts)

const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png'; // Imagen por defecto


  if (main_image.startsWith('http')) return main_image;

  if (main_image.startsWith('public/')) {
    return '/storage/' + main_image.replace('public/', '');
  }

  if (main_image.startsWith('storage/app/public/')) {
    return '/storage/' + main_image.replace('storage/app/public/', '');
  }

  if (main_image.startsWith('products/')) {
    return '/storage/' + main_image;
  }

  return main_image;
};
</script>

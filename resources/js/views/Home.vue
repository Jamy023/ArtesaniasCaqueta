<template>
  <div class="bg-white min-h-screen w-full overflow-x-hidden">
    <!-- HERO - SE MANTIENE IGUAL -->
    <section class="hero hero-enhanced">
      <img src="https://res.cloudinary.com/dbjmhh4wr/image/upload/v1756001294/fondo_y3qqeq.png" alt="Fondo Amazonía" class="hero-bg-img" />
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="hero-text fade-in-up">
          <h1 class="hero-title">Artesanías auténticas del corazón de la Amazonía</h1>
          <p class="hero-subtitle">
            Descubre piezas únicas creadas por artesanos locales.
            Cada producto cuenta una historia de tradición y cultura.
            <br>
            <br>
            📦 Envíos a todo el país
          </p>
          <router-link to="/products" class="hero-cta">
            <span>Explorar catálogo</span>
            <svg class="hero-cta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
          </router-link>
        </div>
        <div class="hero-image fade-in-up delay-1">
          <img src="https://res.cloudinary.com/dbjmhh4wr/image/upload/v1756001148/hero_amhs4l.webp" alt="Artesanías amazónicas" class="hero-img" />
        </div>
      </div>
    </section>

    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/573228815953" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
    </a>

    <!-- SECCIÓN DE PRODUCTOS DESTACADOS CON CARRUSEL -->
    <section class="featured-products-section">
      <div class="section-container">
        <div class="section-header-new">
          <div class="section-badge">Selección especial</div>
          <h2 class="section-title-new">Productos destacados</h2>
          <p class="section-subtitle-new">Las mejores piezas de nuestros artesanos más talentosos</p>
        </div>

        <!-- Estados de carga -->
        <div v-if="productsState.loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Cargando productos únicos...</p>
        </div>

        <div v-else-if="productsState.error" class="error-state">
          <div class="error-icon">⚠️</div>
          <h3>Oops, algo salió mal</h3>
          <p>{{ productsState.error }}</p>
          <button @click="fetchProducts" class="retry-button">Intentar nuevamente</button>
        </div>

        <!-- Carrusel de productos -->
        <div v-else-if="productsState.data.length > 0" class="products-showcase">
          <div class="carousel-container" ref="carouselContainer">
            <div 
              class="products-carousel" 
              :style="{ transform: `translateX(-${currentSlide * slideWidth}%)` }"
            >
              <div 
                v-for="product in productsState.data" 
                :key="product.id" 
                class="carousel-slide"
                @click="goToProduct(product)"
              >
                <article class="product-card-carousel">
                  <div class="product-image-wrapper-carousel">
                    <img 
                      :src="getProductImageUrl(product.main_image)" 
                      :alt="product.name" 
                      class="product-image-carousel" 
                      @error="handleImageError" 
                    />
                    <div class="product-overlay-carousel">
                      <div class="quick-view-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                      </div>
                    </div>
                    <div class="product-status" v-if="product.stock === 0">Agotado</div>
                    <div class="product-category-tag" v-if="product.category">{{ product.category.name }}</div>
                  </div>

                  <div class="product-content-carousel">
                    <h3 class="product-name-carousel">{{ product.name }}</h3>
                    <div class="product-price-wrapper-carousel">
                      <span class="product-price-carousel">{{ formatPrice(product.price) }}</span>
                      <span class="product-stock-info-carousel" :class="{ 'out-of-stock': product.stock === 0 }">
                        {{ product.stock > 0 ? `${product.stock} disponibles` : 'Sin stock' }}
                      </span>
                    </div>
                  </div>
                </article>
              </div>
            </div>

            <!-- Indicadores de navegación -->
            <div class="carousel-indicators">
              <button 
                v-for="(slide, index) in totalSlides" 
                :key="index"
                @click="goToSlide(index)"
                :class="['indicator-dot', { active: index === currentSlide }]"
              ></button>
            </div>

            <!-- Botones de navegación -->
            <button 
              class="carousel-nav prev" 
              @click="prevSlide"
              :disabled="currentSlide === 0"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15,18 9,12 15,6"></polyline>
              </svg>
            </button>
            <button 
              class="carousel-nav next" 
              @click="nextSlide"
              :disabled="currentSlide >= totalSlides - 1"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"></polyline>
              </svg>
            </button>
          </div>

          <div class="view-all-products">
            <router-link to="/products" class="view-all-button">
              <span>Ver todo el catálogo</span>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m9 18 6-6-6-6"/>
              </svg>
            </router-link>
          </div>
        </div>

        <!-- Sin productos -->
        <div v-else class="no-products-state">
          <div class="no-products-illustration">🎨</div>
          <h3>Próximamente nuevos productos</h3>
          <p>Estamos preparando una hermosa colección de artesanías amazónicas para ti</p>
        </div>
      </div>
    </section>

    <!-- SECCIÓN DE CONFIANZA Y BENEFICIOS -->
    <section class="trust-section">
      <div class="trust-container">
        <div class="trust-items">
          <div class="trust-item">
            <div class="trust-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 1L21 5V11C21 16.55 17.16 21.74 12 23C6.84 21.74 3 16.55 3 11V5L12 1Z"/>
              </svg>
            </div>
            <h3>100% Auténtico</h3>
            <p>Productos artesanales únicos hechos por Artesanos e indigenas</p>
          </div>

          <div class="trust-item">
            <div class="trust-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 13H11V3H3V13ZM3 21H11V15H3V21ZM13 21H21V11H13V21ZM13 3V9H21V3H13Z"/>
              </svg>
            </div>
            <h3>Envíos Seguros</h3>
            <p>Empaque especial para proteger cada pieza durante el transporte</p>
          </div>

          <div class="trust-item">
            <div class="trust-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M7 18C5.9 18 5 18.9 5 20S5.9 22 7 22 9 21.1 9 20 8.1 18 7 18ZM1 2V4H3L6.6 11.59L5.25 14.04C5.09 14.32 5 14.65 5 15C5 16.1 5.9 17 7 17H19V15H7.42C7.28 15 7.17 14.89 7.17 14.75L7.2 14.63L8.1 13H15.55C16.3 13 16.96 12.59 17.3 11.97L20.88 5H18.21L15.27 11H8.53L4.27 2H1ZM17 18C15.9 18 15 18.9 15 20S15.9 22 17 22 19 21.1 19 20 18.1 18 17 18Z"/>
              </svg>
            </div>
            <h3>Comercio Justo</h3>
            <p>Tu compra apoya directamente a las comunidades artesanas</p>
          </div>

          <div class="trust-item">
            <div class="trust-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12S6.48 22 12 22 22 17.52 22 12 17.48 2 12 2ZM13 19H11V17H13V19ZM15.07 11.25L14.17 12.17C13.45 12.9 13 13.5 13 15H11V14.5C11 13.4 11.45 12.4 12.17 11.67L13.41 10.41C13.78 10.05 14 9.55 14 9C14 7.9 13.1 7 12 7S10 7.9 10 9H8C8 6.79 9.79 5 12 5S16 6.79 16 9C16 9.88 15.64 10.68 15.07 11.25Z"/>
              </svg>
            </div>
            <h3>Atención Personal</h3>
            <p>Asesoría especializada para ayudarte a elegir la pieza perfecta</p>
          </div>
        </div>
      </div>
    </section>

    <!-- SECCIÓN NUESTRA HISTORIA REDISEÑADA -->
    <section class="story-section-new">
      <div class="story-container-new">
        <div class="story-content-grid">
          <div class="story-text-content">
            <div class="story-header">
              <div class="section-badge">Nuestra misión</div>
              <h2 class="story-title-new">Preservando tradiciones milenarias</h2>
            </div>
            
            <div class="story-text-block">
              <p class="story-lead">Desde hace 20 años conectamos el corazón de la Amazonía colombiana con el mundo, a través de nuestros productos donde se refleja el talento de los artesanos y la belleza natural de la biodiversidad caqueteña.
</p>
              
              <p class="story-paragraph">Trabajamos directamente con más de 25 artesanos e indígenas, garantizando que haya variedad en los productos ofrecidos a nuestros clientes y así mismo contribuyendo al desarrollo sostenible y justo de los artesanos.</p>
            </div>

            <div class="story-highlight-box">
              <div class="highlight-icon">💡</div>
              <div class="highlight-content">
                <h4>Nuestro compromiso</h4>
                <p>"Cada pieza lleva consigo el alma de nuestros artesanos y la sabiduría de generaciones."</p>
              </div>
            </div>

            <div class="story-metrics">
              <div class="metric">
                <span class="metric-number">25+</span>
                <span class="metric-label">Artesanos e Indigenas</span>
              </div>
              <div class="metric">
                <span class="metric-number">15</span>
                <span class="metric-label">Comunidades</span>
              </div>
              <div class="metric">
                <span class="metric-number">100+</span>
                <span class="metric-label">Variedad de diseños en productos</span>
              </div>
              <div class="metric">
                <span class="metric-number">1000+</span>
                <span class="metric-label">Clientes felices</span>
              </div>
            </div>
          </div>

          <div class="story-visual-content">
            <div class="story-image-main-new">
              <img src="https://res.cloudinary.com/dbjmhh4wr/image/upload/v1756171337/tienda_nnc2tb.jpg" alt="Artesanos trabajando" class="story-main-image">
              
            </div>
            <div class="story-images-grid">
              <div class="story-small-image">
                <img src="https://scontent-mia3-1.xx.fbcdn.net/v/t39.30808-6/515442315_736016622406983_5031074432807981918_n.jpg?_nc_cat=106&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeGlg-yQflW0cLRlp2FPe6LyrX43OB79AUStfjc4Hv0BRIr985WYUtp3MBqvrZjyP546WUN5CopK2tRhxoQwfv3N&_nc_ohc=VXX5ZJ9ioPgQ7kNvwFD9Znx&_nc_oc=Adl0xw5SyuNONgdu58NVNENKMI11fB2fgerMhono0TslfxRM6CGRAHiDqHOpYt5C83k&_nc_zt=23&_nc_ht=scontent-mia3-1.xx&_nc_gid=00iRjP0yw40YX5wMyQfKMw&oh=00_AfX3QnfareQ887Ici6q9eLy-YmcdNAbPOrgqFjP95W1fOw&oe=68B2F3E4" alt="Proceso artesanal" class="small-img">
              </div>
              <div class="story-small-image">
                <img src="https://scontent-mia3-3.xx.fbcdn.net/v/t39.30808-6/515438783_719272567414722_995562772338509084_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeFb6A365irJpPjlDYDTqRvlCYk4JP83HPEJiTgk_zcc8VbPVNX4b0rg9PDEYh_UT0DOjre-z02AypfCyBep-v3i&_nc_ohc=G9r9hwXfRiUQ7kNvwHPtmNO&_nc_oc=AdnXCfKVr48wXrnJe5SEL9lW2o6LAFGOFuTvlHUwXgzuQ6b_y154L98OsfNZiMANVYY&_nc_zt=23&_nc_ht=scontent-mia3-3.xx&_nc_gid=-_lkcc33XoVvyYYkqeSB2Q&oh=00_AfXOTuotjC43JUqTndy2cFiAegtZb_NnZZMdZbIMVGtczQ&oe=68B2F74F" alt="Productos terminados" class="small-img">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECCIÓN CALL TO ACTION FINAL -->
    <section class="final-cta-section">
      <div class="cta-container">
        <div class="cta-content">
          <h2 class="cta-title">¿Listo para encontrar tu pieza perfecta?</h2>
          <p class="cta-subtitle">Explora nuestra colección completa de artesanías auténticas y encuentra esa pieza única que buscas</p>
          <div class="cta-buttons">
            <router-link to="/products" class="cta-button-primary">
              <span>Ver catálogo completo</span>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m9 18 6-6-6-6"/>
              </svg>
            </router-link>
            <a href="https://wa.me/573105867601" class="cta-button-secondary" target="_blank">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.703"/>
              </svg>
              Contáctanos por WhatsApp
            </a>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../axios'
import { getProductImageUrl, handleImageError } from '../utils/imageUtils'
import fondoImage from '/public/img/fondo.webp'
import '../css/Home.css';

const router = useRouter()

// Estado reactivo para productos
const productsState = reactive({
  data: [],
  loading: false,
  error: null
})

// Variables del carrusel
const currentSlide = ref(0)
const carouselContainer = ref(null)
const autoSlideInterval = ref(null)

// Configuración del carrusel
const slidesPerView = computed(() => {
  if (typeof window !== 'undefined') {
    if (window.innerWidth >= 1024) return 3
    if (window.innerWidth >= 768) return 2
    return 1
  }
  return 3
})

const slideWidth = computed(() => {
  return 100 / slidesPerView.value
})

const totalSlides = computed(() => {
  const total = productsState.data.length - slidesPerView.value + 1
  return total > 0 ? total : 1
})

// Metodo para Obtener productos destacados
const fetchProducts = async () => {
  productsState.loading = true
  productsState.error = null

  try {
    const res = await api.get('/products?limit=12')
    
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
    startAutoSlide()
  }
}

// Navegación del carrusel
const nextSlide = () => {
  if (currentSlide.value < totalSlides.value - 1) {
    currentSlide.value++
  } else {
    currentSlide.value = 0
  }
}

const prevSlide = () => {
  if (currentSlide.value > 0) {
    currentSlide.value--
  } else {
    currentSlide.value = totalSlides.value - 1
  }
}

const goToSlide = (index) => {
  currentSlide.value = index
}

// Auto-slide
const startAutoSlide = () => {
  if (productsState.data.length > slidesPerView.value) {
    autoSlideInterval.value = setInterval(() => {
      nextSlide()
    }, 1000)
  }
}

const stopAutoSlide = () => {
  if (autoSlideInterval.value) {
    clearInterval(autoSlideInterval.value)
    autoSlideInterval.value = null
  }
}

// Ir al detalle del producto
const goToProduct = (product) => {
  router.push({ name: 'ProductDetail', params: { slug: product.slug } })
}

// Formatear precios
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(price)
}

// Manejar resize
const handleResize = () => {
  if (currentSlide.value >= totalSlides.value) {
    currentSlide.value = Math.max(0, totalSlides.value - 1)
  }
}

// Metodos del ciclo de vida para montar  eventos
onMounted(() => {
  fetchProducts()
  document.documentElement.style.setProperty('--fondo-image', `url(${fondoImage})`)
  window.addEventListener('resize', handleResize)
})
//metodo para desmontar eventos
onUnmounted(() => {
  stopAutoSlide()
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>


</style>
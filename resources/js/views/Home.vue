<template>
  <div class="bg-white min-h-screen w-full overflow-x-hidden">
    <!-- HERO - SE MANTIENE IGUAL -->
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
          </p>
          <router-link to="/products" class="hero-cta">
            <span>Explorar catálogo</span>
            <svg class="hero-cta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
          </router-link>
        </div>
        <div class="hero-image fade-in-up delay-1">
          <img src="/img/hero.webp" alt="Artesanías amazónicas" class="hero-img" />
        </div>
      </div>
    </section>

    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/573105867601" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
    </a>

   

    <!-- SECCIÓN DE PRODUCTOS DESTACADOS MEJORADA -->
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

        <!-- Grid de productos mejorado -->
        <div v-else-if="productsState.data.length > 0" class="products-showcase">
          <div class="products-grid">
            <article v-for="product in productsState.data.slice(0, 6)" :key="product.id" class="product-card-premium">
              <div class="product-image-wrapper">
                <img :src="getProductImageUrl(product.main_image)" :alt="product.name" class="product-image-premium" @error="handleImageError" />
                <div class="product-overlay">
                  <router-link :to="{ name: 'ProductDetail', params: { slug: product.slug } }" class="quick-view-btn">
                    Vista rápida
                  </router-link>
                </div>
                <div class="product-status" v-if="product.stock === 0">Agotado</div>
                <div class="product-category-tag" v-if="product.category">{{ product.category.name }}</div>
              </div>

              <div class="product-content-premium">
                <h3 class="product-name-premium">{{ product.name }}</h3>
                <div class="product-price-wrapper">
                  <span class="product-price-premium">{{ formatPrice(product.price) }}</span>
                  <span class="product-stock-info" :class="{ 'out-of-stock': product.stock === 0 }">
                    {{ product.stock > 0 ? `${product.stock} disponibles` : 'Sin stock' }}
                  </span>
                </div>
                
                <div class="product-actions-premium">
             
                  <button @click="addToCart(product)" class="add-cart-btn-premium" :disabled="product.stock === 0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="9" cy="21" r="1"/>
                      <circle cx="20" cy="21" r="1"/>
                      <path d="m1 1 4 2.68L3 15.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    {{ product.stock === 0 ? 'Agotado' : 'Agregar' }}
                  </button>
                </div>
              </div>
            </article>
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
            <p>Productos artesanales únicos hechos por comunidades indígenas</p>
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
              <p class="story-lead">Desde 2020, conectamos el corazón de la Amazonía colombiana con el mundo, siendo más que una tienda: somos guardianes de tradiciones ancestrales.</p>
              
              <p class="story-paragraph">Trabajamos directamente con más de 50 artesanos de 15 comunidades indígenas diferentes, garantizando que cada compra contribuya al desarrollo sostenible y justo de nuestras comunidades.</p>
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
                <span class="metric-number">50+</span>
                <span class="metric-label">Artesanos</span>
              </div>
              <div class="metric">
                <span class="metric-number">15</span>
                <span class="metric-label">Comunidades</span>
              </div>
              <div class="metric">
                <span class="metric-number">500+</span>
                <span class="metric-label">Productos</span>
              </div>
              <div class="metric">
                <span class="metric-number">1000+</span>
                <span class="metric-label">Clientes felices</span>
              </div>
            </div>
          </div>

          <div class="story-visual-content">
            <div class="story-image-main-new">
              <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Artesanos trabajando" class="story-main-image">
              <div class="story-image-badge">Artesanos en acción</div>
            </div>
            <div class="story-images-grid">
              <div class="story-small-image">
                <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Proceso artesanal" class="small-img">
              </div>
              <div class="story-small-image">
                <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Productos terminados" class="small-img">
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
import { ref, reactive, onMounted, onUnmounted, watch, computed } from 'vue'
import api from '../axios'

import { getProductImageUrl, handleImageError } from '../utils/imageUtils'
// Importar imagen de fondo correctamente para Vite
import fondoImage from '/public/img/fondo.png'

// Estado reactivo para productos
const productsState = reactive({
  data: [],
  loading: false,
  error: null
})

// Obtener productos destacados
const fetchProducts = async () => {
  productsState.loading = true
  productsState.error = null

  try {
    const res = await api.get('/products?limit=6')
    
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

// Formatear precios
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(price)
}

// Agregar al carrito
const addToCart = (product) => {
  if (product.stock === 0) return
  
  console.log('Agregando al carrito:', product)
  alert(`${product.name} agregado al carrito`)
}

// Las funciones getProductImageUrl y handleImageError se importan desde utils

// Lifecycle hooks
onMounted(() => {
  fetchProducts()
  
  // Aplicar imagen de fondo usando variable CSS
  document.documentElement.style.setProperty('--fondo-image', `url(${fondoImage})`)
})
</script>

<style scoped>
/* =================== ESTILOS EXISTENTES DEL HERO (SE MANTIENEN) =================== */
.home-container {
  background: #fff;
  min-height: 100vh;
  width: 100%;
  overflow-x: hidden;
  margin: 0;
}

.hero-enhanced {
  background: linear-gradient(135deg, rgb(59, 63, 59) 0%, rgb(48, 63, 49) 80%, rgba(48, 63, 49, 0.9) 100%);
  min-height: 650px;
  display: flex;
  align-items: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 15px 30px rgba(48, 63, 49, 0.2);
  margin: 0%;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--fondo-image, url('/img/fondo.png')) center/cover;
}

.hero::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  z-index: 1;
}

.hero-content {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
  position: relative;
  z-index: 2;
  padding: 0 2rem;
}

.hero-text {
  color: white;
}

.hero-title {
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
  font-size: 1.25rem;
  line-height: 1.6;
  margin-bottom: 2rem;
  color: #E8F5E8;
}

.hero-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: #4CAF50;
  color: white;
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 1.1rem;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.hero-cta:hover {
  background: #45a049;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
}

.hero-cta-icon {
  width: 20px;
  height: 20px;
}

.hero-image {
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-img {
  max-width: 100%;
  height: 40em;
  border-radius: 1rem;
  filter: drop-shadow(0 15px 15px rgba(2, 46, 13, 0.2));
  transition: transform 0.3s ease;
}

.fade-in-up {
  opacity: 0;
  transform: translateY(40px);
  animation: fadeInUp 1s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

.fade-in-up.delay-1 {
  animation-delay: 0.3s;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

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

/* =================== NUEVOS ESTILOS REDISEÑADOS =================== */

/* SECCIÓN DE CONFIANZA */
.trust-section {
  background: linear-gradient(135deg, #F1F8E9 0%, #E8F5E8 100%);
  padding: 5rem 2rem;
  position: relative;
  border-bottom: 1px solid rgba(76, 175, 80, 0.1);
}

.trust-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><circle cx="30" cy="30" r="2" fill="%23A5D6A7" opacity="0.3"/></svg>') repeat;
  opacity: 0.4;
  z-index: 1;
}

.trust-container {
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.trust-items {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2.5rem;
}

.trust-item {
  background: rgba(255, 255, 255, 0.9);
  padding: 2.5rem 2rem;
  border-radius: 1.5rem;
  text-align: center;
  box-shadow: 0 8px 32px rgba(93, 64, 55, 0.1);
  border: 1px solid rgba(76, 175, 80, 0.15);
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.trust-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(46, 125, 50, 0.2);
  border-color: rgba(76, 175, 80, 0.3);
}

.trust-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #2E7D32, #4CAF50);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  transition: transform 0.3s ease;
}

.trust-icon svg {
  width: 28px;
  height: 28px;
}

.trust-item:hover .trust-icon {
  transform: scale(1.1) rotate(5deg);
}

.trust-item h3 {
  color: #5D4037;
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.trust-item p {
  color: #8D6E63;
  line-height: 1.6;
  font-size: 0.95rem;
}

/* CONTENEDORES DE SECCIÓN GENERALES */
.section-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.section-header-new {
  text-align: center;
  margin-bottom: 4rem;
}

.section-badge {
  background: linear-gradient(45deg, #E8F5E8, #C8E6C9);
  color: #2E7D32;
  padding: 0.5rem 1.5rem;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  display: inline-block;
  margin-bottom: 1rem;
  border: 1px solid rgba(46, 125, 50, 0.2);
}

.section-title-new {
  color: #5D4037;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.section-subtitle-new {
  color: #8D6E63;
  font-size: 1.1rem;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
}

/* SECCIÓN DE PRODUCTOS DESTACADOS */
.featured-products-section {
  background: #fff;
  padding: 6rem 0;
  position: relative;
}

.loading-state, .error-state {
  text-align: center;
  padding: 4rem 2rem;
}

.loading-spinner {
  width: 50px;
  height: 50px;
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

.error-state {
  color: #5D4037;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.retry-button {
  background: #4CAF50;
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.retry-button:hover {
  background: #45a049;
  transform: translateY(-2px);
}

.products-showcase {
  position: relative;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2.5rem;
  margin-bottom: 4rem;
}

.product-card-premium {
  background: #fff;
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(93, 64, 55, 0.08);
  border: 1px solid #F1F8E9;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  position: relative;
}

.product-card-premium:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 60px rgba(46, 125, 50, 0.15);
  border-color: #E8F5E8;
}

.product-image-wrapper {
  position: relative;
  height: 280px;
  overflow: hidden;
  background: linear-gradient(135deg, #F1F8E9, #E8F5E8);
}

.product-image-premium {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.product-card-premium:hover .product-image-premium {
  transform: scale(1.08);
}

.product-overlay {
  position: absolute;
  inset: 0;
  background: rgba(46, 125, 50, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card-premium:hover .product-overlay {
  opacity: 1;
}

.quick-view-btn {
  background: white;
  color: #2E7D32;
  padding: 0.75rem 1.5rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.quick-view-btn:hover {
  background: #E8F5E8;
  transform: scale(1.05);
}

.product-status {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: #f44336;
  color: white;
  padding: 0.4rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 5;
}

.product-category-tag {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: rgba(255, 255, 255, 0.9);
  color: #2E7D32;
  padding: 0.4rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 5;
  backdrop-filter: blur(10px);
}

.product-content-premium {
  padding: 2rem;
}

.product-name-premium {
  color: #5D4037;
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.3;
}

.product-price-wrapper {
  margin-bottom: 1.5rem;
}

.product-price-premium {
  color: #2E7D32;
  font-size: 1.6rem;
  font-weight: 700;
  display: block;
  margin-bottom: 0.5rem;
}

.product-stock-info {
  color: #4CAF50;
  font-size: 0.85rem;
  font-weight: 500;
}

.product-stock-info.out-of-stock {
  color: #f44336;
}

.product-actions-premium {
  display: flex;
  gap: 1rem;
}

.view-details-btn {
  background: transparent;
  color: #2E7D32;
  border: 2px solid #2E7D32;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  text-align: center;
  flex: 1;
}

.view-details-btn:hover {
  background: #2E7D32;
  color: white;
  transform: translateY(-2px);
}

.add-cart-btn-premium {
  background: linear-gradient(135deg, #2E7D32, #4CAF50);
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  flex: 1;
}

.add-cart-btn-premium:hover:not(:disabled) {
  background: linear-gradient(135deg, #1B5E20, #2E7D32);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
}

.add-cart-btn-premium:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.view-all-products {
  text-align: center;
  margin-top: 3rem;
}

.view-all-button {
  background: linear-gradient(135deg, #2E7D32, #4CAF50);
  color: white;
  padding: 1.25rem 2.5rem;
  border-radius: 60px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(46, 125, 50, 0.3);
}

.view-all-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(46, 125, 50, 0.4);
}

.no-products-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #8D6E63;
}

.no-products-illustration {
  font-size: 4rem;
  margin-bottom: 2rem;
}

.no-products-state h3 {
  color: #5D4037;
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

/* SECCIÓN DE CATEGORÍAS */
.categories-showcase-section {
  background: linear-gradient(135deg, #F1F8E9 0%, #ffffff 100%);
  padding: 6rem 0;
}

.categories-grid-new {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.category-large {
  grid-row: span 2;
}

.category-card-new {
  position: relative;
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: 0 12px 40px rgba(93, 64, 55, 0.1);
  transition: all 0.4s ease;
  background: white;
  min-height: 300px;
  display: flex;
  flex-direction: column;
}

.category-card-new:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 60px rgba(46, 125, 50, 0.15);
}

.category-image-container {
  position: relative;
  flex: 1;
  overflow: hidden;
}

.category-image-new {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.category-card-new:hover .category-image-new {
  transform: scale(1.05);
}

.category-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, transparent 0%, rgba(46, 125, 50, 0.8) 100%);
}

.category-content-new {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2rem;
  color: white;
}

.category-label {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
  margin-bottom: 0.75rem;
  backdrop-filter: blur(10px);
}

.category-title-new {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  line-height: 1.2;
}

.category-large .category-title-new {
  font-size: 2rem;
}

.category-description-new {
  font-size: 0.95rem;
  opacity: 0.9;
  margin-bottom: 1rem;
}

.category-stats {
  font-size: 0.85rem;
  opacity: 0.8;
  font-weight: 500;
}

.category-link-new {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  background: rgba(255, 255, 255, 0.15);
  color: white;
  padding: 0.75rem 1.25rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.category-card-new:hover .category-link-new {
  opacity: 1;
  transform: translateY(0);
}

.category-link-new:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.05);
}

/* SECCIÓN DE HISTORIA */
.story-section-new {
  background: #fff;
  padding: 6rem 0;
  position: relative;
}

.story-container-new {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.story-content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: start;
}

.story-text-content {
  padding-right: 2rem;
}

.story-header {
  margin-bottom: 2rem;
}

.story-title-new {
  color: #5D4037;
  font-size: 2.25rem;
  font-weight: 700;
  line-height: 1.3;
  margin-bottom: 1.5rem;
}

.story-text-block {
  margin-bottom: 2.5rem;
}

.story-lead {
  color: #5D4037;
  font-size: 1.2rem;
  font-weight: 600;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.story-paragraph {
  color: #8D6E63;
  font-size: 1rem;
  line-height: 1.7;
}

.story-highlight-box {
  background: linear-gradient(135deg, #E8F5E8, #F1F8E9);
  padding: 2rem;
  border-radius: 1.5rem;
  border-left: 4px solid #4CAF50;
  margin: 2rem 0;
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.highlight-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.highlight-content h4 {
  color: #2E7D32;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.highlight-content p {
  color: #5D4037;
  font-style: italic;
  line-height: 1.6;
  margin: 0;
}

.story-metrics {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
  margin-top: 3rem;
}

.metric {
  text-align: center;
  padding: 1.5rem;
  background: rgba(241, 248, 233, 0.5);
  border-radius: 1rem;
  border: 1px solid rgba(76, 175, 80, 0.1);
}

.metric-number {
  display: block;
  color: #2E7D32;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.metric-label {
  color: #8D6E63;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.story-visual-content {
  display: grid;
  gap: 1.5rem;
}

.story-image-main-new {
  position: relative;
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(93, 64, 55, 0.15);
  height: 350px;
}

.story-main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.story-image-badge {
  position: absolute;
  bottom: 1.5rem;
  left: 1.5rem;
  background: rgba(46, 125, 50, 0.9);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

.story-images-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.story-small-image {
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(93, 64, 55, 0.1);
  height: 150px;
}

.small-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.story-small-image:hover .small-img {
  transform: scale(1.05);
}

/* SECCIÓN CALL TO ACTION FINAL */
.final-cta-section {
  background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%);
  padding: 5rem 0;
  position: relative;
  overflow: hidden;
}

.final-cta-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="1.5" fill="white" opacity="0.1"/><circle cx="50" cy="80" r="2.5" fill="white" opacity="0.1"/></svg>') repeat;
  z-index: 1;
}

.cta-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
  text-align: center;
  position: relative;
  z-index: 2;
}

.cta-content {
  color: white;
}

.cta-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.cta-subtitle {
  font-size: 1.2rem;
  margin-bottom: 3rem;
  opacity: 0.9;
  line-height: 1.6;
}

.cta-buttons {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.cta-button-primary {
  background: white;
  color: #2E7D32;
  padding: 1.25rem 2.5rem;
  border-radius: 60px;
  text-decoration: none;
  font-weight: 700;
  font-size: 1.1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.cta-button-primary:hover {
  background: #E8F5E8;
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
}

.cta-button-secondary {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  padding: 1.25rem 2.5rem;
  border-radius: 60px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.cta-button-secondary:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-3px);
}

/* =================== RESPONSIVE DESIGN =================== */

/* =================== AJUSTES RESPONSIVOS MEJORADOS =================== */

/* Mantener todos los estilos existentes y agregar estos ajustes */

@media (max-width: 1024px) {
  .hero-content {
    grid-template-columns: 1fr;
    text-align: center;
    gap: 2rem;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  /* Mejorar la imagen del hero en tablet */
  .hero-img {
    height: 35em;
    max-width: 90%;
  }
  
  .trust-items {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
  }
  
  .categories-grid-new {
    grid-template-columns: 1fr;
  }
  
  .category-large {
    grid-row: span 1;
  }
  
  .story-content-grid {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  
  .story-text-content {
    padding-right: 0;
  }
  
  .story-metrics {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .hero {
    padding: 1rem;
    min-height: 600px; /* Aumentar altura mínima */
  }
  
  .hero-content {
    gap: 3rem; /* Más espacio entre texto e imagen */
  }
  
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  /* MEJORA PRINCIPAL: Imagen del hero más prominente en móviles */
  .hero-img {
    height: 30em; /* Aumentar considerablemente */
    max-width: 95%;
    margin: 0 auto;
    display: block;
  }
  
  .hero-image {
    order: -1; /* Poner la imagen arriba del texto */
    margin-bottom: 2rem;
  }
  
  .trust-items {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .trust-item {
    padding: 2rem 1.5rem;
  }
  
  .section-title-new {
    font-size: 2rem;
  }
  
  /* Mantener productos de 1 en 1 en móviles para mejor visualización */
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .product-actions-premium {
    flex-direction: column;
  }
  
  .story-metrics {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }
  
  .story-images-grid {
    grid-template-columns: 1fr;
  }
  
  .cta-title {
    font-size: 2rem;
  }
  
  .cta-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .cta-button-primary,
  .cta-button-secondary {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .hero {
    min-height: 650px; /* Aún más altura para móviles pequeños */
  }
  
  .hero-title {
    font-size: 1.75rem;
  }
  
  /* Imagen aún más prominente en móviles pequeños */
  .hero-img {
    height: 28em;
    max-width: 100%;
  }
  
  .hero-content {
    gap: 2.5rem;
  }
  
  .trust-section,
  .featured-products-section,
  .categories-showcase-section,
  .story-section-new,
  .final-cta-section {
    padding: 3rem 0;
  }
  
  .section-container {
    padding: 0 1rem;
  }
  
  .trust-item {
    padding: 1.5rem 1rem;
  }
  
  /* Mantener 1 producto por fila en móviles pequeños */
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .product-content-premium {
    padding: 1.5rem;
  }
  
  .story-container-new {
    padding: 0 1rem;
  }
  
  .story-highlight-box {
    flex-direction: column;
    text-align: center;
    padding: 1.5rem;
  }
}

/* Ajuste adicional para móviles muy pequeños (menos de 400px) */
@media (max-width: 400px) {
  .hero-img {
    height: 25em;
  }
  
  /* Mantener productos individuales con buen tamaño */
  .products-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}
</style>
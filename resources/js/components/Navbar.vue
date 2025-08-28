<template>
  <nav class="navbar">
    <DebugAuth />
    <div class="navbar-container">
      <router-link to="/" class="navbar-brand">
        <img src="https://videos.openai.com/vg-assets/assets%2Ftask_01k3hvaxqffgdbba1j02hqpjg7%2F1756167083_img_1.webp?st=2025-08-25T22%3A26%3A23Z&se=2025-08-31T23%3A26%3A23Z&sks=b&skt=2025-08-25T22%3A26%3A23Z&ske=2025-08-31T23%3A26%3A23Z&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skoid=3d249c53-07fa-4ba4-9b65-0bf8eb4ea46a&skv=2019-02-02&sv=2018-11-09&sr=b&sp=r&spr=https%2Chttp&sig=Vfiy7QgrlCLPpYMO4mtaRiMEuEC1igIpnLcq6rUYLgE%3D&az=oaivgprodscus" alt="Logo" class="logo">
      </router-link>

      <!-- Desktop Menu -->
      <div class="navbar-menu" :class="{ 'is-active': isMenuOpen }">
        <router-link to="/" class="navbar-item" @click="closeMenu">Inicio</router-link>
        <router-link to="/products" class="navbar-item" @click="closeMenu">Productos</router-link>

        <div class="navbar-dropdown" @mouseenter="showDropdown" @mouseleave="hideDropdown">
          <span class="navbar-item">Explorar ⏷</span>
          <div class="dropdown-content" v-show="isDropdownOpen">
            <button 
              v-for="category in categories" 
              :key="category.id"
              @click="filterByCategory(category.slug)"
              class="dropdown-item dropdown-button"
            >
              {{ category.name }}
            </button>
          </div>
        </div>

        <!-- Mobile Auth Section (dentro del menú móvil) -->
        <div class="mobile-auth-section" v-if="isMenuOpen">
          <div v-if="isLoggedIn" class="mobile-user-menu">
            <div class="mobile-user-info">
              <div class="mobile-user-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <div class="mobile-user-details">
                <div class="mobile-user-name">{{ currentUser?.nombre }} {{ currentUser?.apellido }}</div>
                <div class="mobile-user-email">{{ currentUser?.email }}</div>
              </div>
            </div>
            <div class="mobile-user-actions">
              <button class="mobile-user-action" @click="goToProfile">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                Mi cuenta
              </button>
              <button class="mobile-user-action" @click="goToOrders">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                  <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                </svg>
                Mis pedidos
              </button>
              <button class="mobile-user-action logout-action" @click="handleLogout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                  <polyline points="16,17 21,12 16,7"/>
                  <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Cerrar sesión
              </button>
            </div>
          </div>
          <div v-else class="mobile-auth-actions">
            <button class="mobile-auth-btn login-btn" @click="openLoginModal">
              Iniciar Sesión
            </button>
            <router-link to="/register" class="mobile-auth-btn register-btn" @click="closeMenu">
              Registrarse
            </router-link>
          </div>
        </div>
      </div>

      <!-- Desktop Actions -->
      <div class="navbar-actions">
        <button class="action-btn search-btn" @click="toggleSearch" title="Buscar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
        </button>

        <button class="action-btn cart-btn" @click="toggleCart" title="Carrito">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
               viewBox="0 0 24 24" fill="none" stroke="currentColor" 
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8" cy="21" r="1"/>
            <circle cx="19" cy="21" r="1"/>
            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57L20.3 9H5.12"/>
          </svg>
          <span class="cart-count" v-if="cartCount > 0">{{ cartCount }}</span>
        </button>

        <!-- Desktop Auth Section -->
        <div class="auth-section desktop-only">
          <!-- Si está logueado -->
          <div v-if="isLoggedIn" class="user-menu" @mouseenter="showUserMenu" @mouseleave="hideUserMenu">
            <button class="user-button">
              <div class="user-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <span class="user-greeting">Hola, {{ userName }}</span>
              <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6,9 12,15 18,9"></polyline>
              </svg>
            </button>

            <div class="user-dropdown" v-show="isUserMenuOpen">
              <div class="user-infomation">
                <div class="user-name2">{{ currentUser?.nombre }} {{ currentUser?.apellido }}</div>
                <div class="user-email2">{{ currentUser?.email }}</div>
              </div>
              <hr class="dropdown-divider">
              <div class="user-actions">
                <button class="user-action-item" @click="goToProfile">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                  Mi cuenta
                </button>
                <button class="user-action-item" @click="goToOrders">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                  </svg>
                  Mis pedidos
                </button>
                <button class="user-action-item" @click="goToSettings">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                  </svg>
                  Configuración
                </button>
                <hr class="dropdown-divider">
                <button class="user-action-item logout-btn" @click="handleLogout">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16,17 21,12 16,7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                  </svg>
                  Cerrar sesión
                </button>
              </div>
            </div>
          </div>

          <!-- Si NO está logueado -->
          <div v-else class="auth-dropdown" @mouseenter="showAuthDropdown" @mouseleave="hideAuthDropdown">
            <button class="auth-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span class="auth-text">Iniciar sesión</span>
            </button>
            <div class="auth-dropdown-content" v-show="isAuthDropdownOpen">
              <div class="auth-info">
                <div class="auth-greeting">¡Hola!</div>
                <div class="auth-subtitle">Entra o regístrate</div>
              </div>
              <div class="auth-actions">
                <button class="auth-action-btn login-btn" @click="openLoginModal">
                  Iniciar Sesión
                </button>
                <router-link to="/register" class="auth-action-btn register-btn">
                  Registrarse
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="menu-toggle" @click="toggleMenu" :class="{ 'is-active': isMenuOpen }">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>

    <!-- Overlay para cerrar menú móvil -->
    <div v-if="isMenuOpen" class="mobile-menu-overlay" @click="closeMenu"></div>

    <!-- Search Bar (Collapsible) -->
    <transition name="search-slide">
      <div class="search-bar" v-show="isSearchOpen">
        <div class="search-container">
          <div class="search-input-wrapper">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            <input
              type="text"
              v-model="searchQuery"
              @keyup.enter="performSearch"
              placeholder="Buscar productos..."
              class="search-input"
              ref="searchInput"
            />
            <button v-if="searchQuery" @click="clearSearch" class="clear-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
          <button @click="performSearch" class="search-submit-btn">
            Buscar
          </button>
        </div>
      </div>
    </transition>

    <!-- Loading overlay cuando se hace logout -->
    <div v-if="authLoading" class="auth-loading">
      <div class="loading-spinner"></div>
    </div>
  </nav>
  
  <!-- Modales y sidebars -->
  <CartSidebar :isOpen="isCartOpen" @close="isCartOpen = false" />
  <LoginModal :isOpen="isLoginModalOpen" @close="closeLoginModal" @login-success="onLoginSuccess" />
</template>

<script>
import { ref, onMounted, nextTick, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cartStore';
import { useAuthStore } from '../stores/authStore';
import CartSidebar from './CartSidebar.vue';
import LoginModal from './Auth/LoginModal.vue';
import '../css/nav.css';

export default {
  name: 'Navbar',
  components: {
    CartSidebar,
    LoginModal,
  },
  setup() {
    const router = useRouter();
    const cartStore = useCartStore();
    const authStore = useAuthStore();
    
    // Estados locales
    const isMenuOpen = ref(false);
    const isDropdownOpen = ref(false);
    const isAuthDropdownOpen = ref(false);
    const isUserMenuOpen = ref(false);
    const isSearchOpen = ref(false);
    const searchQuery = ref('');
    const categories = ref([]);
    const isCartOpen = ref(false);
    const isLoginModalOpen = ref(false);
    const searchInput = ref(null);

    // Computed properties desde el authStore
    const isLoggedIn = computed(() => authStore.isLoggedIn);
    const currentUser = computed(() => authStore.currentUser);
    const authLoading = computed(() => authStore.loading);
    
    // Computed para obtener solo el nombre de pila
    const userName = computed(() => {
      if (!currentUser.value) return '';
      return currentUser.value.nombre || 'Usuario';
    });

    // Función para cerrar el menú móvil
    const closeMenu = () => {
      isMenuOpen.value = false;
    };

    // Filtrar por categoría
    const filterByCategory = (categorySlug) => {
      isDropdownOpen.value = false;
      closeMenu(); // Cerrar menú móvil también
      
      if (router.currentRoute.value.name !== 'Products') {
        router.push({
          name: 'Products',
          query: { category: categorySlug }
        });
      } else {
        router.push({
          name: 'Products',
          query: { 
            ...router.currentRoute.value.query,
            category: categorySlug 
          }
        });
      }
    };

    // Función de búsqueda
    const performSearch = () => {
      if (searchQuery.value.trim()) {
        isSearchOpen.value = false;
        closeMenu(); // Cerrar menú móvil también
        
        if (router.currentRoute.value.name !== 'Products') {
          router.push({
            name: 'Products',
            query: { search: searchQuery.value.trim() }
          });
        } else {
          router.push({
            name: 'Products',
            query: { 
              ...router.currentRoute.value.query,
              search: searchQuery.value.trim() 
            }
          });
        }
        
        searchQuery.value = '';
      }
    };

    // Funciones existentes
    const toggleMenu = () => {
      isMenuOpen.value = !isMenuOpen.value;
      // Cerrar otros menús cuando se abre el móvil
      if (isMenuOpen.value) {
        isDropdownOpen.value = false;
        isAuthDropdownOpen.value = false;
        isUserMenuOpen.value = false;
        isSearchOpen.value = false;
      }
    };

    const showDropdown = () => {
      isDropdownOpen.value = true;
    };

    const hideDropdown = () => {
      isDropdownOpen.value = false;
    };

    const showAuthDropdown = () => {
      isAuthDropdownOpen.value = true;
    };

    const hideAuthDropdown = () => {
      isAuthDropdownOpen.value = false;
    };

    const showUserMenu = () => {
      isUserMenuOpen.value = true;
    };

    const hideUserMenu = () => {
      isUserMenuOpen.value = false;
    };

    const toggleSearch = async () => {
      isSearchOpen.value = !isSearchOpen.value;
      closeMenu(); // Cerrar menú móvil
      if (isSearchOpen.value) {
        await nextTick();
        if (searchInput.value) {
          searchInput.value.focus();
        }
      }
    };

    const clearSearch = () => {
      searchQuery.value = '';
      if (searchInput.value) {
        searchInput.value.focus();
      }
    };

    const toggleCart = () => {
      isCartOpen.value = !isCartOpen.value;
      closeMenu(); // Cerrar menú móvil
    };

    const openLoginModal = () => {
      isLoginModalOpen.value = true;
      isAuthDropdownOpen.value = false;
      closeMenu(); // Cerrar menú móvil
    };

    const closeLoginModal = () => {
      isLoginModalOpen.value = false;
    };

    const handleLogout = async () => {
      try {
        await authStore.logout();
        isUserMenuOpen.value = false;
        closeMenu(); // Cerrar menú móvil
        if (router.currentRoute.value.path !== '/') {
          router.push('/');
        }
      } catch (error) {
        console.error('Error al cerrar sesión:', error);
      }
    };

    const onLoginSuccess = () => {
      closeLoginModal();
      console.log('Login exitoso!');
    };

    const goToProfile = () => {
      router.push('/account'); 
      isUserMenuOpen.value = false;
      closeMenu(); // Cerrar menú móvil
    };

    /**
     * Navegar a la sección de pedidos del usuario
     * Redirige al AccountPage con la tab de pedidos activa
     */
    const goToOrders = () => {
      // Navegamos al AccountPage con un query parameter para abrir la tab de pedidos
      router.push('/account?tab=orders');
      isUserMenuOpen.value = false;
      closeMenu(); // Cerrar menú móvil
    };

    const goToSettings = () => {
      router.push('/settings');
      isUserMenuOpen.value = false;
      closeMenu(); // Cerrar menú móvil
    };

    const fetchCategories = async () => {
      try {
        const response = await axios.get('/categories');
        categories.value = response.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
        categories.value = [
          { id: 1, name: 'Electrónicos', slug: 'electronicos' },
          { id: 2, name: 'Ropa', slug: 'ropa' },
          { id: 3, name: 'Hogar', slug: 'hogar' },
          { id: 4, name: 'Deportes', slug: 'deportes' }
        ];
      }
    };

    const cartCount = computed(() => cartStore.itemCount);

    onMounted(() => {
      cartStore.loadFromLocalStorage();
      fetchCategories();
    });

    return {
      // Estados
      isMenuOpen,
      isDropdownOpen,
      isAuthDropdownOpen,
      isUserMenuOpen,
      isSearchOpen,
      searchQuery,
      categories,
      cartCount,
      searchInput,
      isCartOpen,
      isLoginModalOpen,
      
      // Estados de auth
      isLoggedIn,
      currentUser,
      authLoading,
      userName,
      
      // Funciones
      toggleMenu,
      closeMenu,
      showDropdown,
      hideDropdown,
      showAuthDropdown,
      hideAuthDropdown,
      showUserMenu,
      hideUserMenu,
      toggleSearch,
      clearSearch,
      performSearch,
      filterByCategory,
      toggleCart,
      openLoginModal,
      closeLoginModal,
      handleLogout,
      onLoginSuccess,
      goToProfile,
      goToOrders,
      goToSettings,
    };
  }
}
</script>

<style scoped>

</style>
<template>
  <nav class="navbar">

    <DebugAuth />
    <div class="navbar-container">

      <router-link to="/" class="navbar-brand">
        <img src="@img/logo.png" alt="Logo" class="logo">
      </router-link>


      <div class="navbar-menu" :class="{ 'is-active': isMenuOpen }">
        <router-link to="/" class="navbar-item">Inicio</router-link>
        <router-link to="/products" class="navbar-item">Productos</router-link>

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
      </div>

  
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

        <!-- 🔥 AUTH SECTION - MODERNIZADO -->
        <div class="auth-section">
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

            <!-- Dropdown para usuario logueado -->
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
        <button class="menu-toggle" @click="toggleMenu">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
      </div>
    </div>

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

    // 🔥 NUEVA FUNCIÓN PARA FILTRAR POR CATEGORÍA
    const filterByCategory = (categorySlug) => {
      // Cerrar el dropdown
      isDropdownOpen.value = false;
      
      // Si no estamos en la página de productos, navegar allí con el filtro
      if (router.currentRoute.value.name !== 'Products') {
        router.push({
          name: 'Products',
          query: { category: categorySlug }
        });
      } else {
        // Si ya estamos en productos, solo actualizar la query
        router.push({
          name: 'Products',
          query: { 
            ...router.currentRoute.value.query,
            category: categorySlug 
          }
        });
      }
    };

    // 🔥 FUNCIÓN DE BÚSQUEDA CORREGIDA
    const performSearch = () => {
      if (searchQuery.value.trim()) {
        // Cerrar la barra de búsqueda
        isSearchOpen.value = false;
        
        // Si no estamos en la página de productos, navegar allí con la búsqueda
        if (router.currentRoute.value.name !== 'Products') {
          router.push({
            name: 'Products',
            query: { search: searchQuery.value.trim() }
          });
        } else {
          // Si ya estamos en productos, actualizar la query
          router.push({
            name: 'Products',
            query: { 
              ...router.currentRoute.value.query,
              search: searchQuery.value.trim() 
            }
          });
        }
        
        // Limpiar el campo de búsqueda después de buscar
        searchQuery.value = '';
      }
    };

    // Funciones existentes
    const toggleMenu = () => {
      isMenuOpen.value = !isMenuOpen.value;
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
    };

    const openLoginModal = () => {
      isLoginModalOpen.value = true;
      isAuthDropdownOpen.value = false;
    };

    const closeLoginModal = () => {
      isLoginModalOpen.value = false;
    };

    const handleLogout = async () => {
      try {
        await authStore.logout();
        isUserMenuOpen.value = false;
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
};
  const goToOrders = () => {
 
};
    const goToSettings = () => {
      router.push('/settings');
      isUserMenuOpen.value = false;
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

 const cartCount = computed(() => cartStore.itemCount)

onMounted(() => {
  cartStore.loadFromLocalStorage()
  fetchCategories()
})

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
      showDropdown,
      hideDropdown,
      showAuthDropdown,
      hideAuthDropdown,
      showUserMenu,
      hideUserMenu,
      toggleSearch,
      clearSearch,
      performSearch, // 🔥 Función corregida
      filterByCategory, // 🔥 Nueva función
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
.navbar {
  background: linear-gradient(135deg, #388E3C 0%, #2E7D32 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: sticky;
  top: 0;
  z-index: 1000;
  backdrop-filter: blur(10px);
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 90px;
}

.navbar-brand {
  display: flex;
  align-items: center;
  text-decoration: none;
}

.logo {
  width: 90;
  height: 90px;
  object-fit: cover;
  border-radius: 8px;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 30px;
}

.navbar-item {
  color: #F5F5DC;
  text-decoration: none;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 25px;
  transition: all 0.3s ease;
  position: relative;
  font-size: 15px;
}

.navbar-item:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-1px);
}

.navbar-item.router-link-active {
  background: rgba(255, 255, 255, 0.25);
  font-weight: 600;
}

.navbar-dropdown {
  position: relative;
}

.dropdown-content {
  position: absolute;
  top: calc(100% + 10px);
  left: 0;
  background: white;
  color: #333;
  min-width: 200px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  display: block;
  padding: 12px 20px;
  color: #333;
  text-decoration: none;
  transition: background-color 0.3s ease;
  font-size: 14px;
}

.dropdown-item:hover {
  background: #f8f9fa;
  color: #388E3C;
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.action-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(255, 255, 255, 0.2);
  color: white;
  padding: 10px;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  position: relative;
}

.action-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  transform: translateY(-2px);
}



auth-section {
  position: relative;
}

/* Estilos para usuario logueado */
.user-menu {
  position: relative;
}

.user-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #374151;
}

.user-button:hover {
  background: #f3f4f6;
}

.user-avatar {
  width: 32px;
  height: 32px;
  background: #3b82f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.user-greeting {
  font-weight: 500;
  white-space: nowrap;
}

.dropdown-arrow {
  transition: transform 0.2s ease;
}

.user-menu:hover .dropdown-arrow {
  transform: rotate(180deg);
}

.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  padding: 0.5rem 0;
  min-width: 220px;
  z-index: 1000;
  margin-top: 0.5rem;
}

.user-infomation {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #e5e7eb;

}

.user-name2 {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
}

.user-email2 {
  font-size: 0.875rem;
  color: #6b7280;
}

.dropdown-divider {
  border: none;
  height: 1px;
  background: #e5e7eb;
  margin: 0.5rem 0;
}

.user-actions {
  padding: 0.5rem 0;
}

.user-action-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  border: none;
  background: none;
  color: #374151;
  cursor: pointer;
  transition: background 0.2s ease;
  font-size: 0.875rem;
  text-align: left;
}

.user-action-item:hover {
  background: #f9fafb;
}

.user-action-item.logout-btn {
  color: #dc2626;
}

.user-action-item.logout-btn:hover {
  background: #fef2f2;
}

/* Estilos para usuario NO logueado */
.auth-dropdown {
  position: relative;
}

.auth-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #374151;
}

.auth-btn:hover {
  background: #f3f4f6;
}

.auth-text {
  font-weight: 500;
  white-space: nowrap;
}

.auth-dropdown-content {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  padding: 1rem;
  min-width: 200px;
  z-index: 1000;
  margin-top: 0.5rem;
}

.auth-info {
  margin-bottom: 1rem;
}

.auth-greeting {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
}

.auth-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
}

.auth-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.auth-action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
  text-decoration: none;
  text-align: center;
}

.login-btn {
  background: #3b82f6;
  color: white;
}

.login-btn:hover {
  background: #2563eb;
}

.register-btn {
  background: #f3f4f6;
  color: #374151;
}

.register-btn:hover {
  background: #e5e7eb;
}

/* Loading spinner */
.auth-loading {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
  .user-greeting,
  .auth-text {
    display: none;
  }
  
  .user-button,
  .auth-btn {
    padding: 0.5rem;
  }
  
  .navbar-menu {
    display: none;
  }
  
  .menu-toggle {
    display: block;
  }
}

/* Dropdown transitions */
.user-dropdown,
.auth-dropdown-content {
  transform: translateY(-10px);
  opacity: 0;
  transition: all 0.2s ease;
  pointer-events: none;
}

.user-menu:hover .user-dropdown,
.auth-dropdown:hover .auth-dropdown-content {
  transform: translateY(0);
  opacity: 1;
  pointer-events: auto;
}
.cart-count {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ff4757;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 11px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
}
.menu-toggle {
  display: none;
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}

.menu-toggle:hover {
  background: rgba(255, 255, 255, 0.1);
}

.search-bar {
  background: rgba(255, 255, 255, 0.1);
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
}

.search-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 15px 20px;
  display: flex;
  gap: 12px;
  align-items: center;
}

.search-input-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 15px;
  color: #999;
  z-index: 1;
}

.search-input {
  width: 100%;
  padding: 12px 15px 12px 45px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 25px;
  font-size: 16px;
  outline: none;
  background: rgba(255, 255, 255, 0.95);
  color: #333;
  transition: all 0.3s ease;
}

.search-input:focus {
  border-color: rgba(255, 255, 255, 0.6);
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

.search-input::placeholder {
  color: #999;
}

.clear-btn {
  position: absolute;
  right: 15px;
  background: none;
  border: none;
  color: #999;
  cursor: pointer;
  padding: 2px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.clear-btn:hover {
  background: #f0f0f0;
  color: #666;
}

.search-submit-btn {
  background: white;
  color: #388E3C;
  border: none;
  padding: 12px 24px;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.search-submit-btn:hover {
  background: #f8f9fa;
  transform: translateY(-1px);
}

/* Animaciones */
.search-slide-enter-active,
.search-slide-leave-active {
  transition: all 0.3s ease;
  transform-origin: top;
}

.search-slide-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.search-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Responsive */
@media (max-width: 768px) {
  .navbar-container {
    height: 60px;
  }

  .navbar-menu {
    position: fixed;
    top: 60px;
    left: -100%;
    width: 100%;
    height: calc(100vh - 60px);
    background: linear-gradient(135deg, #388E3C 0%, #2E7D32 100%);
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding-top: 50px;
    transition: left 0.3s ease;
    backdrop-filter: blur(10px);
  }

  .navbar-menu.is-active {
    left: 0;
  }

  .menu-toggle {
    display: block;
  }

  .navbar-item {
    font-size: 18px;
    margin: 10px 0;
  }

  .logo {
    width: 40px;
    height: 40px;
  }

  .action-btn {
    width: 40px;
    height: 40px;
  }

  .navbar-actions {
    gap: 8px;
  }

  .search-container {
    flex-direction: column;
    gap: 15px;
  }

  .search-input-wrapper {
    width: 100%;
  }

  .search-submit-btn {
    width: 100%;
  }

  .dropdown-content,
  .auth-dropdown-content {
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    min-width: auto;
    margin: 0;
    border-radius: 0;
  }
}

@media (max-width: 480px) {
  .navbar-container {
    padding: 0 15px;
  }

  .navbar-actions {
    gap: 5px;
  }

  .action-btn {
    width: 36px;
    height: 36px;
  }

  .search-container {
    padding: 12px 15px;
  }
}
</style>
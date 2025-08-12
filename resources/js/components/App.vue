<template>
  <div id="app">
    <!-- Solo mostrar navbar y footer si NO estamos en rutas de admin  y no mostrar el nav en el la pagina de resgistro-->
    <template v-if="!isAdminRoute && !$route.meta.hideNav">
      <Navbar />
    </template>
    
    <main :class="{ 'admin-main': isAdminRoute, 'public-main': !isAdminRoute  }">
      <router-view />
    </main>
    
    <template v-if="!isAdminRoute">
      <Footer />
    </template>
  </div>
</template>

<script>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'

export default {
  name: 'App',
  components: {
    Navbar,
    Footer
  },
  setup() {
    const route = useRoute()
    const authStore = useAuthStore()
    
    // Verificar si estamos en una ruta de admin
    const isAdminRoute = computed(() => {
      return route.path.startsWith('/admin')
    })
     const Isregister = computed(() => {
      return route.path.startsWith('//register')
    })
    
    onMounted(async () => {
      // Inicializar autenticación al montar la app
      await authStore.initAuth()
    })
    
    return {
      authStore,
      isAdminRoute
    }
  }
}
</script>

<style>
/* Estilos globales */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', sans-serif;
  line-height: 1.6;
  color: #333;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Main para páginas públicas */
.public-main {
  flex: 1;
}

/* Main para páginas de admin */
.admin-main {
  padding: 0; /* Sin padding para que AdminLayout maneje todo el espacio */
  margin: 0;
}
</style>
<template>
  <q-layout view="hhh Lpr fFf" class="bg-white">
    <q-header elevated class="q-py-md text-white bg-teal-8">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          @click="toggleLeftDrawer"
          aria-label="Menu"
          icon="menu"
        />
        
        <q-toolbar-title>
          Artesanias de la Amazonia - Admin Dashboard
        </q-toolbar-title>
        
        <!-- Botón de logout -->
        <q-btn 
          flat
          icon="logout"
          @click="logout"
          label="Salir"
        />
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-grey-2"
    >
      <q-list>
        <q-item-label header>Panel Administrativo</q-item-label>
        
        <!-- Dashboard -->
        <q-item 
          clickable
          @click="setCurrentView('dashboard')"
          :class="{ 'bg-teal-1 text-teal-8': currentView === 'dashboard' }"
        >
          <q-item-section avatar>
            <q-icon name="dashboard" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Productos -->
        <q-item 
          clickable
          @click="setCurrentView('productos')"
          :class="{ 'bg-teal-1 text-teal-8': currentView === 'productos' }"
        >
          <q-item-section avatar>
            <q-icon name="inventory" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Productos</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Categorías -->
        <q-item 
          clickable
          @click="setCurrentView('categorias')"
          :class="{ 'bg-teal-1 text-teal-8': currentView === 'categorias' }"
        >
          <q-item-section avatar>
            <q-icon name="category" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Categorías</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Usuarios -->
        <q-item 
          clickable
          @click="setCurrentView('usuarios')"
          :class="{ 'bg-teal-1 text-teal-8': currentView === 'usuarios' }"
        >
          <q-item-section avatar>
            <q-icon name="people" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Usuarios</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Pedidos -->
        <q-item 
          clickable
          @click="setCurrentView('pedidos')"
          :class="{ 'bg-teal-1 text-teal-8': currentView === 'pedidos' }"
        >
          <q-item-section avatar>
            <q-icon name="shopping_cart" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Pedidos</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <q-page class="q-pa-md">
        <!-- Componente dinámico basado en la vista actual -->
        <component :is="currentComponent" />
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAdminAuthStore } from '@/stores/adminAuthStore'

// Importar todos los componentes administrativos
import AdminDashboard from './AdminDashboard.vue'
import AdminProductos from './AdminProductos.vue'
import AdminCategorias from './AdminCategorias.vue'
import AdminUsuarios from './AdminUsuarios.vue'
import AdminPedidos from './AdminPedidos.vue'

const leftDrawerOpen = ref(false)
const router = useRouter()
const adminAuthStore = useAdminAuthStore()

// Estado para manejar la vista actual
const currentView = ref('dashboard')

// Mapeo de vistas a componentes
const viewComponents = {
  dashboard: AdminDashboard,
  productos: AdminProductos,
  categorias: AdminCategorias,
  usuarios: AdminUsuarios,
  pedidos: AdminPedidos
}

// Componente actual computado
const currentComponent = computed(() => {
  return viewComponents[currentView.value] || AdminDashboard
})

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function setCurrentView(view) {
  currentView.value = view
  // Opcional: actualizar la URL sin recargar la página
  const routeName = `admin-${view}`
  if (router.currentRoute.value.name !== routeName) {
    router.replace({ name: routeName })
  }
}

async function logout() {
  await adminAuthStore.logout()
  router.push('/admin/login')
}

// Sincronizar vista actual con la ruta al cargar
const initializeView = () => {
  const routeName = router.currentRoute.value.name
  if (routeName && routeName.startsWith('admin-')) {
    const view = routeName.replace('admin-', '')
    if (viewComponents[view]) {
      currentView.value = view
    }
  }
}

// Inicializar vista al montar
initializeView()
</script>

<style scoped>
.q-item--active {
  background-color: rgba(0, 150, 136, 0.1) !important;
  color: #00695c !important;
}
</style>
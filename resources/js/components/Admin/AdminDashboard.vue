<template>
  <q-layout view="hhh Lpr fFf" class="bg-white">
    <q-header elevated class="q-py-md text-white bg-blue-8">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          @click="toggleLeftDrawer"
          aria-label="Menu"
          icon="bi-list"
        />
        
        <q-toolbar-title>
          Artesanias de la Amazonia - Admin Dashboard
        </q-toolbar-title>
        
        
        <q-btn 
          flat
          icon="bi-box-arrow-right"
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
        
        <!-- Dashboard Home -->
        <q-item 
          clickable
          :to="{ name: 'admin-dashboard-home' }"
          exact-active-class="bg-teal-1 text-teal-8"
          @click="closeDrawerOnMobile"
        >
          <q-item-section avatar>
            <q-icon name="bi-house" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
            <q-item-label caption>Vista general</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Productos -->
        <q-item 
          clickable
          :to="{ name: 'admin-productos' }"
          exact-active-class="bg-teal-1 text-teal-8"
          @click="closeDrawerOnMobile"
        >
          <q-item-section avatar>
            <q-icon name="bi-shop" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Productos</q-item-label>
            <q-item-label caption>Gestión de productos</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Categorías -->
        <q-item 
          clickable
          :to="{ name: 'admin-categorias' }"
          exact-active-class="bg-teal-1 text-teal-8"
          @click="closeDrawerOnMobile"
        >
          <q-item-section avatar>
            <q-icon name="bi-folder" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Categorías</q-item-label>
            <q-item-label caption>Gestión de categorías</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Usuarios -->
        <q-item 
          clickable
          :to="{ name: 'admin-usuarios' }"
          exact-active-class="bg-teal-1 text-teal-8"
          @click="closeDrawerOnMobile"
        >
          <q-item-section avatar>
            <q-icon name="bi-people" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Usuarios</q-item-label>
            <q-item-label caption>Gestión de usuarios</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Clientes -->
        <q-item 
          clickable
          :to="{ name: 'admin-clientes' }"
          exact-active-class="bg-teal-1 text-teal-8"
          @click="closeDrawerOnMobile"
        >
          <q-item-section avatar>
            <q-icon name="bi-person-heart" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Clientes</q-item-label>
            <q-item-label caption>Gestión de clientes</q-item-label>
          </q-item-section>
        </q-item>

        <!-- Pedidos -->
        <q-item 
          clickable
          :to="{ name: 'admin-pedidos' }"
          exact-active-class="bg-teal-1 text-teal-8"
          @click="closeDrawerOnMobile"
        >
          <q-item-section avatar>
            <q-icon name="bi-cart" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Pedidos</q-item-label>
            <q-item-label caption>Gestión de pedidos</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <!-- Aquí se renderizan las diferentes páginas del admin -->
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { useAdminAuthStore } from '@/stores/adminAuthStore'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'

const $q = useQuasar()
const leftDrawerOpen = ref(false)
const router = useRouter()
const adminAuthStore = useAdminAuthStore()

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

async function logout() {
  try {
    await adminAuthStore.logout()
    $q.notify({
      type: 'positive',
      message: 'Sesión cerrada correctamente'
    })
    router.push('/admin/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
    $q.notify({
      type: 'negative',
      message: 'Error al cerrar sesión'
    })
  }
}

function closeDrawerOnMobile() {
  // Cerrar el drawer en dispositivos móviles después de navegar
  if ($q.screen.lt.md) {
    leftDrawerOpen.value = false
  }
}
</script>
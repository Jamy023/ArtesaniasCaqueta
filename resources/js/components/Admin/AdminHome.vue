<template>
  <q-page class="q-pa-md">
    <div class="text-h4 q-mb-md text-teal-8">
      Bienvenido al Panel Administrativo
    </div>
    
    <div class="row q-gutter-md">
      <!-- Tarjeta de Productos -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-blue-1">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Productos</div>
                <div class="text-h4 text-blue-8">{{ stats.productos }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="inventory" size="48px" class="text-blue-8" />
              </div>
            </div>
          </q-card-section>
          <q-card-actions>
            <q-btn 
              flat 
              color="blue-8" 
              label="Ver productos"
              :to="{ name: 'admin-productos' }"
            />
          </q-card-actions>
        </q-card>
      </div>

      <!-- Tarjeta de Usuarios -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-green-1">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Usuarios</div>
                <div class="text-h4 text-green-8">{{ stats.usuarios }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="people" size="48px" class="text-green-8" />
              </div>
            </div>
          </q-card-section>
          <q-card-actions>
            <q-btn 
              flat 
              color="green-8" 
              label="Ver usuarios"
              :to="{ name: 'admin-usuarios' }"
            />
          </q-card-actions>
        </q-card>
      </div>

      <!-- Tarjeta de Pedidos -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-orange-1">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Pedidos</div>
                <div class="text-h4 text-orange-8">{{ stats.pedidos }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="shopping_cart" size="48px" class="text-orange-8" />
              </div>
            </div>
          </q-card-section>
          <q-card-actions>
            <q-btn 
              flat 
              color="orange-8" 
              label="Ver pedidos"
              :to="{ name: 'admin-pedidos' }"
            />
          </q-card-actions>
        </q-card>
      </div>

      <!-- Tarjeta de Categorías -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-purple-1">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Categorías</div>
                <div class="text-h4 text-purple-8">{{ stats.categorias }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="category" size="48px" class="text-purple-8" />
              </div>
            </div>
          </q-card-section>
          <q-card-actions>
            <q-btn 
              flat 
              color="purple-8" 
              label="Ver categorías"
              :to="{ name: 'admin-categorias' }"
            />
          </q-card-actions>
        </q-card>
      </div>
    </div>

    <!-- Actividad reciente -->
    <div class="q-mt-xl">
      <div class="text-h5 q-mb-md">Actividad Reciente</div>
      <q-card>
        <q-card-section>
          <q-list separator>
            <q-item v-for="activity in recentActivity" :key="activity.id">
              <q-item-section avatar>
                <q-icon :name="activity.icon" :color="activity.color" />
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ activity.title }}</q-item-label>
                <q-item-label caption>{{ activity.description }}</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label caption>{{ activity.time }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
// import axios from 'axios' // Descomenta cuando tengas la API

const stats = ref({
  productos: 0,
  usuarios: 0,
  pedidos: 0,
  categorias: 0
})

const recentActivity = ref([
  {
    id: 1,
    icon: 'add_shopping_cart',
    color: 'green',
    title: 'Nuevo pedido recibido',
    description: 'Pedido #1234 por $150.000',
    time: 'Hace 2 horas'
  },
  {
    id: 2,
    icon: 'person_add',
    color: 'blue',
    title: 'Nuevo usuario registrado',
    description: 'Juan Pérez se registró',
    time: 'Hace 4 horas'
  },
  {
    id: 3,
    icon: 'inventory_2',
    color: 'orange',
    title: 'Producto actualizado',
    description: 'Hamaca Wayuu - stock actualizado',
    time: 'Hace 6 horas'
  }
])

onMounted(async () => {
  await loadStats()
})

async function loadStats() {
  try {
    // Aquí harías las llamadas a tu API para obtener las estadísticas reales
    // const response = await axios.get('/admin/stats')
    // stats.value = response.data
    
    // Por ahora, datos de ejemplo:
    stats.value = {
      productos: 45,
      usuarios: 128,
      pedidos: 23,
      categorias: 8
    }
  } catch (error) {
    console.error('Error cargando estadísticas:', error)
  }
}
</script>
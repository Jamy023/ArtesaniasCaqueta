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
                <q-icon name="bi-box" size="48px" class="text-blue-8" />
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
                <div class="text-h6">Clientes</div>
                <div class="text-h4 text-green-8">{{ stats.usuarios }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="bi-people" size="48px" class="text-green-8" />
              </div>
            </div>
          </q-card-section>
          <q-card-actions>
            <q-btn 
              flat 
              color="green-8" 
              label="Ver Clientes"
              :to="{ name: 'admin-clientes' }"
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
                <q-icon name="bi-cart" size="48px" class="text-orange-8" />
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
                <q-icon name="bi-tags" size="48px" class="text-purple-8" />
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


  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'

import api from '../../axios'

const stats = ref({
  productos: 0,
  usuarios: 0,
  pedidos: 0,
  categorias: 0
})





const fetchProductos = async () => {
  try {
    const res = await api.get('/products')
    stats.value.productos = res.data.data ? res.data.data.length : 0
  } catch (error) {
    console.error('Error al cargar estadísticas:', error)
  }
}

const fetchClientes = async () => {
  try{
    const res = await api.get('/clientes')
    
    stats.value.usuarios = res.data.data ? res.data.data.length : 0

  }catch(error){
    console.error('Error al cargar los usuarios:', error)
  }
}

const fetchOrders = async ()=>{
  try{
    const res = await api.get('/admin/orders');
    // El endpoint devuelve { success: true, orders: [...] }
    stats.value.pedidos = res.data.orders ? res.data.orders.length : 0

  }catch(error){
    console.error('Error al cargar los pedidos:', error)
  }
}

const fetchCategories = async ()=>{
  try{
    const res = await api.get('/categories');
    stats.value.categorias = res.data.length

  }catch(error){
    console.error('Error al cargar las categorias:', error)
  }
}


const loadStats = async () => {
  await Promise.all([
    fetchProductos(),
    fetchClientes(),
    fetchOrders(),
    fetchCategories()
  ])
}

onMounted(async () => {
  await loadStats()
})
</script>
<template>
  <div>
    <div class="row items-center justify-between q-mb-lg">
      <div class="col">
        <br>
        <br>
        <h4 class="text-h4 q-my-none">Gestión de Pedidos</h4>
        <p class="text-grey-6">Administra los pedidos realizados por los clientes</p>
      </div>
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <!-- Búsqueda por número de pedido -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-input
              v-model="searchQuery"
              placeholder="Buscar por número de pedido..."
              outlined
              dense
              clearable
              @input="applyFilters"
            >
              <template v-slot:prepend>
                <q-icon name="bi-search" />
              </template>
            </q-input>
          </div>

          <!-- Filtro por estado -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-select
              v-model="statusFilter"
              :options="statusOptions"
              placeholder="Todos los estados"
              outlined
              dense
              clearable
              emit-value
              map-options
              @update:model-value="applyFilters"
            />
          </div>

          <!-- Filtro por fecha desde -->
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-input
              v-model="dateFromFilter"
              type="date"
              label="Desde"
              outlined
              dense
              @update:model-value="applyFilters"
            >
              <template v-slot:prepend>
                <q-icon name="bi-calendar-date" />
              </template>
            </q-input>
          </div>

          <!-- Filtro por fecha hasta -->
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-input
              v-model="dateToFilter"
              type="date"
              label="Hasta"
              outlined
              dense
              @update:model-value="applyFilters"
            >
              <template v-slot:prepend>
                <q-icon name="bi-calendar-date" />
              </template>
            </q-input>
          </div>

          <!-- Botón limpiar filtros -->
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-btn
              flat
              icon="clear"
              label="Limpiar"
              @click="clearFilters"
              :disable="!hasActiveFilters"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabla de pedidos -->
    <q-card flat bordered>
      <q-card-section>
        <q-table
          :rows="filteredOrders"
          :columns="columns"
          row-key="id"
          :loading="loading"
          flat
          bordered
          :pagination="pagination"
          @request="onRequest"
        >
          <!-- Número de pedido -->
          <template v-slot:body-cell-order_number="props">
            <q-td :props="props">
              <div class="text-weight-medium text-primary">
                {{ props.row.order_number }}
              </div>
            </q-td>
          </template>

          <!-- Cliente -->
          <template v-slot:body-cell-customer="props">
            <q-td :props="props">
              <div>
                <div class="text-weight-medium">
                  {{ getCustomerName(props.row) }}
                </div>
                <div class="text-caption text-grey-6">
                  {{ getCustomerEmail(props.row) }}
                </div>
              </div>
            </q-td>
          </template>

          <!-- Total -->
          <template v-slot:body-cell-total="props">
            <q-td :props="props">
              <span class="text-weight-medium">
                {{ formatPrice(props.row.total_amount) }}
              </span>
            </q-td>
          </template>

          <!-- Estado -->
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-chip
                :label="getStatusLabel(props.row.status)"
                :color="getStatusColor(props.row.status)"
                text-color="white"
                size="sm"
              />
            </q-td>
          </template>

          <!-- Fecha -->
          <template v-slot:body-cell-date="props">
            <q-td :props="props">
              <div>
                <div class="text-weight-medium">
                  {{ formatDate(props.row.created_at) }}
                </div>
                <div class="text-caption text-grey-6">
                  {{ formatTime(props.row.created_at) }}
                </div>
              </div>
            </q-td>
          </template>

          <!-- Items/Productos -->
          <template v-slot:body-cell-items="props">
            <q-td :props="props">
              <div class="text-caption">
                {{ getItemsCount(props.row.items) }} producto(s)
              </div>
              <q-tooltip>
                <div v-for="item in props.row.items" :key="item.id" class="q-py-xs">
                  {{ item.quantity }}x {{ item.name }} - {{ formatPrice(item.price) }}
                </div>
              </q-tooltip>
            </q-td>
          </template>

          <!-- Acciones -->
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <div class="q-gutter-xs">
                <q-btn
                  flat
                  round
                  icon="bi-eye"
                  size="sm"
                  color="info"
                  @click="openOrderDialog('view', props.row)"
                >
                  <q-tooltip>Ver detalles</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="bi-pencil"
                  size="sm"
                  color="primary"
                  @click="openOrderDialog('edit', props.row)"
                  :disable="!canEditOrder(props.row)"
                >
                  <q-tooltip>Editar estado</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </template>

          <!-- Mensaje cuando no hay datos -->
          <template v-slot:no-data>
            <div class="full-width row flex-center text-grey-5 q-gutter-sm">
              <q-icon size="2em" name="bi-receipt" />
              <span>No se encontraron pedidos</span>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal para ver/editar pedido -->
    <q-dialog 
      v-model="orderDialog" 
      persistent 
      transition-show="slide-up" 
      transition-hide="slide-down"
    >
      <q-card class="full-width" style="max-width: 800px">
        <q-card-section class="row items-center q-pb-none bg-primary text-white">
          <div class="text-h5">
            <q-icon 
              :name="dialogMode === 'edit' ? 'bi-pencil' : 'bi-eye'" 
              class="q-mr-sm" 
            />
            {{ dialogTitle }}
          </div>
          <q-space />
          <q-btn icon="bi-x" flat round dense @click="closeOrderDialog" />
        </q-card-section>

        <q-card-section class="q-pa-lg" v-if="selectedOrder">
          <div class="row q-gutter-lg">
            <!-- Información del pedido -->
            <div class="col-md-6 col-xs-12">
              <div class="text-h6 text-primary q-mb-md">
                <q-icon name="bi-receipt" class="q-mr-sm" />
                Información del Pedido
              </div>

              <div class="q-gutter-sm">
                <q-input
                  :model-value="selectedOrder.order_number"
                  label="Número de Pedido"
                  outlined
                  readonly
                />
                
                <q-input
                  :model-value="formatPrice(selectedOrder.total_amount)"
                  label="Total"
                  outlined
                  readonly
                />

                <q-select
                  v-model="orderForm.status"
                  :options="editableStatusOptions"
                  label="Estado"
                  outlined
                  emit-value
                  map-options
                  :readonly="dialogMode === 'view'"
                />

                <q-input
                  v-model="orderForm.notes"
                  label="Notas administrativas"
                  type="textarea"
                  rows="3"
                  outlined
                  :readonly="dialogMode === 'view'"
                />

                <q-input
                  :model-value="selectedOrder.payment_method || 'No especificado'"
                  label="Método de Pago"
                  outlined
                  readonly
                />

                <q-input
                  :model-value="formatDate(selectedOrder.created_at) + ' ' + formatTime(selectedOrder.created_at)"
                  label="Fecha del Pedido"
                  outlined
                  readonly
                />
              </div>
            </div>

            <!-- Información del cliente -->
            <div class="col-md-6 col-xs-12">
              <div class="text-h6 text-primary q-mb-md">
                <q-icon name="bi-person" class="q-mr-sm" />
                Información del Cliente
              </div>

              <div class="q-gutter-sm" v-if="selectedOrder.customer_data">
                <q-input
                  :model-value="getCustomerName(selectedOrder)"
                  label="Nombre Completo"
                  outlined
                  readonly
                />
                
                <q-input
                  :model-value="selectedOrder.customer_data.email"
                  label="Email"
                  outlined
                  readonly
                />
                
                <q-input
                  :model-value="selectedOrder.customer_data.telefono || 'No especificado'"
                  label="Teléfono"
                  outlined
                  readonly
                />
                
                <q-input
                  :model-value="(selectedOrder.customer_data.tipo_documento || '') + ' ' + (selectedOrder.customer_data.documento || '')"
                  label="Documento"
                  outlined
                  readonly
                />
                
                <q-input
                  :model-value="getCustomerAddress(selectedOrder)"
                  label="Dirección"
                  outlined
                  readonly
                />
              </div>
            </div>
          </div>

          <!-- Productos del pedido -->
          <div class="q-mt-lg">
            <div class="text-h6 text-primary q-mb-md">
              <q-icon name="bi-cart" class="q-mr-sm" />
              Productos del Pedido
            </div>

            <q-table
              :rows="selectedOrder.items || []"
              :columns="itemsColumns"
              row-key="id"
              flat
              bordered
              hide-pagination
              :rows-per-page-options="[0]"
            >
              <template v-slot:body-cell-price="props">
                <q-td :props="props">
                  {{ formatPrice(props.row.price) }}
                </q-td>
              </template>
              
              <template v-slot:body-cell-subtotal="props">
                <q-td :props="props">
                  <span class="text-weight-medium">
                    {{ formatPrice(props.row.price * props.row.quantity) }}
                  </span>
                </q-td>
              </template>
            </q-table>
          </div>

          <!-- Botones de acción -->
          <div class="row q-gutter-sm justify-end q-mt-xl" v-if="dialogMode === 'edit'">
            <q-btn 
              flat 
              label="Cancelar" 
              color="grey-7" 
              @click="closeOrderDialog"
            />
            <q-btn 
              label="Actualizar Pedido"
              color="primary"
              :loading="saving"
              icon-right="save"
              @click="saveOrder"
            />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import api from '../../axios'

const $q = useQuasar()

// Estados reactivos
const loading = ref(false)
const saving = ref(false)
const orders = ref([])
const orderDialog = ref(false)
const dialogMode = ref('view') // 'view', 'edit'
const selectedOrder = ref(null)

// Filtros
const searchQuery = ref('')
const statusFilter = ref(null)
const dateFromFilter = ref('')
const dateToFilter = ref('')

// Formulario de pedido
const orderForm = reactive({
  status: '',
  notes: ''
})

// Paginación
const pagination = ref({
  sortBy: 'created_at',
  descending: true,
  page: 1,
  rowsPerPage: 15,
  rowsNumber: 0
})

// Definir columnas de la tabla
const columns = [
  {
    name: 'order_number',
    label: 'Número',
    field: 'order_number',
    align: 'left',
    sortable: true
  },
  {
    name: 'customer',
    label: 'Cliente',
    field: row => getCustomerName(row),
    align: 'left',
    sortable: false
  },
  {
    name: 'items',
    label: 'Productos',
    field: 'items',
    align: 'center',
    sortable: false
  },
  {
    name: 'total',
    label: 'Total',
    field: 'total_amount',
    align: 'right',
    sortable: true
  },
  {
    name: 'status',
    label: 'Estado',
    field: 'status',
    align: 'center',
    sortable: true
  },
  {
    name: 'date',
    label: 'Fecha',
    field: 'created_at',
    align: 'center',
    sortable: true
  },
  {
    name: 'actions',
    label: 'Acciones',
    field: 'actions',
    align: 'center',
    sortable: false
  }
]

// Columnas para la tabla de items
const itemsColumns = [
  {
    name: 'name',
    label: 'Producto',
    field: 'name',
    align: 'left'
  },
  {
    name: 'quantity',
    label: 'Cantidad',
    field: 'quantity',
    align: 'center'
  },
  {
    name: 'price',
    label: 'Precio Unitario',
    field: 'price',
    align: 'right'
  },
  {
    name: 'subtotal',
    label: 'Subtotal',
    field: row => row.price * row.quantity,
    align: 'right'
  }
]

// Opciones para los filtros
const statusOptions = [
  { label: 'Todos los estados', value: null },
  { label: 'Pendiente', value: 'pending' },
  { label: 'Pagado', value: 'paid' },
  { label: 'Fallido', value: 'failed' },
  { label: 'Cancelado', value: 'cancelled' }
]

const editableStatusOptions = [
  { label: 'Pendiente', value: 'pending' },
  { label: 'Pagado', value: 'paid' },
  { label: 'Fallido', value: 'failed' },
  { label: 'Cancelado', value: 'cancelled' }
]

// Pedidos filtrados
const filteredOrders = computed(() => {
  let filtered = [...orders.value]

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order =>
      order.order_number.toLowerCase().includes(query)
    )
  }

  // Filtrar por estado
  if (statusFilter.value) {
    filtered = filtered.filter(order =>
      order.status === statusFilter.value
    )
  }

  // Filtrar por fecha desde
  if (dateFromFilter.value) {
    const fromDate = new Date(dateFromFilter.value)
    filtered = filtered.filter(order => {
      const orderDate = new Date(order.created_at)
      return orderDate >= fromDate
    })
  }

  // Filtrar por fecha hasta
  if (dateToFilter.value) {
    const toDate = new Date(dateToFilter.value)
    toDate.setHours(23, 59, 59, 999) // Incluir todo el día
    filtered = filtered.filter(order => {
      const orderDate = new Date(order.created_at)
      return orderDate <= toDate
    })
  }

  return filtered
})

// Verificar si hay filtros activos
const hasActiveFilters = computed(() => {
  return searchQuery.value || statusFilter.value || dateFromFilter.value || dateToFilter.value
})

const dialogTitle = computed(() => {
  if (dialogMode.value === 'edit') return 'Editar Pedido'
  return 'Detalles del Pedido'
})

// Cargar pedidos desde la API
const loadOrders = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/orders')
    
    if (response.data?.orders) {
      orders.value = response.data.orders
    } else if (Array.isArray(response.data)) {
      orders.value = response.data
    } else {
      orders.value = []
    }

    $q.notify({
      type: 'positive',
      message: `${orders.value.length} pedidos cargados correctamente`
    })

  } catch (error) {
    console.error('Error loading orders:', error)
    
    let errorMessage = 'Error al cargar pedidos'
    
    if (error.response) {
      if (error.response.status === 401) {
        errorMessage = 'No tienes permisos para acceder a esta información'
      } else if (error.response.status === 403) {
        errorMessage = 'Acceso denegado'
      } else if (error.response.data?.message) {
        errorMessage = error.response.data.message
      }
    } else if (error.request) {
      errorMessage = 'Error de conexión con el servidor'
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage
    })
    orders.value = []
  } finally {
    loading.value = false
  }
}

// Abrir modal de pedido
const openOrderDialog = (mode, order = null) => {
  dialogMode.value = mode
  selectedOrder.value = order
  
  if (order) {
    orderForm.status = order.status
    orderForm.notes = order.notes || ''
  }
  
  orderDialog.value = true
}

// Cerrar modal
const closeOrderDialog = () => {
  orderDialog.value = false
  selectedOrder.value = null
  saving.value = false
}

// Guardar cambios del pedido
const saveOrder = async () => {
  if (!selectedOrder.value) return
  
  saving.value = true
  
  try {
    const response = await api.patch(`/admin/orders/${selectedOrder.value.id}/status`, {
      status: orderForm.status,
      notes: orderForm.notes
    })
    
    // Actualizar pedido en el array local
    const index = orders.value.findIndex(o => o.id === selectedOrder.value.id)
    if (index !== -1) {
      orders.value[index] = { ...orders.value[index], ...orderForm }
    }

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Pedido actualizado correctamente',
      icon: 'check_circle'
    })

    closeOrderDialog()

  } catch (error) {
    console.error('Error saving order:', error)
    
    let errorMessage = 'Error al actualizar el pedido'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage,
      icon: 'error'
    })
  } finally {
    saving.value = false
  }
}

// Aplicar filtros
const applyFilters = () => {
  console.log('Filtros aplicados:', {
    search: searchQuery.value,
    status: statusFilter.value,
    dateFrom: dateFromFilter.value,
    dateTo: dateToFilter.value
  })
}

// Limpiar filtros
const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = null
  dateFromFilter.value = ''
  dateToFilter.value = ''
}

// Manejar solicitudes de paginación/ordenamiento
const onRequest = (props) => {
  pagination.value = props.pagination
}

// Funciones utilitarias
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', { 
    style: 'currency', 
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(price)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-CO')
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('es-CO', { 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Pendiente',
    'paid': 'Pagado', 
    'failed': 'Fallido',
    'cancelled': 'Cancelado'
  }
  return labels[status] || status
}

const getStatusColor = (status) => {
  const colors = {
    'pending': 'warning',
    'paid': 'positive',
    'failed': 'negative', 
    'cancelled': 'grey'
  }
  return colors[status] || 'grey'
}

const getCustomerName = (order) => {
  if (!order.customer_data) return 'No disponible'
  const firstName = order.customer_data.nombre || ''
  const lastName = order.customer_data.apellido || ''
  return `${firstName} ${lastName}`.trim() || 'No disponible'
}

const getCustomerEmail = (order) => {
  return order.customer_data?.email || 'No disponible'
}

const getCustomerAddress = (order) => {
  if (!order.customer_data) return 'No disponible'
  const address = order.customer_data.direccion || ''
  const city = order.customer_data.ciudad || ''
  const department = order.customer_data.departamento || ''
  return `${address}, ${city}, ${department}`.replace(/^,\s*|,\s*$/g, '') || 'No disponible'
}

const getItemsCount = (items) => {
  if (!Array.isArray(items)) return 0
  return items.reduce((total, item) => total + (item.quantity || 0), 0)
}

const canEditOrder = (order) => {
  // Solo permitir editar pedidos que no estén pagados
  return order.status !== 'paid'
}

// Cargar datos al montar el componente
onMounted(() => {
  loadOrders()
})
</script>

<style scoped>
.q-table th {
  font-weight: bold;
}

.q-table td {
  vertical-align: middle;
}

/* Estilos para el modal */
.q-dialog .q-card-section {
  padding: 24px;
}

.q-form .q-field {
  margin-bottom: 16px;
}

/* Transiciones suaves */
.q-btn {
  transition: all 0.2s ease;
}

.q-card {
  transition: box-shadow 0.2s ease;
}

.q-card:hover {
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* Mejorar tabla responsive */
.q-table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Custom chip styles */
.q-chip {
  font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .q-dialog .q-card {
    margin: 0;
    height: 100vh;
    max-height: 100vh;
  }
}
</style>
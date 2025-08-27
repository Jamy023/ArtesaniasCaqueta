<template>
  <div>
    <div class="row items-center justify-between q-mb-lg">
      <div class="col">
        <br>
        <br>
        <h4 class="text-h4 q-my-none">Gestión de Clientes</h4>
        <p class="text-grey-6">Administra los clientes registrados en tu tienda</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="bi-plus-circle"
          label="Nuevo Cliente"
          unelevated
          size="md"
          class="q-px-lg"
          @click="openClientDialog('create')"
        />
      </div>
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <!-- Búsqueda por nombre/email -->
          <div class="col-md-4 col-sm-6 col-xs-12">
            <q-input
              v-model="searchQuery"
              placeholder="Buscar por nombre o email..."
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

          <!-- Filtro por departamento -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-select
              v-model="departmentFilter"
              :options="departmentOptions"
              placeholder="Todos los departamentos"
              outlined
              dense
              clearable
              emit-value
              map-options
              @update:model-value="applyFilters"
            />
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

          <!-- Botón limpiar filtros -->
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-btn
              flat
              icon="bi-x-circle"
              label="Limpiar"
              @click="clearFilters"
              :disable="!hasActiveFilters"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabla de clientes -->
    <q-card flat bordered>
      <q-card-section>
        <q-table
          :rows="filteredClients"
          :columns="columns"
          row-key="id"
          :loading="loading"
          flat
          bordered
          :pagination="pagination"
          @request="onRequest"
        >
          <!-- Información del cliente -->
          <template v-slot:body-cell-client="props">
            <q-td :props="props">
              <div class="row items-center q-gutter-sm">
                <q-avatar
                  size="40px"
                  color="primary"
                  text-color="white"
                >
                  {{ getInitials(props.row.nombre + ' ' + props.row.apellido) }}
                </q-avatar>
                <div>
                  <div class="text-weight-medium">{{ props.row.nombre }} {{ props.row.apellido }}</div>
                  <div class="text-caption text-grey-6">{{ props.row.email }}</div>
                </div>
              </div>
            </q-td>
          </template>

          <!-- Documento -->
          <template v-slot:body-cell-document="props">
            <q-td :props="props">
              <div>
                <div class="text-weight-medium">{{ props.row.tipo_documento }}</div>
                <div class="text-caption text-grey-6">{{ props.row.numero_documento }}</div>
              </div>
            </q-td>
          </template>

          <!-- Ubicación -->
          <template v-slot:body-cell-location="props">
            <q-td :props="props">
              <div>
                <div class="text-weight-medium">{{ props.row.ciudad || 'Sin especificar' }}</div>
                <div class="text-caption text-grey-6">{{ props.row.departamento || 'Sin especificar' }}</div>
              </div>
            </q-td>
          </template>

          <!-- Teléfono -->
          <template v-slot:body-cell-phone="props">
            <q-td :props="props">
              <span v-if="props.row.telefono">{{ formatPhone(props.row.telefono) }}</span>
              <span v-else class="text-grey-5">Sin teléfono</span>
            </q-td>
          </template>

          <!-- Fecha de registro -->
          <template v-slot:body-cell-created_at="props">
            <q-td :props="props">
              <div>
                <div class="text-weight-medium">{{ formatDate(props.row.created_at) }}</div>
                <div class="text-caption text-grey-6">{{ formatRelativeDate(props.row.created_at) }}</div>
              </div>
            </q-td>
          </template>

          <!-- Estado activo/inactivo -->
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-chip
                :label="props.row.is_active ? 'Activo' : 'Inactivo'"
                :color="props.row.is_active ? 'positive' : 'negative'"
                text-color="white"
                size="sm"
              />
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
                  @click="openClientDialog('view', props.row)"
                >
                  <q-tooltip>Ver detalles</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="bi-pencil"
                  size="sm"
                  color="primary"
                  @click="openClientDialog('edit', props.row)"
                >
                  <q-tooltip>Editar</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="bi-key"
                  size="sm"
                  color="warning"
                  @click="openPasswordDialog(props.row)"
                >
                  <q-tooltip>Cambiar contraseña</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="bi-toggle-on"
                  size="sm"
                  :color="props.row.is_active ? 'negative' : 'positive'"
                  @click="toggleClientStatus(props.row)"
                >
                  <q-tooltip>{{ props.row.is_active ? 'Desactivar' : 'Activar' }}</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </template>

          <!-- Mensaje cuando no hay datos -->
          <template v-slot:no-data>
            <div class="full-width row flex-center text-grey-5 q-gutter-sm">
              <q-icon size="2em" name="bi-people" />
              <span>No se encontraron clientes</span>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal para agregar/editar/ver cliente -->
    <q-dialog 
      v-model="clientDialog" 
      persistent 
      transition-show="slide-up" 
      transition-hide="slide-down"
    >
      <q-card class="full-width">
        <q-card-section class="row items-center q-pb-none bg-primary text-white">
          <div class="text-h5">
            <q-icon 
              :name="dialogMode === 'create' ? 'bi-plus-circle' : dialogMode === 'edit' ? 'bi-pencil' : 'bi-eye'" 
              class="q-mr-sm" 
            />
            {{ dialogTitle }}
          </div>
          <q-space />
          <q-btn icon="bi-x" flat round dense @click="closeClientDialog" />
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <q-form @submit="saveClient" class="q-gutter-md">
            
            <!-- Información Personal -->
            <div class="text-h6 text-primary q-mb-md">
              <q-icon name="bi-person" class="q-mr-sm" />
              Información Personal
            </div>

            <div class="row q-gutter-md">
              <!-- Nombre -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.nombre"
                  label="Nombre *"
                  outlined
                  :readonly="dialogMode === 'view'"
                  :rules="[val => !!val || 'El nombre es obligatorio']"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-person" />
                  </template>
                </q-input>
              </div>

              <!-- Apellido -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.apellido"
                  label="Apellido *"
                  outlined
                  :readonly="dialogMode === 'view'"
                  :rules="[val => !!val || 'El apellido es obligatorio']"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-person" />
                  </template>
                </q-input>
              </div>
            </div>

            <div class="row q-gutter-md">
              <!-- Tipo de Documento -->
              <div class="col-md-6 col-xs-12">
                <q-select
                  v-model="clientForm.tipo_documento"
                  :options="documentTypes"
                  label="Tipo de Documento *"
                  outlined
                  :readonly="dialogMode === 'view'"
                  :rules="[val => !!val || 'El tipo de documento es obligatorio']"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-card-text" />
                  </template>
                </q-select>
              </div>

              <!-- Número de Documento (solo lectura en edición) -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.numero_documento"
                  label="Número de Documento *"
                  outlined
                  :readonly="dialogMode !== 'create'"
                  :rules="dialogMode === 'create' ? [val => !!val || 'El número de documento es obligatorio'] : []"
                  :hint="dialogMode === 'edit' ? 'No se puede modificar el documento' : ''"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-hash" />
                  </template>
                </q-input>
              </div>
            </div>

            <!-- Información de Contacto -->
            <div class="text-h6 text-primary q-mb-md q-mt-lg">
              <q-icon name="bi-envelope" class="q-mr-sm" />
              Información de Contacto
            </div>

            <!-- Email -->
            <q-input
              v-model="clientForm.email"
              label="Email *"
              type="email"
              outlined
              :readonly="dialogMode === 'view'"
              :rules="[
                val => !!val || 'El email es obligatorio',
                val => /.+@.+\..+/.test(val) || 'Email inválido'
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="bi-envelope" />
              </template>
            </q-input>

            <div class="row q-gutter-md">
              <!-- Teléfono -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.telefono"
                  label="Teléfono"
                  outlined
                  :readonly="dialogMode === 'view'"
                  mask="### ### ####"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-telephone" />
                  </template>
                </q-input>
              </div>

              <!-- Fecha de Nacimiento -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.fecha_nacimiento"
                  label="Fecha de Nacimiento"
                  type="date"
                  outlined
                  :readonly="dialogMode === 'view'"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-calendar" />
                  </template>
                </q-input>
              </div>
            </div>

            <!-- Ubicación -->
            <div class="text-h6 text-primary q-mb-md q-mt-lg">
              <q-icon name="bi-geo-alt" class="q-mr-sm" />
              Ubicación
            </div>

            <!-- Dirección -->
            <q-input
              v-model="clientForm.direccion"
              label="Dirección"
              outlined
              :readonly="dialogMode === 'view'"
            >
              <template v-slot:prepend>
                <q-icon name="bi-house" />
              </template>
            </q-input>

            <div class="row q-gutter-md">
              <!-- Departamento -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.departamento"
                  label="Departamento"
                  outlined
                  :readonly="dialogMode === 'view'"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-map" />
                  </template>
                </q-input>
              </div>

              <!-- Ciudad -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.ciudad"
                  label="Ciudad"
                  outlined
                  :readonly="dialogMode === 'view'"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-building" />
                  </template>
                </q-input>
              </div>
            </div>

            <!-- Contraseña (solo para crear) -->
            <div v-if="dialogMode === 'create'" class="text-h6 text-primary q-mb-md q-mt-lg">
              <q-icon name="bi-shield-lock" class="q-mr-sm" />
              Contraseña
            </div>

            <div v-if="dialogMode === 'create'" class="row q-gutter-md">
              <!-- Contraseña -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.password"
                  label="Contraseña *"
                  :type="showPassword ? 'text' : 'password'"
                  outlined
                  :rules="[
                    val => !!val || 'La contraseña es obligatoria',
                    val => val.length >= 8 || 'La contraseña debe tener al menos 8 caracteres'
                  ]"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-lock" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      :name="showPassword ? 'bi-eye-slash' : 'bi-eye'"
                      class="cursor-pointer"
                      @click="showPassword = !showPassword"
                    />
                  </template>
                </q-input>
              </div>

              <!-- Confirmar contraseña -->
              <div class="col-md-6 col-xs-12">
                <q-input
                  v-model="clientForm.password_confirmation"
                  label="Confirmar contraseña *"
                  :type="showPassword ? 'text' : 'password'"
                  outlined
                  :rules="[
                    val => !!val || 'Confirma la contraseña',
                    val => val === clientForm.password || 'Las contraseñas no coinciden'
                  ]"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-lock" />
                  </template>
                </q-input>
              </div>
            </div>

            <!-- Estado activo -->
            <div class="row items-center q-gutter-sm q-mt-md">
              <q-toggle
                v-model="clientForm.is_active"
                label="Cliente activo"
                :disable="dialogMode === 'view'"
                color="positive"
              />
              <q-icon 
                name="bi-question-circle" 
                color="grey-6" 
                size="sm"
              >
                <q-tooltip>Los clientes inactivos no pueden iniciar sesión</q-tooltip>
              </q-icon>
            </div>

            <!-- Botones de acción -->
            <div class="row q-gutter-sm justify-end q-mt-xl" v-if="dialogMode !== 'view'">
              <q-btn 
                flat 
                label="Cancelar" 
                color="grey-7" 
                @click="closeClientDialog"
              />
              <q-btn 
                type="submit" 
                :label="dialogMode === 'create' ? 'Crear Cliente' : 'Actualizar Cliente'"
                color="primary"
                :loading="saving"
                icon-right="bi-save"
              />
            </div>

          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Modal para cambio de contraseña -->
    <q-dialog v-model="passwordDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">
            <q-icon name="bi-key" class="q-mr-sm" />
            Cambiar Contraseña
          </div>
          <q-space />
          <q-btn icon="bi-x" flat round dense @click="passwordDialog = false" />
        </q-card-section>

        <q-card-section>
          <div class="text-body2 q-mb-md">
            Cambiar contraseña para: <strong>{{ selectedClient?.nombre }} {{ selectedClient?.apellido }}</strong>
          </div>
          
          <q-form @submit="changePassword" class="q-gutter-md">
            <q-input
              v-model="passwordForm.password"
              label="Nueva contraseña *"
              :type="showNewPassword ? 'text' : 'password'"
              outlined
              :rules="[
                val => !!val || 'La contraseña es obligatoria',
                val => val.length >= 8 || 'La contraseña debe tener al menos 8 caracteres'
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="bi-lock" />
              </template>
              <template v-slot:append>
                <q-icon
                  :name="showNewPassword ? 'bi-eye-slash' : 'bi-eye'"
                  class="cursor-pointer"
                  @click="showNewPassword = !showNewPassword"
                />
              </template>
            </q-input>

            <q-input
              v-model="passwordForm.password_confirmation"
              label="Confirmar nueva contraseña *"
              :type="showNewPassword ? 'text' : 'password'"
              outlined
              :rules="[
                val => !!val || 'Confirma la contraseña',
                val => val === passwordForm.password || 'Las contraseñas no coinciden'
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="bi-lock" />
              </template>
            </q-input>

            <div class="row q-gutter-sm justify-end q-mt-lg">
              <q-btn 
                flat 
                label="Cancelar" 
                color="grey-7" 
                @click="passwordDialog = false"
              />
              <q-btn 
                type="submit" 
                label="Cambiar Contraseña"
                color="primary"
                :loading="changingPassword"
                icon-right="bi-key"
              />
            </div>
          </q-form>
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
const changingPassword = ref(false)
const clients = ref([])
const clientDialog = ref(false)
const passwordDialog = ref(false)
const dialogMode = ref('create') // 'create', 'edit', 'view'
const selectedClient = ref(null)

// Controles de visibilidad de contraseña
const showPassword = ref(false)
const showNewPassword = ref(false)

// Filtros
const searchQuery = ref('')
const departmentFilter = ref(null)
const statusFilter = ref(null)

// Formulario de cliente
const clientForm = reactive({
  nombre: '',
  apellido: '',
  tipo_documento: '',
  numero_documento: '',
  email: '',
  telefono: '',
  fecha_nacimiento: '',
  direccion: '',
  ciudad: '',
  departamento: '',
  password: '',
  password_confirmation: '',
  is_active: true
})

// Formulario de cambio de contraseña
const passwordForm = reactive({
  password: '',
  password_confirmation: ''
})

// Paginación
const pagination = ref({
  sortBy: 'created_at',
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0
})

// Definir columnas de la tabla
const columns = [
  {
    name: 'client',
    label: 'Cliente',
    field: 'nombre',
    align: 'left',
    sortable: true
  },
  {
    name: 'document',
    label: 'Documento',
    field: 'numero_documento',
    align: 'left',
    sortable: true
  },
  {
    name: 'location',
    label: 'Ubicación',
    field: 'ciudad',
    align: 'left',
    sortable: true
  },
  {
    name: 'phone',
    label: 'Teléfono',
    field: 'telefono',
    align: 'left',
    sortable: false
  },
  {
    name: 'created_at',
    label: 'Fecha Registro',
    field: 'created_at',
    align: 'left',
    sortable: true
  },
  {
    name: 'status',
    label: 'Estado',
    field: 'is_active',
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

// Opciones para filtros
const departmentOptions = [
  { label: 'Todos los departamentos', value: null },
  { label: 'Antioquia', value: 'Antioquia' },
  { label: 'Bogotá D.C.', value: 'Bogotá D.C.' },
  { label: 'Valle del Cauca', value: 'Valle del Cauca' },
  { label: 'Cundinamarca', value: 'Cundinamarca' },
  { label: 'Atlántico', value: 'Atlántico' },
  { label: 'Santander', value: 'Santander' },
  { label: 'Bolívar', value: 'Bolívar' },
  // Agregar más departamentos según necesidad
]

const statusOptions = [
  { label: 'Todos los estados', value: null },
  { label: 'Activos', value: true },
  { label: 'Inactivos', value: false }
]

const documentTypes = [
  { label: 'Cédula de Ciudadanía', value: 'CC' },
  { label: 'Tarjeta de Identidad', value: 'TI' },
  { label: 'Cédula de Extranjería', value: 'CE' },
  { label: 'Pasaporte', value: 'PP' },
  { label: 'NIT', value: 'NIT' }
]

// Clientes filtrados
const filteredClients = computed(() => {
  let filtered = [...clients.value]

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(client =>
      client.nombre.toLowerCase().includes(query) ||
      client.apellido.toLowerCase().includes(query) ||
      client.email.toLowerCase().includes(query)
    )
  }

  // Filtrar por departamento
  if (departmentFilter.value) {
    filtered = filtered.filter(client =>
      client.departamento === departmentFilter.value
    )
  }

  // Filtrar por estado
  if (statusFilter.value !== null) {
    filtered = filtered.filter(client =>
      client.is_active === statusFilter.value
    )
  }

  return filtered
})

// Verificar si hay filtros activos
const hasActiveFilters = computed(() => {
  return searchQuery.value || departmentFilter.value || statusFilter.value !== null
})

const dialogTitle = computed(() => {
  switch (dialogMode.value) {
    case 'create': return 'Nuevo Cliente'
    case 'edit': return 'Editar Cliente'
    case 'view': return 'Detalles del Cliente'
    default: return 'Cliente'
  }
})

// Cargar clientes desde la API
const loadClients = async () => {
  loading.value = true
  try {
    const response = await api.get('/clientes')
    
    if (response.data?.data) {
      clients.value = response.data.data
      if (response.data.total) {
        pagination.value.rowsNumber = response.data.total
      }
    } else if (Array.isArray(response.data)) {
      clients.value = response.data
    } else {
      clients.value = []
    }

    $q.notify({
      type: 'positive',
      message: `${clients.value.length} clientes cargados correctamente`,
      icon: 'bi-check-circle'
    })

  } catch (error) {
    console.error('Error loading clients:', error)
    
    let errorMessage = 'Error al cargar clientes'
    
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
    } else {
      errorMessage = error.message
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage,
      icon: 'bi-exclamation-triangle'
    })
    clients.value = []
  } finally {
    loading.value = false
  }
}

// Abrir modal de cliente
const openClientDialog = (mode, client = null) => {
  dialogMode.value = mode
  selectedClient.value = client
  
  if (mode === 'create') {
    // Resetear formulario
    Object.assign(clientForm, {
      nombre: '',
      apellido: '',
      tipo_documento: '',
      numero_documento: '',
      email: '',
      telefono: '',
      fecha_nacimiento: '',
      direccion: '',
      ciudad: '',
      departamento: '',
      password: '',
      password_confirmation: '',
      is_active: true
    })
  } else if (client) {
    // Cargar datos del cliente
    Object.assign(clientForm, {
      nombre: client.nombre || '',
      apellido: client.apellido || '',
      tipo_documento: client.tipo_documento || '',
      numero_documento: client.numero_documento || '',
      email: client.email || '',
      telefono: client.telefono || '',
      fecha_nacimiento: client.fecha_nacimiento || '',
      direccion: client.direccion || '',
      ciudad: client.ciudad || '',
      departamento: client.departamento || '',
      password: '',
      password_confirmation: '',
      is_active: client.is_active ?? true
    })
  }
  
  clientDialog.value = true
}

// Cerrar modal
const closeClientDialog = () => {
  clientDialog.value = false
  selectedClient.value = null
  saving.value = false
}

// Guardar cliente
const saveClient = async () => {
  saving.value = true
  
  try {
    let response
    
    if (dialogMode.value === 'create') {
      response = await api.post('/clientes', clientForm)
      
      // Agregar nuevo cliente al array
      if (response.data?.client) {
        clients.value.unshift(response.data.client)
      }
      
    } else if (dialogMode.value === 'edit' && selectedClient.value) {
      // Crear copia sin campos de contraseña
      const { password, password_confirmation, ...updateData } = clientForm
      response = await api.put(`/clientes/${selectedClient.value.id}`, updateData)
      
      // Actualizar cliente en el array
      const index = clients.value.findIndex(c => c.id === selectedClient.value.id)
      if (index !== -1 && response.data?.client) {
        clients.value[index] = response.data.client
      }
    }

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Operación completada correctamente',
      icon: 'bi-check-circle'
    })

    closeClientDialog()

  } catch (error) {
    console.error('Error saving client:', error)
    
    let errorMessage = 'Error al guardar el cliente'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage = errors.join(', ')
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage,
      icon: 'bi-exclamation-triangle'
    })
  } finally {
    saving.value = false
  }
}

// Abrir modal de cambio de contraseña
const openPasswordDialog = (client) => {
  selectedClient.value = client
  passwordForm.password = ''
  passwordForm.password_confirmation = ''
  passwordDialog.value = true
}

// Cambiar contraseña
const changePassword = async () => {
  changingPassword.value = true
  
  try {
    const response = await api.patch(`/clientes/${selectedClient.value.id}/change-password`, {
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation
    })

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Contraseña cambiada correctamente',
      icon: 'bi-check-circle'
    })

    passwordDialog.value = false
    passwordForm.password = ''
    passwordForm.password_confirmation = ''

  } catch (error) {
    console.error('Error changing password:', error)
    
    let errorMessage = 'Error al cambiar la contraseña'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage = errors.join(', ')
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage,
      icon: 'bi-exclamation-triangle'
    })
  } finally {
    changingPassword.value = false
  }
}

// Cambiar estado activo/inactivo
const toggleClientStatus = async (client) => {
  client.updating = true
  try {
    const response = await api.patch(`/clientes/${client.id}/toggle-active`)
    
    // Actualizar el cliente en el array local
    const index = clients.value.findIndex(c => c.id === client.id)
    if (index !== -1) {
      clients.value[index].is_active = !clients.value[index].is_active
    }

    const message = response.data?.message || 
      `Cliente ${clients.value[index].is_active ? 'activado' : 'desactivado'} correctamente`

    $q.notify({
      type: 'positive',
      message: message,
      icon: 'bi-check-circle'
    })

  } catch (error) {
    console.error('Error toggling client status:', error)
    
    let errorMessage = 'Error al cambiar el estado del cliente'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage,
      icon: 'bi-exclamation-triangle'
    })
  } finally {
    client.updating = false
  }
}

// Aplicar filtros
const applyFilters = () => {
  console.log('Filtros aplicados:', {
    search: searchQuery.value,
    department: departmentFilter.value,
    status: statusFilter.value
  })
}

// Limpiar filtros
const clearFilters = () => {
  searchQuery.value = ''
  departmentFilter.value = null
  statusFilter.value = null
}

// Manejar solicitudes de paginación/ordenamiento
const onRequest = (props) => {
  pagination.value = props.pagination
}

// Funciones utilitarias
const getInitials = (name) => {
  return name.split(' ')
    .map(word => word.charAt(0))
    .join('')
    .substring(0, 2)
    .toUpperCase()
}

const formatPhone = (phone) => {
  if (!phone) return ''
  // Formatear teléfono con espacios
  return phone.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3')
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('es-CO', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

const formatRelativeDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 1) return 'Hace 1 día'
  if (diffDays < 30) return `Hace ${diffDays} días`
  
  const diffMonths = Math.floor(diffDays / 30)
  if (diffMonths === 1) return 'Hace 1 mes'
  if (diffMonths < 12) return `Hace ${diffMonths} meses`
  
  const diffYears = Math.floor(diffMonths / 12)
  if (diffYears === 1) return 'Hace 1 año'
  return `Hace ${diffYears} años`
}

// Cargar datos al montar el componente
onMounted(async () => {
  await loadClients()
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

/* Responsive adjustments */
@media (max-width: 768px) {
  .q-dialog .q-card {
    margin: 0;
    height: 100vh;
    max-height: 100vh;
  }
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
</style>
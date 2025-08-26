<template>
  <div class="q-pa-lg">
    <div class="row items-center justify-between q-mb-xl">
      <div class="col">
        <div class="text-h5 text-weight-light text-primary q-mb-sm">
          Gestión de Usuarioshsgdgd
        </div>
        <p class="text-grey-6 text-body1">Administra los usuarios del sistema</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="bi-plus-circle"
          label="Nuevo Usuario"
          unelevated
          size="md"
          class="q-px-lg"
          @click="openUserDialog('create')"
        />
      </div>
    </div>

    <!-- Filtros mejorados -->
    <q-card flat bordered class="q-mb-lg shadow-2">
      <q-card-section>
        <div class="text-subtitle2 text-grey-8 q-mb-md">
          <q-icon name="bi-search" class="q-mr-xs" />
          Filtros de búsqueda
        </div>
        <div class="row q-gutter-md">
          <!-- Búsqueda por Email/Nombre -->
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
                <q-icon name="bi-search" color="primary" />
              </template>
            </q-input>
          </div>

          <!-- Filtro por estado -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-select
              v-model="statusFilter"
              :options="statusOptions"
              label="Estado"
              outlined
              dense
              clearable
              @update:model-value="applyFilters"
            />
          </div>

          <!-- Botón limpiar filtros -->
          <div class="col-auto">
            <q-btn
              flat
              icon="bi-x-circle"
              label="Limpiar"
              color="grey-7"
              @click="clearFilters"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Estadísticas rápidas -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col">
        <q-card flat bordered>
          <q-card-section class="text-center">
            <div class="text-h6 text-primary">{{ users.length }}</div>
            <div class="text-caption text-grey-6">Total Usuarios</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col">
        <q-card flat bordered>
          <q-card-section class="text-center">
            <div class="text-h6 text-positive">{{ activeUsers }}</div>
            <div class="text-caption text-grey-6">Usuarios Activos</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col">
        <q-card flat bordered>
          <q-card-section class="text-center">
            <div class="text-h6 text-negative">{{ inactiveUsers }}</div>
            <div class="text-caption text-grey-6">Usuarios Inactivos</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-card flat bordered class="shadow-2">
      <q-card-section>
        <q-table
          :rows="filteredUsers"
          :columns="columns"
          row-key="id"
          :loading="loading"
          flat
          bordered
          :pagination="pagination"
          @request="onRequest"
          class="user-table"
        >
          <!-- Información del usuario -->
          <template v-slot:body-cell-user="props">
            <q-td :props="props">
              <div class="row items-center q-gutter-sm">
                <q-avatar
                  size="40px"
                  color="primary"
                  text-color="white"
                  :icon="'bi-person'"
                >
                  {{ getInitials(props.row.name) }}
                </q-avatar>
                <div>
                  <div class="text-weight-medium">{{ props.row.name }}</div>
                  <div class="text-caption text-grey-6">{{ props.row.email }}</div>
                </div>
              </div>
            </q-td>
          </template>

          <!-- Fecha de registro -->
          <template v-slot:body-cell-created_at="props">
            <q-td :props="props">
              <div class="text-body2">{{ formatDate(props.row.created_at) }}</div>
              <div class="text-caption text-grey-6">
                {{ formatRelativeDate(props.row.created_at) }}
              </div>
            </q-td>
          </template>

          <!-- Estado activo/inactivo -->
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-toggle
                :model-value="props.row.is_active"
                @update:model-value="toggleUserStatus(props.row)"
                color="positive"
                :loading="props.row.updating"
                size="lg"
              />
              <div class="text-caption text-center q-mt-xs">
                {{ props.row.is_active ? 'Activo' : 'Inactivo' }}
              </div>
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
                  @click="openUserDialog('view', props.row)"
                >
                  <q-tooltip>Ver detalles</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="bi-pencil"
                  size="sm"
                  color="primary"
                  @click="openUserDialog('edit', props.row)"
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
                  icon="bi-trash"
                  size="sm"
                  color="negative"
                  @click="deleteUser(props.row)"
                >
                  <q-tooltip>Eliminar</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </template>

          <!-- Mensaje cuando no hay datos -->
          <template v-slot:no-data>
            <div class="full-width row flex-center text-grey-5 q-gutter-sm q-pa-lg">
              <q-icon size="3em" name="bi-people" />
              <div class="text-center">
                <div class="text-h6">No se encontraron usuarios</div>
                <div class="text-body2">Intenta ajustar los filtros o agrega un nuevo usuario</div>
              </div>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal para agregar/editar/ver Usuario -->
    <q-dialog 
      v-model="userDialog" 
      persistent 
      transition-show="slide-up" 
      transition-hide="slide-down"
    >
      <q-card class="full-width" style="max-width: 600px">
        <q-card-section class="row items-center q-pb-none bg-primary text-white">
          <div class="text-h5">
            <q-icon 
              :name="dialogMode === 'create' ? 'bi-plus-circle' : dialogMode === 'edit' ? 'bi-pencil' : 'bi-eye'" 
              class="q-mr-sm" 
            />
            {{ dialogTitle }}
          </div>
          <q-space />
          <q-btn icon="bi-x" flat round dense @click="closeUserDialog" />
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <q-form @submit="saveUser" class="q-gutter-md">
            
            <!-- Nombre completo -->
            <q-input
              v-model="userForm.name"
              label="Nombre completo *"
              outlined
              :readonly="dialogMode === 'view'"
              :rules="[val => !!val || 'El nombre es obligatorio']"
            >
              <template v-slot:prepend>
                <q-icon name="bi-person" />
              </template>
            </q-input>

            <!-- Email -->
            <q-input
              v-model="userForm.email"
              label="Correo electrónico *"
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

            <!-- Contraseña (solo para crear) -->
            <q-input
              v-if="dialogMode === 'create'"
              v-model="userForm.password"
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

            <!-- Confirmar contraseña (solo para crear) -->
            <q-input
              v-if="dialogMode === 'create'"
              v-model="userForm.password_confirmation"
              label="Confirmar contraseña *"
              :type="showPassword ? 'text' : 'password'"
              outlined
              :rules="[
                val => !!val || 'Confirma la contraseña',
                val => val === userForm.password || 'Las contraseñas no coinciden'
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="bi-lock" />
              </template>
            </q-input>

            <!-- Estado activo -->
            <div class="row items-center q-gutter-sm">
              <q-toggle
                v-model="userForm.is_active"
                label="Usuario activo"
                :disable="dialogMode === 'view'"
                color="positive"
              />
              <q-icon 
                name="bi-question-circle" 
                color="grey-6" 
                size="sm"
              >
                <q-tooltip>Los usuarios inactivos no pueden iniciar sesión</q-tooltip>
              </q-icon>
            </div>

            <!-- Información adicional en modo view -->
            <div v-if="dialogMode === 'view' && selectedUser">
              <q-separator class="q-my-md" />
              <div class="text-subtitle2 text-grey-8 q-mb-sm">Información adicional</div>
              <div class="row q-gutter-md">
                <div class="col">
                  <div class="text-caption text-grey-6">Usuario ID</div>
                  <div class="text-body2">#{{ selectedUser.id }}</div>
                </div>
                <div class="col">
                  <div class="text-caption text-grey-6">Registrado</div>
                  <div class="text-body2">{{ formatDate(selectedUser.created_at) }}</div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="row q-gutter-sm justify-end q-mt-lg" v-if="dialogMode !== 'view'">
              <q-btn 
                flat 
                label="Cancelar" 
                color="grey-7" 
                @click="closeUserDialog"
              />
              <q-btn 
                type="submit" 
                :label="dialogMode === 'create' ? 'Crear Usuario' : 'Actualizar Usuario'"
                color="primary"
                :loading="saving"
                icon-right="save"
              />
            </div>

          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Modal para cambiar contraseña -->
    <q-dialog v-model="passwordDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center q-pb-none bg-warning text-white">
          <div class="text-h6">
            <q-icon name="bi-key" class="q-mr-sm" />
            Cambiar Contraseña
          </div>
          <q-space />
          <q-btn icon="bi-x" flat round dense @click="passwordDialog = false" />
        </q-card-section>

        <q-card-section>
          <div class="text-body1 q-mb-md">
            Cambiar contraseña para: <strong>{{ selectedUser?.name }}</strong>
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
            />

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
                color="warning"
                :loading="changingPassword"
                icon-right="key"
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
const users = ref([])
const userDialog = ref(false)
const passwordDialog = ref(false)
const dialogMode = ref('create') // 'create', 'edit', 'view'
const selectedUser = ref(null)
const showPassword = ref(false)
const showNewPassword = ref(false)

// Filtros
const searchQuery = ref('')
const statusFilter = ref(null)

// Formulario de usuario
const userForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_active: true
})

// Formulario de contraseña
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
    name: 'user',
    label: 'Usuario',
    field: 'name',
    align: 'left',
    sortable: true,
    style: 'width: 40%'
  },
  {
    name: 'created_at',
    label: 'Fecha de Registro',
    field: 'created_at',
    align: 'center',
    sortable: true,
    style: 'width: 20%'
  },
  {
    name: 'status',
    label: 'Estado',
    field: 'is_active',
    align: 'center',
    sortable: true,
    style: 'width: 15%'
  },
  {
    name: 'actions',
    label: 'Acciones',
    field: 'actions',
    align: 'center',
    sortable: false,
    style: 'width: 25%'
  }
]

// Opciones para el filtro de estado
const statusOptions = [
  { label: 'Activos', value: 1 },
  { label: 'Inactivos', value: 0 }
]

// Computed
const filteredUsers = computed(() => {
  let filtered = [...users.value]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(user => 
      user.name?.toLowerCase().includes(query) ||
      user.email?.toLowerCase().includes(query)
    )
  }

  if (statusFilter.value !== null) {
    filtered = filtered.filter(user => user.is_active === statusFilter.value)
  }

  // aplicar paginación local
  const start = (pagination.value.page - 1) * pagination.value.rowsPerPage
  const end = start + pagination.value.rowsPerPage

  pagination.value.rowsNumber = filtered.length
  return filtered.slice(start, end)
})

const dialogTitle = computed(() => {
  switch (dialogMode.value) {
    case 'create': return 'Nuevo Usuario'
    case 'edit': return 'Editar Usuario'
    case 'view': return 'Detalles del Usuario'
    default: return 'Usuario'
  }
})

const activeUsers = computed(() => 
  users.value.filter(user => user.is_active).length
)

const inactiveUsers = computed(() => 
  users.value.filter(user => !user.is_active).length
)

// Métodos
const loadUsers = async () => {
  loading.value = true
  try {
    const response = await api.get('/users')
    
    if (response.data?.data) {
      users.value = response.data.data
    } else if (Array.isArray(response.data)) {
      users.value = response.data
    } else {
      users.value = []
    }

  } catch (error) {
    console.error('Error loading users:', error)
    users.value = []
    
    $q.notify({
      type: 'negative',
      message: 'Error al cargar los usuarios',
      icon: 'error'
    })
  } finally {
    loading.value = false
  }
}

const openUserDialog = (mode, user = null) => {
  dialogMode.value = mode
  selectedUser.value = user
  
  if (mode === 'create') {
    // Resetear formulario
    Object.assign(userForm, {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      is_active: true
    })
  } else if (user) {
    // Cargar datos del usuario
    Object.assign(userForm, {
      name: user.name || '',
      email: user.email || '',
      password: '',
      password_confirmation: '',
      is_active: user.is_active ?? true
    })
  }
  
  userDialog.value = true
}

const closeUserDialog = () => {
  userDialog.value = false
  selectedUser.value = null
  saving.value = false
  showPassword.value = false
}

const openPasswordDialog = (user) => {
  selectedUser.value = user
  Object.assign(passwordForm, {
    password: '',
    password_confirmation: ''
  })
  passwordDialog.value = true
}

const saveUser = async () => {
  saving.value = true
  
  try {
    let response
    
    if (dialogMode.value === 'create') {
      response = await api.post('/users', userForm)
      
      // Agregar nuevo usuario al array
      if (response.data?.data) {
        users.value.unshift(response.data.data)
      }
      
    } else if (dialogMode.value === 'edit' && selectedUser.value) {
      const updateData = {
        name: userForm.name,
        email: userForm.email,
        is_active: userForm.is_active
      }
      
      response = await api.put(`/users/${selectedUser.value.id}`, updateData)
      
      // Actualizar usuario en el array
      const index = users.value.findIndex(u => u.id === selectedUser.value.id)
      if (index !== -1 && response.data?.data) {
        users.value[index] = response.data.data
      }
    }

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Operación completada correctamente',
      icon: 'check_circle'
    })

    closeUserDialog()

  } catch (error) {
    console.error('Error saving user:', error)
    
    let errorMessage = 'Error al guardar el usuario'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage = errors.join(', ')
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

const changePassword = async () => {
  changingPassword.value = true
  
  try {
    const response = await api.put(`/users/${selectedUser.value.id}/change-password`, {
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation
    })

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Contraseña actualizada correctamente',
      icon: 'check_circle'
    })

    passwordDialog.value = false

  } catch (error) {
    console.error('Error changing password:', error)
    
    let errorMessage = 'Error al cambiar la contraseña'
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage = errors.join(', ')
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage,
      icon: 'error'
    })
  } finally {
    changingPassword.value = false
  }
}

const toggleUserStatus = async (user) => {
  user.updating = true
  try {
    const response = await api.patch(`/users/${user.id}/toggle-active`)
    
    const index = users.value.findIndex(u => u.id === user.id)
    if (index !== -1) {
      users.value[index].is_active = !users.value[index].is_active
    }

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Estado actualizado correctamente',
      icon: 'check_circle'
    })

  } catch (error) {
    console.error('Error toggling user status:', error)
    
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error al cambiar el estado',
      icon: 'error'
    })
  } finally {
    user.updating = false
  }
}

const deleteUser = (user) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: `¿Estás seguro de eliminar el usuario "${user.name}"?`,
    cancel: true,
    persistent: true,
    ok: {
      label: 'Eliminar',
      color: 'negative',
      icon: 'delete'
    },
    cancel: {
      label: 'Cancelar',
      color: 'grey',
      flat: true
    }
  }).onOk(async () => {
    const loadingNotify = $q.notify({
      type: 'ongoing',
      message: 'Eliminando usuario...',
      timeout: 0,
      spinner: true
    })
    
    try {
      const response = await api.delete(`/users/${user.id}`)
      
      // Remover el usuario del array local
      const index = users.value.findIndex(u => u.id === user.id)
      if (index !== -1) {
        users.value.splice(index, 1)
      }

      loadingNotify()
      
      $q.notify({
        type: 'positive',
        message: response.data?.message || 'Usuario eliminado correctamente',
        icon: 'check_circle'
      })
      
    } catch (error) {
      console.error('Error deleting user:', error)
      
      loadingNotify()
      
      $q.notify({
        type: 'negative',
        message: error.response?.data?.message || 'Error al eliminar el usuario',
        icon: 'error'
      })
    }
  })
}

const applyFilters = () => {
  console.log('Filtros aplicados:', {
    search: searchQuery.value,
    status: statusFilter.value
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = null
}

const onRequest = (props) => {
  pagination.value = props.pagination
}

const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatRelativeDate = (dateString) => {
  if (!dateString) return ''
  const now = new Date()
  const date = new Date(dateString)
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 1) return 'Ayer'
  if (diffDays < 7) return `Hace ${diffDays} días`
  if (diffDays < 30) return `Hace ${Math.ceil(diffDays / 7)} semanas`
  return `Hace ${Math.ceil(diffDays / 30)} meses`
}

// Cargar datos al montar el componente
onMounted(async () => {
  await loadUsers()
})
</script>

<style scoped>
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

/* Responsive adjustments */
@media (max-width: 768px) {
  .q-dialog .q-card {
    margin: 0;
    height: 100vh;
    max-height: 100vh;
  }
  
  .user-table .q-table td {
    padding: 8px 4px;
  }
}

/* Estilos para el modal */
.q-dialog .q-card-section {
  padding: 24px;
}

.q-form .q-field {
  margin-bottom: 16px;
}

/* Avatar hover effect */
.q-avatar {
  transition: transform 0.2s ease;
  cursor: default;
}

.q-avatar:hover {
  transform: scale(1.05);
}

/* Loading states */
.q-btn--loading {
  pointer-events: none;
}

/* Improved table row hover */
.q-table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Cursor pointer for clickable icons */
.cursor-pointer {
  cursor: pointer;
}
</style>
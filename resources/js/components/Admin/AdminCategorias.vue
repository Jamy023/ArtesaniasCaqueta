<template>
  <div class="q-pa-lg">
    <div class="row items-center justify-between q-mb-xl">
      <div class="col">
        <div class="text-h5 text-weight-light text-primary q-mb-sm">
         
          Gestión de Categorías
        </div>
        <p class="text-grey-6 text-body1">Administra el catálogo de categorías de tu tienda</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Nueva Categoría"
          unelevated
          size="md"
          class="q-px-lg"
          @click="openCategoryDialog('create')"
        />
      </div>
    </div>


 

    <!-- Filtros mejorados -->
    <q-card flat bordered class="q-mb-lg shadow-2">
      <q-card-section>
        <div class="text-subtitle2 text-grey-8 q-mb-md">
          <q-icon name="search" class="q-mr-xs" />
          Filtros de búsqueda
        </div>
        <div class="row q-gutter-md">
          <!-- Búsqueda por nombre -->
          <div class="col-md-4 col-sm-6 col-xs-12">
            <q-input
              v-model="searchQuery"
              placeholder="Buscar categorías..."
              outlined
              dense
              clearable
              @input="applyFilters"
            >
              <template v-slot:prepend>
                <q-icon name="search" color="primary" />
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
              icon="clear"
              label="Limpiar"
              color="grey-7"
              @click="clearFilters"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabla de categorías mejorada -->
    <q-card flat bordered class="shadow-2">
      <q-card-section>
        <q-table
          :rows="filteredCategories"
          :columns="columns"
          row-key="id"
          :loading="loading"
          flat
          bordered
          :pagination="pagination"
          @request="onRequest"
          class="category-table"
        >
          <!-- Nombre y descripción -->
          <template v-slot:body-cell-name="props">
            <q-td :props="props">
              <div class="row items-center q-gutter-sm">
                <q-avatar
                  size="30px"
                  color="primary"
                  text-color="white"
                  :icon="props.row.image ? 'photo' : 'folder'"
                >
                  <img v-if="props.row.image" :src="props.row.image" alt="Categoría" />
                </q-avatar>
                <div>
                 <div class="text-weight-medium">{{ props.row.name }}</div>
                <div class="text-caption text-grey-6" v-if="props.row.description">
                    {{ truncateText(props.row.description, 60) }}
                  </div>
               
                </div>
              </div>
            </q-td>
          </template>

          <!-- Cantidad de productos -->
          <template v-slot:body-cell-products_count="props">
            <q-td :props="props">
              <q-chip 
                :label="props.row.products_count || 0"
                :color="props.row.products_count > 0 ? 'positive' : 'grey-5'"
                text-color="white"
                size="sm"
                icon="shop"
              />
            </q-td>
          </template>

          <!-- Estado activo/inactivo -->
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-toggle
                :model-value="props.row.is_active"
                @update:model-value="toggleCategoryStatus(props.row)"
                color="positive"
                :loading="props.row.updating"
                size="lg"
              />
              <div class="text-caption text-center q-mt-xs">
                {{ props.row.is_active ? 'Activa' : 'Inactiva' }}
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
                  icon="visibility"
                  size="sm"
                  color="info"
                  @click="openCategoryDialog('view', props.row)"
                >
                  <q-tooltip>Ver detalles</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="edit"
                  size="sm"
                  color="primary"
                  @click="openCategoryDialog('edit', props.row)"
                >
                  <q-tooltip>Editar</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="delete"
                  size="sm"
                  color="negative"
                  @click="deleteCategory(props.row)"
                >
                  <q-tooltip>Eliminar</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </template>

          <!-- Mensaje cuando no hay datos -->
          <template v-slot:no-data>
            <div class="full-width row flex-center text-grey-5 q-gutter-sm q-pa-lg">
              <q-icon size="3em" name="category" />
              <div class="text-center">
                <div class="text-h6">No se encontraron categorías</div>
                <div class="text-body2">Intenta ajustar los filtros o agrega una nueva categoría</div>
              </div>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal para agregar/editar/ver categoría -->
    <q-dialog 
      v-model="categoryDialog" 
      persistent 

      transition-show="slide-up" 
      transition-hide="slide-down"
    >
      <q-card class="full-width">
        <q-card-section class="row items-center q-pb-none bg-primary text-white">
          <div class="text-h5">
            <q-icon 
              :name="dialogMode === 'create' ? 'add_circle' : dialogMode === 'edit' ? 'edit' : 'visibility'" 
              class="q-mr-sm" 
            />
            {{ dialogTitle }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense @click="closeCategoryDialog" />
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <div class="row q-gutter-lg">
            <!-- Formulario -->
            <div class="col-md-8 col-xs-12">
              <q-form @submit="saveCategoryModal" class="q-gutter-md">
                
                <!-- Nombre -->
                <q-input
                  v-model="categoryForm.name"
                  label="Nombre de la categoría *"
                  outlined
                  :readonly="dialogMode === 'view'"
                  :rules="[val => !!val || 'El nombre es obligatorio']"
                  @input="generateSlug"
                >
                  <template v-slot:prepend>
                    <q-icon name="title" />
                  </template>
                </q-input>

                <!-- Slug (generado automáticamente) -->
                <q-input
                  v-model="categoryForm.slug"
                  label="Slug (URL amigable)"
                  outlined
                  readonly
                  hint="Se genera automáticamente basado en el nombre"
                >
                  <template v-slot:prepend>
                    <q-icon name="link" />
                  </template>
                </q-input>

                <!-- Descripción -->
                <q-input
                  v-model="categoryForm.description"
                  label="Descripción"
                  type="textarea"
                  outlined
                  rows="4"
                  :readonly="dialogMode === 'view'"
                  counter
                  maxlength="1000"
                >
                  <template v-slot:prepend>
                    <q-icon name="description" />
                  </template>
                </q-input>

                <!-- URL de imagen -->
                <q-input
                  v-model="categoryForm.image"
                  label="URL de imagen (opcional)"
                  outlined
                  :readonly="dialogMode === 'view'"
                  hint="URL de la imagen representativa de la categoría"
                >
                  <template v-slot:prepend>
                    <q-icon name="image" />
                  </template>
                </q-input>

                <!-- Estado activo -->
                <div class="row items-center q-gutter-sm">
                  <q-toggle
                    v-model="categoryForm.is_active"
                    label="Categoría activa"
                    :disable="dialogMode === 'view'"
                    color="positive"
                  />
                  <q-icon 
                    name="help_outline" 
                    color="grey-6" 
                    size="sm"
                  >
                    <q-tooltip>Las categorías inactivas no se mostrarán en la tienda</q-tooltip>
                  </q-icon>
                </div>

                <!-- Botones de acción -->
                <div class="row q-gutter-sm justify-end q-mt-lg" v-if="dialogMode !== 'view'">
                  <q-btn 
                    flat 
                    label="Cancelar" 
                    color="grey-7" 
                    @click="closeCategoryDialog"
                  />
                  <q-btn 
                    type="submit" 
                    :label="dialogMode === 'create' ? 'Crear Categoría' : 'Actualizar Categoría'"
                    color="primary"
                    :loading="saving"
                    icon-right="save"
                  />
                </div>

              </q-form>
            </div>

          
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
const categories = ref([])
const categoryDialog = ref(false)
const dialogMode = ref('create') // 'create', 'edit', 'view'
const selectedCategory = ref(null)

// Filtros
const searchQuery = ref('')
const statusFilter = ref(null)

// Formulario de categoría
const categoryForm = reactive({
  name: '',
  slug: '',
  description: '',
  image: '',
  is_active: true
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
    name: 'name',
    label: 'Categoría',
    field: 'name',
    align: 'left',
    sortable: true,
    style: 'width: 40%'
  },
  {
    name: 'products_count',
    label: 'Productos',
    field: 'products_count',
    align: 'center',
    sortable: true,
    style: 'width: 15%'
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
    style: 'width: 20%'
  }
]

// Opciones para el filtro de estado
const statusOptions = [
  { label: 'Activas', value: 1 },
  { label: 'Inactivas', value: 0 }
]

// Computed
const filteredCategories = computed(() => {
  let filtered = [...categories.value]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(category => 
      category.name?.toLowerCase().includes(query) ||
      category.description?.toLowerCase().includes(query) ||
      category.slug?.toLowerCase().includes(query)
    )
  }

  if (statusFilter.value !== null) {
    filtered = filtered.filter(category => category.is_active === statusFilter.value)
  }

  // aplicar paginación local
  const start = (pagination.value.page - 1) * pagination.value.rowsPerPage
  const end = start + pagination.value.rowsPerPage

  pagination.value.rowsNumber = filtered.length // actualiza el total
  return filtered.slice(start, end)
})


const dialogTitle = computed(() => {
  switch (dialogMode.value) {
    case 'create': return 'Nueva Categoría'
    case 'edit': return 'Editar Categoría'
    case 'view': return 'Detalles de Categoría'
    default: return 'Categoría'
  }
})




const inactiveCategories = computed(() => 
  categories.value.filter(cat => !cat.is_active).length
)


// Métodos
const loadCategories = async () => {
  loading.value = true
  try {
    const response = await api.get('/categories')
    
    if (response.data?.data) {
      categories.value = response.data.data
    } else if (Array.isArray(response.data)) {
      categories.value = response.data
    } else {
      categories.value = []
    }

  } catch (error) {
    console.error('Error loading categories:', error)
    categories.value = []
    
    $q.notify({
      type: 'negative',
      message: 'Error al cargar las categorías',
      icon: 'error'
    })
  } finally {
    loading.value = false
  }
}

const generateSlug = () => {
  if (categoryForm.name) {
    categoryForm.slug = categoryForm.name
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim('-')
  }
}

const openCategoryDialog = (mode, category = null) => {
  dialogMode.value = mode
  selectedCategory.value = category
  
  if (mode === 'create') {
    // Resetear formulario
    Object.assign(categoryForm, {
      name: '',
      slug: '',
      description: '',
      image: '',
      is_active: true
    })
  } else if (category) {
    // Cargar datos de la categoría
    Object.assign(categoryForm, {
      name: category.name || '',
      slug: category.slug || '',
      description: category.description || '',
      image: category.image || '',
      is_active: category.is_active ?? true
    })
  }
  
  categoryDialog.value = true
}

const closeCategoryDialog = () => {
  categoryDialog.value = false
  selectedCategory.value = null
  saving.value = false
}

const saveCategoryModal = async () => {
  saving.value = true
  
  try {
    let response
    
    if (dialogMode.value === 'create') {
      response = await api.post('/categories', categoryForm)
      
      // Agregar nueva categoría al array
      if (response.data?.data) {
        categories.value.unshift(response.data.data)
      }
      
    } else if (dialogMode.value === 'edit' && selectedCategory.value) {
      response = await api.put(`/categories/${selectedCategory.value.id}`, categoryForm)
      
      // Actualizar categoría en el array
      const index = categories.value.findIndex(c => c.id === selectedCategory.value.id)
      if (index !== -1 && response.data?.data) {
        categories.value[index] = response.data.data
      }
    }

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Operación completada correctamente',
      icon: 'check_circle'
    })

    closeCategoryDialog()

  } catch (error) {
    console.error('Error saving category:', error)
    
    let errorMessage = 'Error al guardar la categoría'
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

const toggleCategoryStatus = async (category) => {
  category.updating = true
  try {
    const response = await api.patch(`/categories/${category.id}/toggle-active`)
    
    const index = categories.value.findIndex(c => c.id === category.id)
    if (index !== -1) {
      categories.value[index].is_active = !categories.value[index].is_active
    }

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Estado actualizado correctamente',
      icon: 'check_circle'
    })

  } catch (error) {
    console.error('Error toggling category status:', error)
    
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error al cambiar el estado',
      icon: 'error'
    })
  } finally {
    category.updating = false
  }
}

const deleteCategory = (category) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: `¿Estás seguro de eliminar la categoría "${category.name}"?`,
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
      message: 'Eliminando categoría...',
      timeout: 0,
      spinner: true
    })
    
    try {
      const response = await api.delete(`/categories/${category.id}`)
      
      // Remover la categoría del array local
      const index = categories.value.findIndex(c => c.id === category.id)
      if (index !== -1) {
        categories.value.splice(index, 1)
      }

      loadingNotify()
      
      $q.notify({
        type: 'positive',
        message: response.data?.message || 'Categoría eliminada correctamente',
        icon: 'check_circle'
      })
      
    } catch (error) {
      console.error('Error deleting category:', error)
      
      loadingNotify()
      
      $q.notify({
        type: 'negative',
        message: error.response?.data?.message || 'Error al eliminar la categoría',
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

const truncateText = (text, maxLength) => {
  if (!text) return ''
  return text.length > maxLength ? text.slice(0, maxLength) + '...' : text
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Cargar datos al montar el componente
onMounted(async () => {
  await loadCategories()
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
  
  .category-table .q-table td {
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
}

.q-avatar:hover {
  transform: scale(1.05);
}

/* Loading states */
.q-btn--loading {
  pointer-events: none;
}

/* Custom chip styles */
.q-chip {
  font-weight: 500;
}

/* Improved table row hover */
.q-table tbody tr:hover {
  background-color: #f8f9fa;
}
</style>
<template>
  <div>
    <div class="row items-center justify-between q-mb-lg">
      <div class="col">
        <br>
        <br>
        <h4 class="text-h4 q-my-none">Gestión de Productos</h4>
        <p class="text-grey-6">Administra el catálogo de productos de tu tienda</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="bi-plus-circle"
          label="Nuevo Producto"
          unelevated
          size="md"
          class="q-px-lg"
          @click="openProductDialog('create')"
        />
      </div>
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <!-- Búsqueda por nombre -->
          <div class="col-md-4 col-sm-6 col-xs-12">
            <q-input
              v-model="searchQuery"
              placeholder="Buscar productos..."
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

          <!-- Filtro por categoría -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-select
              v-model="selectedCategory"
              :options="categoryOptions"
              placeholder="Todas las categorías"
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

    <!-- Tabla de productos -->
    <q-card flat bordered>
      <q-card-section>
        <q-table
          :rows="filteredProducts"
          :columns="columns"
          row-key="id"
          :loading="loading"
          flat
          bordered
          :pagination="pagination"
          @request="onRequest"
        >
          <!-- Imagen del producto -->
          <template v-slot:body-cell-image="props">
            <q-td :props="props">
              <q-avatar size="40px" square>
                <img 
                  :src="getProductImageUrl(props.row.main_image)" 
                  :alt="props.row.name"
                  style="object-fit: cover;"
                />
              </q-avatar>
            </q-td>
          </template>

          <!-- Nombre y descripción -->
          <template v-slot:body-cell-name="props">
            <q-td :props="props">
              <div>
                <div class="text-weight-medium">{{ props.row.name }}</div>
                <div class="text-caption text-grey-6" v-if="props.row.description">
                  {{ truncateText(props.row.description, 50) }}
                </div>
              </div>
            </q-td>
          </template>

          <!-- Categoría -->
          <template v-slot:body-cell-category="props">
            <q-td :props="props">
              <q-chip 
                v-if="props.row.category"
                :label="props.row.category.name"
                color="primary"
                text-color="white"
                size="sm"
              />
              <span v-else class="text-grey-5">Sin categoría</span>
            </q-td>
          </template>

          <!-- Precio -->
          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              <span class="text-weight-medium">
                {{ formatPrice(props.row.price) }}
              </span>
            </q-td>
          </template>

          <!-- Stock con colores -->
          <template v-slot:body-cell-stock="props">
            <q-td :props="props">
              <q-chip
                :label="props.row.stock"
                :color="getStockColor(props.row.stock)"
                text-color="white"
                size="sm"
              />
            </q-td>
          </template>

          <!-- Estado activo/inactivo -->
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-toggle
                :model-value="props.row.is_active"
                @update:model-value="toggleProductStatus(props.row)"
                color="positive"
                :loading="props.row.updating"
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
                  @click="openProductDialog('view', props.row)"
                >
                  <q-tooltip>Ver detalles</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="bi-pencil"
                  size="sm"
                  color="primary"
                  @click="openProductDialog('edit', props.row)"
                >
                  <q-tooltip>Editar</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  round
                  icon="bi-trash"
                  size="sm"
                  color="negative"
                  @click="deleteProduct(props.row)"
                >
                  <q-tooltip>Eliminar</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </template>

          <!-- Mensaje cuando no hay datos -->
          <template v-slot:no-data>
            <div class="full-width row flex-center text-grey-5 q-gutter-sm">
              <q-icon size="2em" name="bi-box" />
              <span>No se encontraron productos</span>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal para agregar/editar/ver producto -->
    <q-dialog 
      v-model="productDialog" 
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
          <q-btn icon="bi-x" flat round dense @click="closeProductDialog" />
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <div class="row q-gutter-lg">
            <!-- Formulario principal -->
            <div class="col-md-8 col-xs-12">
              <q-form @submit="saveProductModal" class="q-gutter-md">
                
                <!-- Información básica -->
                <div class="text-h6 text-primary q-mb-md">
                  <q-icon name="bi-info-circle" class="q-mr-sm" />
                  Información Básica
                </div>

                <!-- Nombre -->
                <q-input
                  v-model="productForm.name"
                  label="Nombre del producto *"
                  outlined
                  :readonly="dialogMode === 'view'"
                  :rules="[val => !!val || 'El nombre es obligatorio']"
                  @input="generateSlug"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-type" />
                  </template>
                </q-input>

                <!-- Slug (generado automáticamente) -->
                <q-input
                  v-model="productForm.slug"
                  label="Slug (URL amigable)"
                  outlined
                  readonly
                  hint="Se genera automáticamente basado en el nombre"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-link" />
                  </template>
                </q-input>

                <!-- Descripción -->
                <q-input
                  v-model="productForm.description"
                  label="Descripción *"
                  type="textarea"
                  outlined
                  rows="4"
                  :readonly="dialogMode === 'view'"
                  :rules="[val => !!val || 'La descripción es obligatoria']"
                  counter
                  maxlength="2000"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-textarea-resize" />
                  </template>
                </q-input>

                <!-- Categoría -->
                <q-select
                  v-model="productForm.category_id"
                  :options="categorySelectOptions"
                  label="Categoría *"
                  outlined
                  emit-value
                  map-options
                  :readonly="dialogMode === 'view'"
                  :rules="[val => !!val || 'La categoría es obligatoria']"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-tags" />
                  </template>
                </q-select>

                <!-- Precio y Stock -->
                <div class="text-h6 text-primary q-mb-md q-mt-lg">
                  <q-icon name="bi-cash" class="q-mr-sm" />
                  Precio y Stock
                </div>

                <div class="row q-gutter-md">
                  <!-- Precio -->
                  <div class="col-md-6 col-xs-12">
                    <q-input
                      v-model.number="productForm.price"
                      label="Precio *"
                      type="number"
                      min="0"
                      step="0.01"
                      outlined
                      :readonly="dialogMode === 'view'"
                      :rules="[val => val >= 0 || 'El precio debe ser mayor o igual a 0']"
                      prefix="$"
                    >
                      <template v-slot:prepend>
                        <q-icon name="bi-currency-dollar" />
                      </template>
                    </q-input>
                  </div>

                  <!-- Stock -->
                  <div class="col-md-6 col-xs-12">
                    <q-input
                      v-model.number="productForm.stock"
                      label="Stock *"
                      type="number"
                      min="0"
                      outlined
                      :readonly="dialogMode === 'view'"
                      :rules="[val => val >= 0 || 'El stock debe ser mayor o igual a 0']"
                    >
                      <template v-slot:prepend>
                        <q-icon name="bi-boxes" />
                      </template>
                    </q-input>
                  </div>
                </div>

                <!-- SKU -->
                <q-input
                  v-model="productForm.sku"
                  label="SKU (opcional)"
                  outlined
                  :readonly="dialogMode === 'view'"
                  hint="Código único del producto"
                >
                  <template v-slot:prepend>
                    <q-icon name="bi-upc" />
                  </template>
                </q-input>

                <!-- Dimensiones -->
                <div class="text-h6 text-primary q-mb-md q-mt-lg">
                  <q-icon name="bi-rulers" class="q-mr-sm" />
                  Dimensiones (opcional)
                </div>

                <div class="row q-gutter-md">
                  <div class="col-md-4 col-xs-12">
                    <q-input
                      v-model.number="productForm.dimensions.width"
                      label="Ancho (cm)"
                      type="number"
                      min="0"
                      step="0.1"
                      outlined
                      :readonly="dialogMode === 'view'"
                    />
                  </div>
                  <div class="col-md-4 col-xs-12">
                    <q-input
                      v-model.number="productForm.dimensions.height"
                      label="Alto (cm)"
                      type="number"
                      min="0"
                      step="0.1"
                      outlined
                      :readonly="dialogMode === 'view'"
                    />
                  </div>
                  <div class="col-md-4 col-xs-12">
                    <q-input
                      v-model.number="productForm.dimensions.depth"
                      label="Profundidad (cm)"
                      type="number"
                      min="0"
                      step="0.1"
                      outlined
                      :readonly="dialogMode === 'view'"
                    />
                  </div>
                </div>

                <!-- Estado activo -->
                <div class="row items-center q-gutter-sm q-mt-md">
                  <q-toggle
                    v-model="productForm.is_active"
                    label="Producto activo"
                    :disable="dialogMode === 'view'"
                    color="positive"
                  />
                  <q-icon 
                    name="bi-question-circle" 
                    color="grey-6" 
                    size="sm"
                  >
                    <q-tooltip>Los productos inactivos no se mostrarán en la tienda</q-tooltip>
                  </q-icon>
                </div>

                <!-- Botones de acción -->
                <div class="row q-gutter-sm justify-end q-mt-xl" v-if="dialogMode !== 'view'">
                  <q-btn 
                    flat 
                    label="Cancelar" 
                    color="grey-7" 
                    @click="closeProductDialog"
                  />
                  <q-btn 
                    type="submit" 
                    :label="dialogMode === 'create' ? 'Crear Producto' : 'Actualizar Producto'"
                    color="primary"
                    :loading="saving"
                    icon-right="bi-save"
                  />
                </div>

              </q-form>
            </div>

            <!-- Panel de imágenes -->
            <div class="col-md-4 col-xs-12">
              <div class="text-h6 text-primary q-mb-md">
                <q-icon name="bi-images" class="q-mr-sm" />
                Imágenes del Producto
              </div>

              <!-- Imagen principal -->
              <div class="q-mb-lg">
                <div class="text-subtitle2 q-mb-sm">Imagen Principal</div>
                <div class="image-upload-container">
                  <div v-if="productForm.main_image" class="image-preview">
                    <img 
                      :src="getProductImageUrl(productForm.main_image)" 
                      alt="Imagen principal"
                      class="preview-image"
                    />
                    <div v-if="dialogMode !== 'view'" class="image-overlay">
                      <q-btn
                        round
                        color="negative"
                        icon="bi-trash"
                        size="sm"
                        @click="removeMainImage"
                      />
                    </div>
                  </div>
                  <div v-else class="image-placeholder">
                    <q-icon name="bi-image" size="3em" color="grey-5" />
                    <div class="text-grey-5">Sin imagen</div>
                  </div>
                  
                  <q-btn v-if="dialogMode !== 'view'"
                    color="primary"
                    icon="bi-upload"
                    label="Seleccionar Imagen"
                    class="q-mt-sm full-width"
                    @click="selectMainImage"
                  />
                  <input
                    ref="mainImageInput"
                    type="file"
                    accept="image/*"
                    style="display: none"
                    @change="handleMainImageUpload"
                  />
                </div>
              </div>

              <!-- Galería de imágenes -->
              <div>
                <div class="text-subtitle2 q-mb-sm">Galería (opcional)</div>
                <div class="gallery-container">
                  <div v-for="(image, index) in productForm.gallery" :key="index" class="gallery-item">
                    <img 
                      :src="getProductImageUrl(image)" 
                      alt="Imagen de galería"
                      class="gallery-image"
                    />
                    <div v-if="dialogMode !== 'view'" class="image-overlay">
                      <q-btn
                        round
                        color="negative"
                        icon="bi-trash"
                        size="xs"
                        @click="removeGalleryImage(index)"
                      />
                    </div>
                  </div>
                  
                  <div v-if="dialogMode !== 'view'" class="gallery-add">
                    <q-btn
                      color="primary"
                      icon="bi-plus-circle"
                      flat
                      class="full-width full-height"
                      @click="selectGalleryImage"
                    />
                  </div>
                </div>
                
                <input
                  ref="galleryImageInput"
                  type="file"
                  accept="image/*"
                  multiple
                  style="display: none"
                  @change="handleGalleryImageUpload"
                />
              </div>
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
const products = ref([])
const categories = ref([])
const productDialog = ref(false)
const dialogMode = ref('create') // 'create', 'edit', 'view'
const selectedProduct = ref(null)

// Referencias para inputs de archivos
const mainImageInput = ref(null)
const galleryImageInput = ref(null)

// Filtros
const searchQuery = ref('')
const selectedCategory = ref(null)
const statusFilter = ref(null)

// Formulario de producto
const productForm = reactive({
  name: '',
  slug: '',
  description: '',
  price: 0,
  stock: 0,
  sku: '',
  dimensions: {
    width: null,
    height: null,
    depth: null
  },
  main_image: '',
  gallery: [],
  category_id: null,
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
    name: 'image',
    label: 'Imagen',
    field: 'main_image',
    align: 'center',
    sortable: false
  },
  {
    name: 'name',
    label: 'Producto',
    field: 'name',
    align: 'left',
    sortable: true
  },
  {
    name: 'category',
    label: 'Categoría',
    field: row => row.category?.name || '',
    align: 'left',
    sortable: true
  },
  {
    name: 'price',
    label: 'Precio',
    field: 'price',
    align: 'right',
    sortable: true
  },
  {
    name: 'stock',
    label: 'Stock',
    field: 'stock',
    align: 'center',
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

// Opciones para los filtros
const categoryOptions = computed(() => [
  { label: 'Todas las categorías', value: null },
  ...categories.value.map(cat => ({
    label: cat.name,
    value: cat.id
  }))
])

const categorySelectOptions = computed(() => 
  categories.value.map(cat => ({
    label: cat.name,
    value: cat.id
  }))
)

const statusOptions = [
  { label: 'Todos los estados', value: null },
  { label: 'Activos', value: true },
  { label: 'Inactivos', value: false }
]

// Productos filtrados
const filteredProducts = computed(() => {
  let filtered = [...products.value]

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product =>
      product.name.toLowerCase().includes(query) ||
      (product.description && product.description.toLowerCase().includes(query))
    )
  }

  // Filtrar por categoría
  if (selectedCategory.value) {
    filtered = filtered.filter(product =>
      product.category_id === selectedCategory.value
    )
  }

  // Filtrar por estado
  if (statusFilter.value !== null) {
    filtered = filtered.filter(product =>
      product.is_active === statusFilter.value
    )
  }

  return filtered
})

// Verificar si hay filtros activos
const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedCategory.value || statusFilter.value !== null
})

const dialogTitle = computed(() => {
  switch (dialogMode.value) {
    case 'create': return 'Nuevo Producto'
    case 'edit': return 'Editar Producto'
    case 'view': return 'Detalles del Producto'
    default: return 'Producto'
  }
})

// Cargar productos desde la API
const loadProducts = async () => {
  loading.value = true
  try {
    const response = await api.get('/products?admin_view=1')
    
    if (response.data?.data) {
      products.value = response.data.data
      if (response.data.total) {
        pagination.value.rowsNumber = response.data.total
      }
    } else if (Array.isArray(response.data)) {
      products.value = response.data
    } else {
      products.value = []
    }

    $q.notify({
      type: 'positive',
      message: `${products.value.length} productos cargados correctamente`
    })

  } catch (error) {
    console.error('Error loading products:', error)
    
    let errorMessage = 'Error al cargar productos'
    
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
      message: errorMessage
    })
    products.value = []
  } finally {
    loading.value = false
  }
}

// Cargar categorías
const loadCategories = async () => {
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
  }
}

// Generar slug automáticamente
const generateSlug = () => {
  if (productForm.name) {
    productForm.slug = productForm.name
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim('-')
  }
}

// Abrir modal de producto
const openProductDialog = (mode, product = null) => {
  dialogMode.value = mode
  selectedProduct.value = product
  
  if (mode === 'create') {
    // Resetear formulario
    Object.assign(productForm, {
      name: '',
      slug: '',
      description: '',
      price: 0,
      stock: 0,
      sku: '',
      dimensions: {
        width: null,
        height: null,
        depth: null
      },
      main_image: '',
      gallery: [],
      category_id: null,
      is_active: true
    })
  } else if (product) {
    // Cargar datos del producto
    Object.assign(productForm, {
      name: product.name || '',
      slug: product.slug || '',
      description: product.description || '',
      price: product.price || 0,
      stock: product.stock || 0,
      sku: product.sku || '',
      dimensions: product.dimensions || { width: null, height: null, depth: null },
      main_image: product.main_image || '',
      gallery: product.gallery || [],
      category_id: product.category_id || null,
      is_active: product.is_active ?? true
    })
  }
  
  productDialog.value = true
}

// Cerrar modal
const closeProductDialog = () => {
  productDialog.value = false
  selectedProduct.value = null
  saving.value = false
}

// Guardar producto
const saveProductModal = async () => {
  saving.value = true
  
  try {
    let response
    
    // Preparar datos para enviar
    const formData = {
      ...productForm,
      dimensions: productForm.dimensions.width || productForm.dimensions.height || productForm.dimensions.depth 
        ? productForm.dimensions 
        : null
    }
    
    if (dialogMode.value === 'create') {
      response = await api.post('/products', formData)
      
      // Agregar nuevo producto al array
      if (response.data?.product) {
        products.value.unshift(response.data.product)
      }
      
    } else if (dialogMode.value === 'edit' && selectedProduct.value) {
      response = await api.put(`/products/${selectedProduct.value.id}`, formData)
      
      // Actualizar producto en el array
      const index = products.value.findIndex(p => p.id === selectedProduct.value.id)
      if (index !== -1 && response.data?.product) {
        products.value[index] = response.data.product
      }
    }

    $q.notify({
      type: 'positive',
      message: response.data?.message || 'Operación completada correctamente',
      icon: 'bi-check-circle'
    })

    closeProductDialog();
    loadProducts();


  } catch (error) {
    console.error('Error saving product:', error)
    
    let errorMessage = 'Error al guardar el producto'
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

// Funciones para manejo de imágenes
const selectMainImage = () => {
  mainImageInput.value?.click()
}

const selectGalleryImage = () => {
  galleryImageInput.value?.click()
}

const handleMainImageUpload = async (event) => {
  const file = event.target.files[0]
  if (file) {
    const uploadedUrl = await uploadImage(file)
    if (uploadedUrl) {
      productForm.main_image = uploadedUrl
    }
  }
}

const handleGalleryImageUpload = async (event) => {
  const files = Array.from(event.target.files)
  for (const file of files) {
    const uploadedUrl = await uploadImage(file)
    if (uploadedUrl) {
      productForm.gallery.push(uploadedUrl)
    }
  }
}

const uploadImage = async (file) => {
  const formData = new FormData()
  formData.append('image', file)
  
  try {
    // Crear endpoint para subir imágenes
    const response = await api.post('/upload-product-image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    return response.data.path
  } catch (error) {
    console.error('Error uploading image:', error)
    $q.notify({
      type: 'negative',
      message: 'Error al subir la imagen'
    })
    return null
  }
}

const removeMainImage = () => {
  productForm.main_image = ''
}

const removeGalleryImage = (index) => {
  productForm.gallery.splice(index, 1)
}

// Cambiar estado activo/inactivo
const toggleProductStatus = async (product) => {
  product.updating = true
  try {
    const response = await api.patch(`/products/${product.id}/toggle-active`)
    
    // Actualizar el producto en el array local
    const index = products.value.findIndex(p => p.id === product.id)
    if (index !== -1) {
      products.value[index].is_active = !products.value[index].is_active
    }

    const message = response.data?.message || 
      `Producto ${products.value[index].is_active ? 'activado' : 'desactivado'} correctamente`

    $q.notify({
      type: 'positive',
      message: message
    })

  } catch (error) {
    console.error('Error toggling product status:', error)
    
    let errorMessage = 'Error al cambiar el estado del producto'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    
    $q.notify({
      type: 'negative',
      message: errorMessage
    })
  } finally {
    product.updating = false
  }
}

// Eliminar producto
const deleteProduct = (product) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: `¿Estás seguro de eliminar el producto "${product.name}"?`,
    cancel: true,
    persistent: true,
    ok: {
      label: 'Eliminar',
      color: 'negative',
      icon: 'bi-trash'
    },
    cancel: {
      label: 'Cancelar',
      color: 'grey',
      flat: true
    }
  }).onOk(async () => {
    const loadingNotify = $q.notify({
      type: 'ongoing',
      message: 'Eliminando producto...',
      timeout: 0,
      spinner: true
    })
    
    try {
      const response = await api.delete(`/products/${product.id}`)
      
      // Remover el producto del array local
      const index = products.value.findIndex(p => p.id === product.id)
      if (index !== -1) {
        products.value.splice(index, 1)
      }

      loadingNotify()
      
      const message = response.data?.message || 'Producto eliminado correctamente'
      $q.notify({
        type: 'positive',
        message: message,
        icon: 'bi-check-circle'
      })
      
    } catch (error) {
      console.error('Error deleting product:', error)
      
      loadingNotify()
      
      let errorMessage = 'Error al eliminar el producto'
      if (error.response?.data?.message) {
        errorMessage = error.response.data.message
      }
      
      $q.notify({
        type: 'negative',
        message: errorMessage,
        icon: 'bi-exclamation-triangle'
      })
    }
  })
}

// Aplicar filtros
const applyFilters = () => {
  console.log('Filtros aplicados:', {
    search: searchQuery.value,
    category: selectedCategory.value,
    status: statusFilter.value
  })
}

// Limpiar filtros
const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = null
  statusFilter.value = null
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

const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png'
  if (main_image.startsWith('http')) return main_image
  if (main_image.startsWith('public/')) {
    return '/storage/' + main_image.replace('public/', '')
  }
  if (main_image.startsWith('storage/app/public/')) {
    return '/storage/' + main_image.replace('storage/app/public/', '')
  }
  if (main_image.startsWith('products/')) {
    return '/storage/' + main_image
  }
  return main_image
}

const getStockColor = (stock) => {
  if (stock === 0) return 'negative'
  if (stock <= 5) return 'warning'
  return 'positive'
}

const truncateText = (text, maxLength) => {
  if (!text) return ''
  return text.length > maxLength ? text.slice(0, maxLength) + '...' : text
}

// Cargar datos al montar el componente
onMounted(async () => {
  await Promise.all([
    loadProducts(),
    loadCategories()
  ])
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

/* Estilos para manejo de imágenes */
.image-upload-container {
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
  transition: border-color 0.3s ease;
}

.image-upload-container:hover {
  border-color: #1976d2;
}

.image-preview {
  position: relative;
  display: inline-block;
  width: 100%;
  max-width: 200px;
}

.preview-image {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
}

.image-placeholder {
  height: 150px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: #f5f5f5;
  border-radius: 8px;
}

.image-overlay {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  padding: 4px;
}

.gallery-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 8px;
  margin-top: 8px;
}

.gallery-item {
  position: relative;
  aspect-ratio: 1;
}

.gallery-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 4px;
}

.gallery-add {
  aspect-ratio: 1;
  border: 2px dashed #e0e0e0;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 80px;
}

.gallery-add:hover {
  border-color: #1976d2;
  background-color: #f8f9fa;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .q-dialog .q-card {
    margin: 0;
    height: 100vh;
    max-height: 100vh;
  }
  
  .gallery-container {
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
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
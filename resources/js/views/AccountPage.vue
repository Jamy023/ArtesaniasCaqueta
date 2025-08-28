<template>
  <div class="account-page">
  
    <div class="account-header">
      <img src="https://res.cloudinary.com/dbjmhh4wr/image/upload/v1756001294/fondo_y3qqeq.png" alt="Fondo Amazonía" class="account-header-bg-img" />
      <div class="account-header-overlay"></div>
      <div class="container">
        <div class="header-content">
          <div class="user-info">
            <div class="user-avatar">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <div class="user-details">
              <h1 class="user-name">{{ currentUser?.nombre }} {{ currentUser?.apellido }}</h1>
              <p class="user-email">{{ currentUser?.email }}</p>
              <p class="user-since">Miembro desde {{ accountData?.cuenta_creada || 'N/A' }}</p>
            </div>
          </div>
          <div class="account-status">
            <span class="status-badge active">{{ accountData?.estado_cuenta || 'Activa' }}</span>
          </div>
        </div>
      </div>
    </div>

  
    <div class="account-nav">
      <div class="container">
        <div class="nav-tabs">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="['tab-btn', { active: activeTab === tab.id }]"
          >
            <component :is="tab.icon" class="tab-icon" />
            {{ tab.label }}
          </button>
        </div>
      </div>
    </div>

  
    <div class="account-content">
      <div class="container">
        
        <!-- Resumen de Cuenta -->
        <div v-if="activeTab === 'overview'" class="tab-content">
          <div class="content-header">
            <h2>Resumen de tu cuenta</h2>
            <p>Una vista general de tu actividad en nuestra tienda</p>
          </div>
          
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon orders">
                <ShoppingBag />
              </div>
              <div class="stat-info">
                <h3>{{ accountData?.total_pedidos || 0 }}</h3>
                <p>Pedidos realizados</p>
              </div>
            </div>
            
            <div class="stat-card">
              <div class="stat-icon completed">
                <CheckCircle />
              </div>
              <div class="stat-info">
                <h3>{{ accountData?.pedidos_completados || 0 }}</h3>
                <p>Pedidos completados</p>
              </div>
            </div>
            
            <div class="stat-card">
              <div class="stat-icon money">
                <DollarSign />
              </div>
              <div class="stat-info">
                <h3>${{ formatNumber(accountData?.monto_total_gastado || 0) }}</h3>
                <p>Total invertido</p>
              </div>
            </div>
            
            <div class="stat-card">
              <div class="stat-icon time">
                <Clock />
              </div>
              <div class="stat-info">
                <h3>{{ accountData?.tiempo_registrado || 'N/A' }}</h3>
                <p>Tiempo con nosotros</p>
              </div>
            </div>
          </div>

          <div class="quick-actions">
            <h3>Acciones rápidas</h3>
            <div class="actions-grid">
              <button @click="activeTab = 'profile'" class="action-card">
                <User class="action-icon" />
                <span>Actualizar perfil</span>
              </button>
              
              <button @click="activeTab = 'security'" class="action-card">
                <Shield class="action-icon" />
                <span>Cambiar contraseña</span>
              </button>
              
              <button @click="activeTab = 'addresses'" class="action-card">
                <MapPin class="action-icon" />
                <span>Gestionar direcciones</span>
              </button>
              
              <button @click="$router.push('/products')" class="action-card">
                <Package class="action-icon" />
                <span>Explorar productos</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Información Personal -->
        <div v-if="activeTab === 'profile'" class="tab-content">
          <div class="content-header">
            <h2>Información personal</h2>
            <p>Mantén actualizados tus datos personales</p>
          </div>
          
          <form @submit.prevent="updateProfile" class="profile-form">
            <div class="form-grid">
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input 
                  id="nombre"
                  v-model="profileForm.nombre" 
                  type="text" 
                  required
                  :disabled="profileLoading"
                />
                <span v-if="profileErrors.nombre" class="error">{{ profileErrors.nombre[0] }}</span>
              </div>
              
              <div class="form-group">
                <label for="apellido">Apellido</label>
                <input 
                  id="apellido"
                  v-model="profileForm.apellido" 
                  type="text" 
                  required
                  :disabled="profileLoading"
                />
                <span v-if="profileErrors.apellido" class="error">{{ profileErrors.apellido[0] }}</span>
              </div>
              
              <div class="form-group full-width">
                <label for="email">Correo electrónico</label>
                <input 
                  id="email"
                  v-model="profileForm.email" 
                  type="email" 
                  required
                  :disabled="profileLoading"
                />
                <span v-if="profileErrors.email" class="error">{{ profileErrors.email[0] }}</span>
              </div>
              
              <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input 
                  id="telefono"
                  v-model="profileForm.telefono" 
                  type="tel"
                  :disabled="profileLoading"
                />
                <span v-if="profileErrors.telefono" class="error">{{ profileErrors.telefono[0] }}</span>
              </div>
              
              <div class="form-group">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <input 
                  id="fecha_nacimiento"
                  v-model="profileForm.fecha_nacimiento" 
                  type="date"
                  :disabled="profileLoading"
                />
              </div>
              
              <div class="form-group full-width">
                <label for="direccion">Dirección</label>
                <input 
                  id="direccion"
                  v-model="profileForm.direccion" 
                  type="text"
                  :disabled="profileLoading"
                />
              </div>
              
              <div class="form-group">
                <label for="ciudad">Ciudad</label>
                <input 
                  id="ciudad"
                  v-model="profileForm.ciudad" 
                  type="text"
                  :disabled="profileLoading"
                />
              </div>
              
              <div class="form-group">
                <label for="departamento">Departamento</label>
                <input 
                  id="departamento"
                  v-model="profileForm.departamento" 
                  type="text"
                  :disabled="profileLoading"
                />
              </div>
            </div>
            
            <div class="form-actions">
              <button type="submit" :disabled="profileLoading" class="btn-primary">
                <Loader v-if="profileLoading" class="spinning" />
                {{ profileLoading ? 'Guardando...' : 'Guardar cambios' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Seguridad -->
        <div v-if="activeTab === 'security'" class="tab-content">
          <div class="content-header">
            <h2>Seguridad de la cuenta</h2>
            <p>Mantén tu cuenta segura actualizando tu contraseña</p>
          </div>
          
          <form @submit.prevent="changePassword" class="security-form">
            <div class="form-group">
              <label for="current_password">Contraseña actual</label>
              <input 
                id="current_password"
                v-model="passwordForm.current_password" 
                type="password" 
                required
                :disabled="passwordLoading"
              />
              <span v-if="passwordErrors.current_password" class="error">{{ passwordErrors.current_password[0] }}</span>
            </div>
            
            <div class="form-group">
              <label for="new_password">Nueva contraseña</label>
              <input 
                id="new_password"
                v-model="passwordForm.new_password" 
                type="password" 
                required
                minlength="8"
                :disabled="passwordLoading"
              />
              <span v-if="passwordErrors.new_password" class="error">{{ passwordErrors.new_password[0] }}</span>
            </div>
            
            <div class="form-group">
              <label for="new_password_confirmation">Confirmar nueva contraseña</label>
              <input 
                id="new_password_confirmation"
                v-model="passwordForm.new_password_confirmation" 
                type="password" 
                required
                minlength="8"
                :disabled="passwordLoading"
              />
            </div>
            
            <div class="form-actions">
              <button type="submit" :disabled="passwordLoading" class="btn-primary">
                <Loader v-if="passwordLoading" class="spinning" />
                {{ passwordLoading ? 'Cambiando...' : 'Cambiar contraseña' }}
              </button>
            </div>
          </form>

          <!-- Sección de eliminación de cuenta -->
          <div class="danger-zone">
            <h3>Zona peligrosa</h3>
            <p>Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, ten cuidado.</p>
            <button @click="showDeleteModal = true" class="btn-danger">
              <Trash2 />
              Eliminar cuenta
            </button>
          </div>
        </div>

        <!-- Direcciones -->
        <div v-if="activeTab === 'addresses'" class="tab-content">
          <div class="content-header">
            <h2>Mis direcciones</h2>
            <p>Gestiona las direcciones de entrega de tus pedidos</p>
          </div>
          
          <div v-if="addresses.length === 0" class="empty-state">
            <MapPin class="empty-icon" />
            <h3>No tienes direcciones guardadas</h3>
            <p>Agrega tu primera dirección para facilitar tus compras</p>
            <button @click="showAddressModal = true" class="btn-primary">
              <Plus />
              Agregar dirección
            </button>
          </div>
          
          <div v-else class="addresses-list">
            <div v-for="address in addresses" :key="address.id" class="address-card">
              <div class="address-info">
                <div class="address-header">
                  <h4>{{ address.tipo }}</h4>
                  <span v-if="address.es_principal" class="badge-primary">Principal</span>
                </div>
                <p class="address-text">{{ address.direccion }}</p>
                <p class="address-location">{{ address.ciudad }}, {{ address.departamento }}</p>
              </div>
              <div class="address-actions">
                <button @click="editAddress(address)" class="btn-outline-sm">
                  <Edit3 />
                  Editar
                </button>
                <button v-if="!address.es_principal" @click="deleteAddress(address.id)" class="btn-danger-sm">
                  <Trash2 />
                </button>
              </div>
            </div>
            
            <button @click="showAddressModal = true" class="btn-outline add-address-btn">
              <Plus />
              Agregar nueva dirección
            </button>
          </div>
        </div>

        <!-- Historial de Pedidos -->
        <div v-if="activeTab === 'orders'" class="tab-content">
          <div class="content-header">
            <h2>Historial de pedidos</h2>
            <p>Revisa el estado de tus compras</p>
          </div>
          
          <!-- Loading estado -->
          <div v-if="ordersLoading" class="loading-state">
            <Loader class="spinning" />
            <p>Cargando pedidos...</p>
          </div>
          
          <!-- Estado vacío -->
          <div v-else-if="orders.length === 0" class="empty-state">
            <Package class="empty-icon" />
            <h3>Aún no has realizado pedidos</h3>
            <p>¡Explora nuestras hermosas artesanías amazónicas y haz tu primera compra!</p>
            <button @click="$router.push('/products')" class="btn-primary">
              <ShoppingBag />
              Explorar productos
            </button>
          </div>
          
          <!-- Lista de pedidos -->
          <div v-else class="orders-list">
            <div v-for="order in validOrders" :key="order.id" class="order-card">
              <!-- Encabezado del pedido -->
              <div class="order-header">
                <div class="order-info">
                  <h3 class="order-number">Pedido #{{ order.id }}</h3>
                  <p class="order-date">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="order-status">
                  <span :class="['status-badge', getStatusClass(order.status)]">
                    {{ getStatusText(order.status) }}
                  </span>
                </div>
              </div>
              
              <!-- Detalles del pedido -->
              <div class="order-body">
                <div class="order-summary">
                  <div class="summary-item">
                    <span class="label">Total de productos:</span>
                    <span class="value">{{ order.items?.length || 0 }} artículo(s)</span>
                  </div>
                  <div class="summary-item">
                    <span class="label">Método de pago:</span>
                    <span class="value">{{ order.payment_method || 'No especificado' }}</span>
                  </div>
                  <div class="summary-item">
                    <span class="label">Total pagado:</span>
                    <span class="value total">${{ formatNumber(order.total_amount || 0) }}</span>
                  </div>
                </div>
                
                <!-- Dirección de envío -->
                <div v-if="order.direccion_envio" class="shipping-address">
                  <h4>Dirección de envío</h4>
                  <p>{{ order.direccion_envio.direccion }}</p>
                  <p>{{ order.direccion_envio.ciudad }}, {{ order.direccion_envio.departamento }}</p>
                </div>
              </div>
              
              <!-- Acciones del pedido -->
              <div class="order-actions">
                <button @click="viewOrderDetails(order)" class="btn-outline-sm">
                  <Package />
                  Ver detalles
                </button>
                <button 
                  v-if="canCancelOrder(order)" 
                  @click="cancelOrder(order.id)" 
                  class="btn-danger-sm"
                >
                  <X />
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal de Detalles del Pedido -->
    <div v-if="showOrderDetailsModal" class="modal-overlay" @click="showOrderDetailsModal = false">
      <div class="modal-content order-details-modal" @click.stop>
        <div class="modal-header">
          <h3>Detalles del Pedido #{{ selectedOrder?.id }}</h3>
          <button @click="showOrderDetailsModal = false" class="modal-close">
            <X />
          </button>
        </div>
        
        <div class="modal-body" v-if="selectedOrder">
          <!-- Información general del pedido -->
          <div class="order-details-section">
            <h4>Información General</h4>
            <div class="details-grid">
              <div class="detail-item">
                <span class="detail-label">Número de pedido:</span>
                <span class="detail-value">#{{ selectedOrder.id }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Fecha:</span>
                <span class="detail-value">{{ formatDate(selectedOrder.created_at) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Estado:</span>
                <span :class="['detail-badge', getStatusClass(selectedOrder.status)]">
                  {{ getStatusText(selectedOrder.status) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Método de pago:</span>
                <span class="detail-value">{{ selectedOrder.payment_method || 'No especificado' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Total:</span>
                <span class="detail-value total">${{ formatNumber(selectedOrder.total_amount || 0) }}</span>
              </div>
            </div>
          </div>

          <!-- Productos del pedido -->
          <div class="order-details-section" v-if="selectedOrder.items && selectedOrder.items.length > 0">
            <h4>Productos ({{ selectedOrder.items.length }})</h4>
            <div class="products-list">
              <div v-for="item in selectedOrder.items" :key="item.id" class="product-item">
                <div class="product-image">
                  <img v-if="item.image" 
                       :src="getProductImageUrl(item.image)" 
                       :alt="item.name"
                       @error="handleImageError" />
                  <div v-else class="no-image">
                    <Package />
                  </div>
                </div>
                <div class="product-info">
                  <h5>{{ item.name || 'Producto no disponible' }}</h5>
                  <p class="product-description">{{ item.description || '' }}</p>
                  <div class="product-details">
                    <span class="quantity">Cantidad: {{ item.quantity }}</span>
                    <span class="price">${{ formatNumber(item.price || 0) }} c/u</span>
                    <span class="subtotal">${{ formatNumber((item.quantity || 0) * (item.price || 0)) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dirección de envío -->
          <div class="order-details-section" v-if="selectedOrder.direccion_envio">
            <h4>Dirección de Envío</h4>
            <div class="shipping-details">
              <p><strong>Dirección:</strong> {{ selectedOrder.direccion_envio.direccion }}</p>
              <p><strong>Ciudad:</strong> {{ selectedOrder.direccion_envio.ciudad }}</p>
              <p><strong>Departamento:</strong> {{ selectedOrder.direccion_envio.departamento }}</p>
              <p v-if="selectedOrder.direccion_envio.codigo_postal">
                <strong>Código Postal:</strong> {{ selectedOrder.direccion_envio.codigo_postal }}
              </p>
            </div>
          </div>

          <!-- Historial de estados (si existe) -->
          <div class="order-details-section" v-if="selectedOrder.historial_estados && selectedOrder.historial_estados.length > 0">
            <h4>Historial de Estados</h4>
            <div class="status-timeline">
              <div v-for="estado in selectedOrder.historial_estados" :key="estado.id" class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                  <span class="timeline-status">{{ getStatusText(estado.estado) }}</span>
                  <span class="timeline-date">{{ formatDate(estado.created_at) }}</span>
                  <p v-if="estado.comentario" class="timeline-comment">{{ estado.comentario }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-actions">
          <button @click="showOrderDetailsModal = false" class="btn-outline">
            Cerrar
          </button>
          <button 
            v-if="canCancelOrder(selectedOrder)" 
            @click="cancelOrderFromModal(selectedOrder.id)" 
            class="btn-danger"
          >
            Cancelar Pedido
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación para Eliminar Cuenta -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="showDeleteModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Eliminar cuenta</h3>
          <button @click="showDeleteModal = false" class="modal-close">
            <X />
          </button>
        </div>
        
        <div class="modal-body">
          <div class="warning-message">
            <AlertTriangle class="warning-icon" />
            <p><strong>¡Atención!</strong> Esta acción no se puede deshacer. Se eliminarán todos tus datos permanentemente.</p>
          </div>
          
          <form @submit.prevent="deleteAccount">
            <div class="form-group">
              <label for="delete_password">Confirma tu contraseña</label>
              <input 
                id="delete_password"
                v-model="deleteForm.password" 
                type="password" 
                required
                :disabled="deleteLoading"
              />
              <span v-if="deleteErrors.password" class="error">{{ deleteErrors.password[0] }}</span>
            </div>
            
            <div class="form-group">
              <label for="delete_confirmation">Escribe "ELIMINAR" para confirmar</label>
              <input 
                id="delete_confirmation"
                v-model="deleteForm.confirmation" 
                type="text" 
                required
                placeholder="ELIMINAR"
                :disabled="deleteLoading"
              />
              <span v-if="deleteErrors.confirmation" class="error">{{ deleteErrors.confirmation[0] }}</span>
            </div>
            
            <div class="modal-actions">
              <button type="button" @click="showDeleteModal = false" class="btn-outline">
                Cancelar
              </button>
              <button type="submit" :disabled="deleteLoading || deleteForm.confirmation !== 'ELIMINAR'" class="btn-danger">
                <Loader v-if="deleteLoading" class="spinning" />
                {{ deleteLoading ? 'Eliminando...' : 'Eliminar cuenta' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div v-if="notification.show" :class="['toast', notification.type]">
      <div class="toast-content">
        <CheckCircle v-if="notification.type === 'success'" class="toast-icon" />
        <AlertTriangle v-else class="toast-icon" />
        <span>{{ notification.message }}</span>
      </div>
      <button @click="hideNotification" class="toast-close">
        <X />
      </button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import fondoImage from '/public/img/fondo.webp';
import '../css/Acount.css';

import axios from 'axios';
import {
  User, Shield, MapPin, Package, ShoppingBag, Settings,
  Edit3, Trash2, Plus, X, CheckCircle, AlertTriangle,
  Clock, DollarSign, Loader
} from 'lucide-vue-next';
import { getProductImageUrl, handleImageError } from '../utils/imageUtils.js';

export default {
  name: 'AccountPage',
  components: {
    User, Shield, MapPin, Package, ShoppingBag, Settings,
    Edit3, Trash2, Plus, X, CheckCircle, AlertTriangle,
    Clock, DollarSign, Loader
  },
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    
    // Estados reactivos
    const activeTab = ref('overview');
    const loading = ref(true);
    const profileLoading = ref(false);
    const passwordLoading = ref(false);
    const deleteLoading = ref(false);
    const ordersLoading = ref(false); // Estado de carga específico para pedidos
    const showDeleteModal = ref(false);
    const showAddressModal = ref(false);
    const showOrderDetailsModal = ref(false); // Modal para detalles del pedido
    
    // Datos
    const accountData = ref(null);
    const addresses = ref([]);
    const orders = ref([]); // Array para almacenar los pedidos del cliente
    const selectedOrder = ref(null); // Pedido seleccionado para ver detalles
    
    // Formularios
    const profileForm = reactive({
      nombre: '',
      apellido: '',
      email: '',
      telefono: '',
      fecha_nacimiento: '',
      direccion: '',
      ciudad: '',
      departamento: ''
    });
    
    const passwordForm = reactive({
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    });
    
    const deleteForm = reactive({
      password: '',
      confirmation: ''
    });
    
    // Errores
    const profileErrors = ref({});
    const passwordErrors = ref({});
    const deleteErrors = ref({});
    
    // Notificaciones
    const notification = reactive({
      show: false,
      type: 'success',
      message: ''
    });
    
    // Computed
    const currentUser = computed(() => authStore.currentUser);
    
    /**
     * Computed que filtra solo los pedidos válidos (no null y con id)
     * Previene errores de renderizado cuando hay datos corruptos
     */
    const validOrders = computed(() => {
      if (!Array.isArray(orders.value)) return [];
      
      return orders.value.filter(order => {
        // Verificar que el pedido existe y tiene las propiedades mínimas requeridas
        return order && 
               order.id && 
               typeof order.id !== 'undefined' &&
               order.id !== null;
      });
    });
    
    // Configuración de tabs
    const tabs = [
      { id: 'overview', label: 'Resumen', icon: 'User' },
      { id: 'profile', label: 'Perfil', icon: 'User' },
      { id: 'security', label: 'Seguridad', icon: 'Shield' },
      { id: 'addresses', label: 'Direcciones', icon: 'MapPin' },
      { id: 'orders', label: 'Pedidos', icon: 'Package' }
    ];
    
    // Métodos
    const loadAccountData = async () => {
      try {
        const response = await axios.get('/clientes/account/overview');
        accountData.value = response.data.data;
      } catch (error) {
        console.error('Error cargando datos de cuenta:', error);
        showNotification('Error cargando datos de cuenta', 'error');
      }
    };
    
    const loadAddresses = async () => {
      try {
        const response = await axios.get('/clientes/addresses');
        addresses.value = response.data.addresses;
      } catch (error) {
        console.error('Error cargando direcciones:', error);
      }
    };
    
    /**
     * Carga los pedidos del cliente autenticado
     * Realiza una petición GET al endpoint de pedidos del cliente
     */
    const loadOrders = async () => {
      ordersLoading.value = true;
      try {
        console.log('🔄 Iniciando carga de pedidos...');
        
        // Realizar petición al endpoint correcto de pedidos del cliente autenticado
        const response = await axios.get('/orders');
        
        console.log('📦 Respuesta completa del servidor:', response);
        console.log('📦 Datos de la respuesta:', response.data);
        
        // Almacenar los pedidos en el estado reactivo
        // El endpoint /orders devuelve los pedidos en formato paginado de Laravel
        let ordersData = response.data.orders?.data || response.data.data || response.data.orders || response.data || [];
        
        // Asegurar que ordersData es un array
        if (!Array.isArray(ordersData)) {
          console.warn('⚠️ Los datos de pedidos no son un array:', ordersData);
          ordersData = [];
        }
        
        // Filtrar pedidos válidos antes de asignar
        const validOrdersData = ordersData.filter(order => order && order.id);
        
        orders.value = validOrdersData;
        
        console.log('✅ Pedidos válidos cargados:', orders.value.length, 'de', ordersData.length);
        console.log('📋 Pedidos detallados:', orders.value);
        
      } catch (error) {
        console.error('❌ Error cargando pedidos:', error);
        console.error('❌ Respuesta del error:', error.response?.data);
        
        // Si hay un error, mostrar notificación al usuario
        showNotification('Error cargando el historial de pedidos', 'error');
        
        // Asegurar que orders esté vacío en caso de error
        orders.value = [];
      } finally {
        // Siempre detener el estado de carga
        ordersLoading.value = false;
        console.log('🏁 Carga de pedidos completada');
      }
    };
    
    const initializeProfileForm = () => {
      if (currentUser.value) {
        Object.assign(profileForm, {
          nombre: currentUser.value.nombre || '',
          apellido: currentUser.value.apellido || '',
          email: currentUser.value.email || '',
          telefono: currentUser.value.telefono || '',
          fecha_nacimiento: currentUser.value.fecha_nacimiento || '',
          direccion: currentUser.value.direccion || '',
          ciudad: currentUser.value.ciudad || '',
          departamento: currentUser.value.departamento || ''
        });
      }
    };
    
    const updateProfile = async () => {
      profileLoading.value = true;
      profileErrors.value = {};
      
      try {
        await authStore.updateProfile(profileForm);
        showNotification('Perfil actualizado exitosamente', 'success');
      } catch (error) {
        profileErrors.value = error.response?.data?.errors || {};
        showNotification('Error actualizando perfil', 'error');
      } finally {
        profileLoading.value = false;
      }
    };
    
    const changePassword = async () => {
      passwordLoading.value = true;
      passwordErrors.value = {};
      
      try {
        await axios.post('/clientes/change-password', passwordForm);
        
        // Limpiar formulario
        Object.assign(passwordForm, {
          current_password: '',
          new_password: '',
          new_password_confirmation: ''
        });
        
        showNotification('Contraseña actualizada. Redirigiendo al login...', 'success');
        
        // Logout automático después de cambiar contraseña
        setTimeout(async () => {
          await authStore.logout();
          router.push('/');
        }, 2000);
        
      } catch (error) {
        passwordErrors.value = error.response?.data?.errors || {};
        showNotification('Error cambiando contraseña', 'error');
      } finally {
        passwordLoading.value = false;
      }
    };
    
    const deleteAccount = async () => {
      deleteLoading.value = true;
      deleteErrors.value = {};
      
      try {
        await axios.delete('/clientes/account', {
          data: deleteForm
        });
        
        showNotification('Cuenta eliminada exitosamente', 'success');
        
        setTimeout(() => {
          router.push('/');
        }, 2000);
        
      } catch (error) {
        deleteErrors.value = error.response?.data?.errors || {};
        showNotification('Error eliminando cuenta', 'error');
      } finally {
        deleteLoading.value = false;
      }
    };
    
    const showNotification = (message, type = 'success') => {
      notification.show = true;
      notification.type = type;
      notification.message = message;
      
      setTimeout(() => {
        hideNotification();
      }, 5000);
    };
    
    const hideNotification = () => {
      notification.show = false;
    };
    
    const formatNumber = (number) => {
      return new Intl.NumberFormat('es-CO').format(number);
    };
    
    /**
     * Formatea una fecha para mostrarla de manera amigable
     * @param {string} dateString - Fecha en formato ISO
     * @returns {string} Fecha formateada
     */
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      
      try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('es-CO', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        }).format(date);
      } catch (error) {
        console.error('Error formateando fecha:', error);
        return 'Fecha inválida';
      }
    };
    
    /**
     * Obtiene el texto legible para el estado del pedido
     * @param {string} status - Estado del pedido
     * @returns {string} Texto del estado
     */
    const getStatusText = (status) => {
      const statusMap = {
        'pending': 'Pendiente',
        'paid': 'Pagado',
        'failed': 'Fallido',
        'cancelled': 'Cancelado'
      };
      
      return statusMap[status] || 'Estado desconocido';
    };
    
    /**
     * Obtiene la clase CSS para el badge de estado
     * @param {string} status - Estado del pedido
     * @returns {string} Clase CSS
     */
    const getStatusClass = (status) => {
      const classMap = {
        'pending': 'pending',
        'paid': 'delivered',
        'failed': 'failed',
        'cancelled': 'cancelled'
      };
      
      return classMap[status] || 'unknown';
    };
    
    /**
     * Determina si un pedido puede ser cancelado
     * @param {Object} order - Objeto del pedido
     * @returns {boolean} True si puede ser cancelado
     */
    const canCancelOrder = (order) => {
      // Solo pedidos pendientes pueden ser cancelados
      const cancelableStatuses = ['pending'];
      return cancelableStatuses.includes(order.status);
    };
    
    /**
     * Ver detalles completos de un pedido
     * Abre el modal de detalles con la información completa del pedido
     * @param {Object} order - Objeto del pedido
     */
    const viewOrderDetails = async (order) => {
      try {
        console.log('🔍 Cargando detalles del pedido:', order.id);
        
        // Si el pedido ya tiene todos los detalles, usar esos datos
        if (order.items && order.items.length > 0) {
          selectedOrder.value = order;
          showOrderDetailsModal.value = true;
          return;
        }
        
        // Si no tiene detalles completos, cargar desde el servidor
        const response = await axios.get(`/orders/${order.id}`);
        selectedOrder.value = response.data.order || response.data;
        showOrderDetailsModal.value = true;
        
        console.log('✅ Detalles del pedido cargados:', selectedOrder.value);
        
      } catch (error) {
        console.error('❌ Error cargando detalles del pedido:', error);
        showNotification('Error cargando los detalles del pedido', 'error');
      }
    };
    
    /**
     * Cancelar pedido desde el modal de detalles
     * @param {number} orderId - ID del pedido a cancelar
     */
    const cancelOrderFromModal = async (orderId) => {
      await cancelOrder(orderId);
      showOrderDetailsModal.value = false;
      selectedOrder.value = null;
    };
    
    
    /**
     * Cancelar un pedido específico
     * @param {number} orderId - ID del pedido a cancelar
     */
    const cancelOrder = async (orderId) => {
      try {
        // Confirmar cancelación con el usuario
        if (!confirm('¿Estás seguro de que deseas cancelar este pedido?')) {
          return;
        }
        
        // Realizar petición para cancelar el pedido usando el endpoint correcto
        await axios.patch(`/orders/${orderId}/cancel`);
        
        // Recargar los pedidos para reflejar el cambio
        await loadOrders();
        
        showNotification('Pedido cancelado exitosamente', 'success');
        
      } catch (error) {
        console.error('Error cancelando pedido:', error);
        showNotification('Error al cancelar el pedido', 'error');
      }
    };
    
    const editAddress = (address) => {
      // Implementar edición de dirección
      console.log('Editar dirección:', address);
    };
    
    const deleteAddress = (addressId) => {
      // Implementar eliminación de dirección
      console.log('Eliminar dirección:', addressId);
    };
    
    /**
     * Inicializar la tab activa basada en query parameters
     * Permite navegar directamente a una sección específica
     */
    const initializeActiveTab = () => {
      // Obtener el query parameter 'tab' de la URL
      const urlParams = new URLSearchParams(window.location.search);
      const tabFromUrl = urlParams.get('tab');
      
      // Si existe un tab en la URL y es válido, usarlo
      const validTabs = ['overview', 'profile', 'security', 'addresses', 'orders'];
      if (tabFromUrl && validTabs.includes(tabFromUrl)) {
        activeTab.value = tabFromUrl;
        console.log(`🎯 Tab inicializada desde URL: ${tabFromUrl}`);
      }
    };

    // Lifecycle
    onMounted(async () => {
      loading.value = true;
      
      try {
        // Inicializar la tab activa basada en query parameters ANTES de cargar datos
        initializeActiveTab();
        
        // Cargar datos de cuenta y direcciones de manera paralela
        await Promise.all([
          loadAccountData(),
          loadAddresses()
        ]);
        
        // Inicializar formulario de perfil con datos del usuario actual
        initializeProfileForm();
        
        // Cargar pedidos de manera independiente (pueden tomar más tiempo)
        // Se ejecuta después de que la página principal haya cargado
        loadOrders();
        
      } catch (error) {
        console.error('Error inicializando página:', error);
        showNotification('Error cargando algunos datos de la cuenta', 'error');
      } finally {
        loading.value = false;
      }
    });
    
    return {
      // Estados
      activeTab,
      loading,
      profileLoading,
      passwordLoading,
      deleteLoading,
      ordersLoading, // Estado de carga para pedidos
      showDeleteModal,
      showAddressModal,
      showOrderDetailsModal, // Estado del modal de detalles del pedido
      
      // Datos
      currentUser,
      accountData,
      addresses,
      orders, // Array de pedidos del cliente
      selectedOrder, // Pedido seleccionado para detalles
      validOrders, // Computed para pedidos válidos filtrados
      tabs,
      fondoImage,
      
      // Formularios
      profileForm,
      passwordForm,
      deleteForm,
      
      // Errores
      profileErrors,
      passwordErrors,
      deleteErrors,
      
      // Notificaciones
      notification,
      
      // Métodos de perfil y cuenta
      updateProfile,
      changePassword,
      deleteAccount,
      showNotification,
      hideNotification,
      formatNumber,
      editAddress,
      deleteAddress,
      
      // Métodos específicos para pedidos
      loadOrders, // Función para recargar pedidos
      formatDate, // Formateador de fechas
      getStatusText, // Obtener texto del estado
      getStatusClass, // Obtener clase CSS del estado
      canCancelOrder, // Verificar si se puede cancelar
      viewOrderDetails, // Ver detalles del pedido
      cancelOrder, // Cancelar pedido
      cancelOrderFromModal, // Cancelar pedido desde modal
      
      // Utilidades de imagen importadas
      getProductImageUrl, // Función de utilidad para URLs de imagen
      handleImageError // Función de utilidad para errores de imagen
    };
  }
}
</script>


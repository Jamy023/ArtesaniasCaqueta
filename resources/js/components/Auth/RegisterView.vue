<!-- views/auth/RegisterView.vue -->
<template>
  <div class="register-page">
    <div class="register-container">
      <!-- Header con navegación -->
      <div class="register-header">
        <router-link to="/" class="back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m12 19-7-7 7-7"/>
            <path d="M19 12H5"/>
          </svg>
          Volver al inicio
        </router-link>
      </div>

    

        <!-- Sección del Formulario -->
        <div class="form-section">
          <div class="form-container">
            <div class="form-header">
              <h2>Información de registro</h2>
              <p>Completa los siguientes datos para crear tu cuenta</p>
            </div>

            <form @submit.prevent="register" class="register-form">
              <!-- Información Personal -->
              <div class="form-group-container">
                <h3 class="group-title">Datos personales</h3>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input 
                      id="nombre"
                      v-model="form.nombre" 
                      type="text"
                      placeholder="Tu nombre"
                      required
                      :class="{ 'error': errors.nombre }"
                    >
                    <span v-if="errors.nombre" class="error-message">{{ errors.nombre[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input 
                      id="apellido"
                      v-model="form.apellido" 
                      type="text"
                      placeholder="Tu apellido"
                      required
                      :class="{ 'error': errors.apellido }"
                    >
                    <span v-if="errors.apellido" class="error-message">{{ errors.apellido[0] }}</span>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="tipo_documento">Tipo de Documento *</label>
                    <select 
                      id="tipo_documento"
                      v-model="form.tipo_documento" 
                      required
                      :class="{ 'error': errors.tipo_documento }"
                    >
                      <option value="">Selecciona...</option>
                      <option value="CC">Cédula de Ciudadanía</option>
                      <option value="TI">Tarjeta de Identidad</option>
                      <option value="CE">Cédula de Extranjería</option>
                      <option value="PP">Pasaporte</option>
                      <option value="NIT">NIT</option>
                    </select>
                    <span v-if="errors.tipo_documento" class="error-message">{{ errors.tipo_documento[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label for="numero_documento">Número de Documento *</label>
                    <input 
                      id="numero_documento"
                      v-model="form.numero_documento" 
                      type="text"
                      placeholder="123456789"
                      required
                      :class="{ 'error': errors.numero_documento }"
                    >
                    <span v-if="errors.numero_documento" class="error-message">{{ errors.numero_documento[0] }}</span>
                  </div>
                </div>
              </div>

              <!-- Contacto -->
              <div class="form-group-container">
                <h3 class="group-title">Información de contacto</h3>

                <div class="form-group">
                  <label for="email">Correo electrónico *</label>
                  <input 
                    id="email"
                    v-model="form.email" 
                    type="email"
                    placeholder="tu@email.com"
                    required
                    :class="{ 'error': errors.email }"
                  >
                  <span v-if="errors.email" class="error-message">{{ errors.email[0] }}</span>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input 
                      id="telefono"
                      v-model="form.telefono" 
                      type="tel"
                      placeholder="300 123 4567"
                      :class="{ 'error': errors.telefono }"
                    >
                    <span v-if="errors.telefono" class="error-message">{{ errors.telefono[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input 
                      id="fecha_nacimiento"
                      v-model="form.fecha_nacimiento" 
                      type="date"
                      :class="{ 'error': errors.fecha_nacimiento }"
                    >
                    <span v-if="errors.fecha_nacimiento" class="error-message">{{ errors.fecha_nacimiento[0] }}</span>
                  </div>
                </div>
              </div>

              <!-- Ubicación -->
              <div class="form-group-container">
                <h3 class="group-title">Ubicación</h3>

                <div class="form-group">
                  <label for="direccion">Dirección</label>
                  <input 
                    id="direccion"
                    v-model="form.direccion" 
                    type="text"
                    placeholder="Calle 123 # 45-67"
                    :class="{ 'error': errors.direccion }"
                  >
                  <span v-if="errors.direccion" class="error-message">{{ errors.direccion[0] }}</span>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="departamento">Departamento *</label>
                    <select 
                      id="departamento"
                      v-model="form.departamento" 
                      @change="onDepartmentChange"
                      required
                      :class="{ 'error': errors.departamento }"
                    >
                      <option value="">Selecciona tu departamento</option>
                      <option v-for="departamento in departamentos" :key="departamento.id" :value="departamento.nombre">
                        {{ departamento.nombre }}
                      </option>
                    </select>
                    <span v-if="errors.departamento" class="error-message">{{ errors.departamento[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label for="ciudad">Ciudad *</label>
                    <select 
                      id="ciudad"
                      v-model="form.ciudad" 
                      :disabled="!form.departamento || ciudades.length === 0"
                      required
                      :class="{ 'error': errors.ciudad }"
                    >
                      <option value="">{{ !form.departamento ? 'Primero selecciona un departamento' : 'Selecciona tu ciudad' }}</option>
                      <option v-for="ciudad in ciudades" :key="ciudad.id" :value="ciudad.nombre">
                        {{ ciudad.nombre }}
                      </option>
                    </select>
                    <span v-if="errors.ciudad" class="error-message">{{ errors.ciudad[0] }}</span>
                  </div>
                </div>
              </div>

              <!-- Seguridad -->
              <div class="form-group-container">
                <h3 class="group-title">Contraseña</h3>

                <div class="form-row">
                  <div class="form-group">
                    <label for="password">Contraseña *</label>
                    <div class="password-input-wrapper">
                      <input 
                        id="password"
                        v-model="form.password" 
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Mínimo 8 caracteres"
                        required
                        :class="{ 'error': errors.password }"
                      >
                      <button 
                        type="button" 
                        class="password-toggle"
                        @click="showPassword = !showPassword"
                      >
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                          <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                      </button>
                    </div>
                    <span v-if="errors.password" class="error-message">{{ errors.password[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña *</label>
                    <div class="password-input-wrapper">
                      <input 
                        id="password_confirmation"
                        v-model="form.password_confirmation" 
                        :type="showPasswordConfirm ? 'text' : 'password'"
                        placeholder="Repite tu contraseña"
                        required
                        :class="{ 'error': errors.password_confirmation }"
                      >
                      <button 
                        type="button" 
                        class="password-toggle"
                        @click="showPasswordConfirm = !showPasswordConfirm"
                      >
                        <svg v-if="showPasswordConfirm" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                          <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                      </button>
                    </div>
                    <span v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation[0] }}</span>
                  </div>
                </div>
              </div>

              <!-- Botones de acción -->
              <div class="form-actions">
                <button type="submit" class="register-btn" :disabled="loading">
                  <span v-if="loading" class="loading-spinner"></span>
                  <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="m22 21-3-3m0 0-3-3m3 3 3-3m-3 3-3 3"/>
                  </svg>
                  {{ loading ? 'Creando cuenta...' : 'Crear mi cuenta' }}
                </button>
              </div>

              <!-- Footer del formulario -->
              <div class="form-footer">
                <div class="divider">
                  <span>o</span>
                </div>
                <p>¿Ya tienes cuenta? 
                  <a href="#" @click="openLoginModal" class="login-link">
                    Inicia sesión aquí
                  </a>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  
</template>

<script>
import { ref, watch } from 'vue'
import { useAuthStore } from '../../stores/authStore'
import { useRouter } from 'vue-router'

export default {
  name: 'RegisterView',
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()
    
    const form = ref({
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
      password_confirmation: ''
    })
    
    // Datos de departamentos y ciudades de Colombia
    const departamentos = ref([
      { id: 1, nombre: 'Antioquia' },
      { id: 2, nombre: 'Atlántico' },
      { id: 3, nombre: 'Bogotá D.C.' },
      { id: 4, nombre: 'Bolívar' },
      { id: 5, nombre: 'Boyacá' },
      { id: 6, nombre: 'Caldas' },
      { id: 7, nombre: 'Caquetá' },
      { id: 8, nombre: 'Cauca' },
      { id: 9, nombre: 'César' },
      { id: 10, nombre: 'Córdoba' },
      { id: 11, nombre: 'Cundinamarca' },
      { id: 12, nombre: 'Chocó' },
      { id: 13, nombre: 'Huila' },
      { id: 14, nombre: 'La Guajira' },
      { id: 15, nombre: 'Magdalena' },
      { id: 16, nombre: 'Meta' },
      { id: 17, nombre: 'Nariño' },
      { id: 18, nombre: 'Norte de Santander' },
      { id: 19, nombre: 'Quindío' },
      { id: 20, nombre: 'Risaralda' },
      { id: 21, nombre: 'Santander' },
      { id: 22, nombre: 'Sucre' },
      { id: 23, nombre: 'Tolima' },
      { id: 24, nombre: 'Valle del Cauca' },
      { id: 25, nombre: 'Arauca' },
      { id: 26, nombre: 'Casanare' },
      { id: 27, nombre: 'Putumayo' },
      { id: 28, nombre: 'San Andrés y Providencia' },
      { id: 29, nombre: 'Amazonas' },
      { id: 30, nombre: 'Guainía' },
      { id: 31, nombre: 'Guaviare' },
      { id: 32, nombre: 'Vaupés' },
      { id: 33, nombre: 'Vichada' }
    ])

    const ciudadesPorDepartamento = {
      1: [ // Antioquia
        { id: 1, nombre: 'Medellín' },
        { id: 2, nombre: 'Bello' },
        { id: 3, nombre: 'Itagüí' },
        { id: 4, nombre: 'Envigado' },
        { id: 5, nombre: 'Apartadó' },
        { id: 6, nombre: 'Turbo' },
        { id: 7, nombre: 'Rionegro' },
        { id: 8, nombre: 'Sabaneta' },
        { id: 9, nombre: 'Caucasia' },
        { id: 10, nombre: 'Copacabana' }
      ],
      2: [ // Atlántico
        { id: 11, nombre: 'Barranquilla' },
        { id: 12, nombre: 'Soledad' },
        { id: 13, nombre: 'Malambo' },
        { id: 14, nombre: 'Sabanagrande' },
        { id: 15, nombre: 'Puerto Colombia' },
        { id: 16, nombre: 'Galapa' },
        { id: 17, nombre: 'Baranoa' }
      ],
      3: [ // Bogotá D.C.
        { id: 18, nombre: 'Bogotá' },
        { id: 19, nombre: 'Suba' },
        { id: 20, nombre: 'Kennedy' },
        { id: 21, nombre: 'Engativá' },
        { id: 22, nombre: 'Ciudad Bolívar' },
        { id: 23, nombre: 'Bosa' },
        { id: 24, nombre: 'San Cristóbal' },
        { id: 25, nombre: 'Usme' }
      ],
      4: [ // Bolívar
        { id: 26, nombre: 'Cartagena' },
        { id: 27, nombre: 'Magangué' },
        { id: 28, nombre: 'Turbaco' },
        { id: 29, nombre: 'El Carmen de Bolívar' },
        { id: 30, nombre: 'Arjona' }
      ],
      5: [ // Boyacá
        { id: 31, nombre: 'Tunja' },
        { id: 32, nombre: 'Duitama' },
        { id: 33, nombre: 'Sogamoso' },
        { id: 34, nombre: 'Chiquinquirá' },
        { id: 35, nombre: 'Paipa' }
      ],
      6: [ // Caldas
        { id: 36, nombre: 'Manizales' },
        { id: 37, nombre: 'La Dorada' },
        { id: 38, nombre: 'Chinchiná' },
        { id: 39, nombre: 'Riosucio' },
        { id: 40, nombre: 'Anserma' }
      ],
      7: [ // Caquetá
        { id: 41, nombre: 'Florencia' },
        { id: 42, nombre: 'San Vicente del Caguán' },
        { id: 43, nombre: 'Puerto Rico' },
        { id: 44, nombre: 'El Paujil' }
      ],
      8: [ // Cauca
        { id: 45, nombre: 'Popayán' },
        { id: 46, nombre: 'Santander de Quilichao' },
        { id: 47, nombre: 'Puerto Tejada' },
        { id: 48, nombre: 'Guapi' }
      ],
      9: [ // César
        { id: 49, nombre: 'Valledupar' },
        { id: 50, nombre: 'Aguachica' },
        { id: 51, nombre: 'Codazzi' },
        { id: 52, nombre: 'La Paz' }
      ],
      10: [ // Córdoba
        { id: 53, nombre: 'Montería' },
        { id: 54, nombre: 'Cereté' },
        { id: 55, nombre: 'Sahagún' },
        { id: 56, nombre: 'Lorica' }
      ],
      11: [ // Cundinamarca
        { id: 57, nombre: 'Soacha' },
        { id: 58, nombre: 'Girardot' },
        { id: 59, nombre: 'Zipaquirá' },
        { id: 60, nombre: 'Facatativá' },
        { id: 61, nombre: 'Chía' },
        { id: 62, nombre: 'Mosquera' },
        { id: 63, nombre: 'Fusagasugá' }
      ],
      12: [ // Chocó
        { id: 64, nombre: 'Quibdó' },
        { id: 65, nombre: 'Istmina' },
        { id: 66, nombre: 'Condoto' },
        { id: 67, nombre: 'Tadó' }
      ],
      13: [ // Huila
        { id: 68, nombre: 'Neiva' },
        { id: 69, nombre: 'Pitalito' },
        { id: 70, nombre: 'Garzón' },
        { id: 71, nombre: 'La Plata' }
      ],
      14: [ // La Guajira
        { id: 72, nombre: 'Riohacha' },
        { id: 73, nombre: 'Maicao' },
        { id: 74, nombre: 'Uribia' },
        { id: 75, nombre: 'Fonseca' }
      ],
      15: [ // Magdalena
        { id: 76, nombre: 'Santa Marta' },
        { id: 77, nombre: 'Ciénaga' },
        { id: 78, nombre: 'Fundación' },
        { id: 79, nombre: 'El Banco' }
      ],
      16: [ // Meta
        { id: 80, nombre: 'Villavicencio' },
        { id: 81, nombre: 'Acacías' },
        { id: 82, nombre: 'Granada' },
        { id: 83, nombre: 'Puerto López' }
      ],
      17: [ // Nariño
        { id: 84, nombre: 'Pasto' },
        { id: 85, nombre: 'Tumaco' },
        { id: 86, nombre: 'Ipiales' },
        { id: 87, nombre: 'Samaniego' }
      ],
      18: [ // Norte de Santander
        { id: 88, nombre: 'Cúcuta' },
        { id: 89, nombre: 'Ocaña' },
        { id: 90, nombre: 'Villa del Rosario' },
        { id: 91, nombre: 'Los Patios' }
      ],
      19: [ // Quindío
        { id: 92, nombre: 'Armenia' },
        { id: 93, nombre: 'Calarcá' },
        { id: 94, nombre: 'La Tebaida' },
        { id: 95, nombre: 'Montenegro' }
      ],
      20: [ // Risaralda
        { id: 96, nombre: 'Pereira' },
        { id: 97, nombre: 'Dosquebradas' },
        { id: 98, nombre: 'Santa Rosa de Cabal' },
        { id: 99, nombre: 'La Virginia' }
      ],
      21: [ // Santander
        { id: 100, nombre: 'Bucaramanga' },
        { id: 101, nombre: 'Floridablanca' },
        { id: 102, nombre: 'Girón' },
        { id: 103, nombre: 'Piedecuesta' },
        { id: 104, nombre: 'Barrancabermeja' }
      ],
      22: [ // Sucre
        { id: 105, nombre: 'Sincelejo' },
        { id: 106, nombre: 'Corozal' },
        { id: 107, nombre: 'Sampués' },
        { id: 108, nombre: 'San Marcos' }
      ],
      23: [ // Tolima
        { id: 109, nombre: 'Ibagué' },
        { id: 110, nombre: 'Espinal' },
        { id: 111, nombre: 'Melgar' },
        { id: 112, nombre: 'Honda' }
      ],
      24: [ // Valle del Cauca
        { id: 113, nombre: 'Cali' },
        { id: 114, nombre: 'Palmira' },
        { id: 115, nombre: 'Buenaventura' },
        { id: 116, nombre: 'Tuluá' },
        { id: 117, nombre: 'Cartago' },
        { id: 118, nombre: 'Buga' }
      ],
      25: [ // Arauca
        { id: 119, nombre: 'Arauca' },
        { id: 120, nombre: 'Saravena' },
        { id: 121, nombre: 'Fortul' }
      ],
      26: [ // Casanare
        { id: 122, nombre: 'Yopal' },
        { id: 123, nombre: 'Aguazul' },
        { id: 124, nombre: 'Villanueva' }
      ],
      27: [ // Putumayo
        { id: 125, nombre: 'Mocoa' },
        { id: 126, nombre: 'Puerto Asís' },
        { id: 127, nombre: 'Orito' }
      ],
      28: [ // San Andrés y Providencia
        { id: 128, nombre: 'San Andrés' },
        { id: 129, nombre: 'Providencia' }
      ],
      29: [ // Amazonas
        { id: 130, nombre: 'Leticia' },
        { id: 131, nombre: 'Puerto Nariño' }
      ],
      30: [ // Guainía
        { id: 132, nombre: 'Inírida' }
      ],
      31: [ // Guaviare
        { id: 133, nombre: 'San José del Guaviare' }
      ],
      32: [ // Vaupés
        { id: 134, nombre: 'Mitú' }
      ],
      33: [ // Vichada
        { id: 135, nombre: 'Puerto Carreño' }
      ]
    }

    const ciudades = ref([])
    
    const showPassword = ref(false)
    const showPasswordConfirm = ref(false)
    const loading = ref(false)
    const errors = ref({})

    // Funciones de validación
    const validateField = (field, value) => {
      switch (field) {
        case 'email':
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
          return emailRegex.test(value) ? null : 'Email inválido'
        
        case 'password':
          if (value.length < 8) return 'La contraseña debe tener al menos 8 caracteres'
          if (!/(?=.*[a-z])/.test(value)) return 'Debe contener al menos una minúscula'
          if (!/(?=.*[A-Z])/.test(value)) return 'Debe contener al menos una mayúscula'
          if (!/(?=.*\d)/.test(value)) return 'Debe contener al menos un número'
          return null
        
        case 'password_confirmation':
          return value === form.value.password ? null : 'Las contraseñas no coinciden'
        
        case 'numero_documento':
          if (!value) return 'Número de documento requerido'
          const docRegex = /^[0-9]{7,12}$/
          return docRegex.test(value.replace(/[.\-\s]/g, '')) ? null : 'Número de documento inválido (7-12 dígitos)'
        
        case 'telefono':
          if (!value) return null // Campo opcional
          const phoneRegex = /^[0-9]{10}$/
          return phoneRegex.test(value.replace(/\s/g, '')) ? null : 'Teléfono debe tener 10 dígitos'
        
        case 'nombre':
          if (!value) return 'Nombre requerido'
          if (value.length < 2) return 'Nombre debe tener al menos 2 caracteres'
          return null
        
        case 'apellido':
          if (!value) return 'Apellido requerido'
          if (value.length < 2) return 'Apellido debe tener al menos 2 caracteres'
          return null
        
        case 'tipo_documento':
          return !value ? 'Tipo de documento requerido' : null
        
        default:
          return null
      }
    }

    // Formatear número de teléfono
    const formatPhoneNumber = (value) => {
      const cleaned = value.replace(/\D/g, '')
      if (cleaned.length >= 10) {
        const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})/)
        if (match) {
          return `${match[1]} ${match[2]} ${match[3]}`
        }
      }
      return cleaned
    }

    // Formatear número de documento
    const formatDocumentNumber = (value, type) => {
      const cleaned = value.replace(/\D/g, '')
      
      switch (type) {
        case 'CC':
        case 'TI':
          if (cleaned.length >= 8) {
            return cleaned.replace(/(\d{1,3})(\d{3})(\d{3})/, '$1.$2.$3')
          }
          return cleaned
        case 'NIT':
          if (cleaned.length >= 9) {
            return cleaned.replace(/(\d{3})(\d{3})(\d{3})(\d{1})/, '$1.$2.$3-$4')
          }
          return cleaned
        default:
          return cleaned
      }
    }

    // Limpiar error de campo específico
    const clearFieldError = (field) => {
      if (errors.value[field]) {
        const newErrors = { ...errors.value }
        delete newErrors[field]
        errors.value = newErrors
      }
    }

    // Validar formulario completo
    const validateForm = () => {
      const validationErrors = {}
      
      // Campos requeridos
      const requiredFields = ['nombre', 'apellido', 'tipo_documento', 'numero_documento', 'email', 'password', 'password_confirmation']
      
      requiredFields.forEach(field => {
        const error = validateField(field, form.value[field])
        if (error) validationErrors[field] = [error]
      })

      // Campos opcionales con validación
      if (form.value.telefono) {
        const phoneError = validateField('telefono', form.value.telefono)
        if (phoneError) validationErrors.telefono = [phoneError]
      }

      return validationErrors
    }

    // Función de registro mejorada
    const register = async () => {
      loading.value = true
      errors.value = {}
      
      // Validación del lado del cliente
      const clientValidation = validateForm()
      
      if (Object.keys(clientValidation).length > 0) {
        errors.value = clientValidation
        loading.value = false
        // Hacer scroll al primer error
        scrollToFirstError()
        return
      }
      
      try {
        await authStore.register(form.value)
        
        // Mostrar mensaje de éxito (opcional)
        showSuccessMessage('¡Cuenta creada exitosamente!')
        
        // Redireccionar
        router.push('/')
        
      } catch (error) {
        console.error('Error de registro:', error)
        
        if (error.response?.status === 422) {
          // Errores de validación del servidor
          errors.value = error.response.data.errors
        } else if (error.response?.status === 500) {
          // Error interno del servidor
          errors.value = { general: ['Error interno del servidor. Intenta más tarde.'] }
        } else if (error.response?.status === 409) {
          // Conflicto (usuario ya existe)
          errors.value = { general: ['El usuario ya existe. Intenta con otros datos.'] }
        } else {
          // Error de conexión u otros
          errors.value = { general: ['Error de conexión. Verifica tu internet.'] }
        }
        
        // Hacer scroll al primer error
        scrollToFirstError()
      } finally {
        loading.value = false
      }
    }

    // Función para hacer scroll al primer error
    const scrollToFirstError = () => {
      setTimeout(() => {
        const firstErrorElement = document.querySelector('.error-message')
        if (firstErrorElement) {
          firstErrorElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
          })
        }
      }, 100)
    }

    // Función para mostrar mensajes de éxito
    const showSuccessMessage = (message) => {
      // Puedes implementar un toast notification aquí
      console.log(message)
    }

    // Función original para abrir modal de login
    const openLoginModal = () => {
      router.push('/?login=true')
    }

    // Manejar cambio en tipo de documento
    const handleDocumentTypeChange = () => {
      form.value.numero_documento = ''
      clearFieldError('numero_documento')
    }

    // Función para manejar cambio de departamento
    const onDepartmentChange = () => {
      const departamentoSeleccionado = departamentos.value.find(dep => dep.nombre === form.value.departamento)
      form.value.ciudad = '' // Limpiar ciudad seleccionada
      clearFieldError('ciudad')
      
      if (departamentoSeleccionado && ciudadesPorDepartamento[departamentoSeleccionado.id]) {
        ciudades.value = ciudadesPorDepartamento[departamentoSeleccionado.id]
      } else {
        ciudades.value = []
      }
    }

    // Watchers para validación en tiempo real y formateo
    watch(() => form.value.email, (newVal) => {
      if (newVal) {
        clearFieldError('email')
        // Validar email en tiempo real después de 500ms
        setTimeout(() => {
          const error = validateField('email', newVal)
          if (error) {
            errors.value = { ...errors.value, email: [error] }
          }
        }, 500)
      }
    })

    watch(() => form.value.password, (newVal) => {
      if (newVal) {
        clearFieldError('password')
        // Validar contraseña en tiempo real
        setTimeout(() => {
          const error = validateField('password', newVal)
          if (error) {
            errors.value = { ...errors.value, password: [error] }
          }
        }, 500)
      }
    })

    watch(() => form.value.password_confirmation, (newVal) => {
      if (newVal) {
        clearFieldError('password_confirmation')
        // Validar confirmación de contraseña en tiempo real
        setTimeout(() => {
          const error = validateField('password_confirmation', newVal)
          if (error) {
            errors.value = { ...errors.value, password_confirmation: [error] }
          }
        }, 500)
      }
    })

    watch(() => form.value.telefono, (newVal) => {
      if (newVal) {
        clearFieldError('telefono')
        // Formatear teléfono automáticamente
        const formatted = formatPhoneNumber(newVal)
        if (formatted !== newVal) {
          form.value.telefono = formatted
        }
      }
    })

    watch(() => form.value.numero_documento, (newVal) => {
      if (newVal && form.value.tipo_documento) {
        clearFieldError('numero_documento')
        // Formatear número de documento automáticamente
        const formatted = formatDocumentNumber(newVal, form.value.tipo_documento)
        if (formatted !== newVal) {
          form.value.numero_documento = formatted
        }
      }
    })

    watch(() => form.value.tipo_documento, () => {
      handleDocumentTypeChange()
    })

    // Limpiar errores al escribir en campos de texto
    watch(() => form.value.nombre, (newVal) => {
      if (newVal) clearFieldError('nombre')
    })

    watch(() => form.value.apellido, (newVal) => {
      if (newVal) clearFieldError('apellido')
    })

    watch(() => form.value.ciudad, (newVal) => {
      if (newVal) clearFieldError('ciudad')
    })

    watch(() => form.value.departamento, (newVal) => {
      if (newVal) clearFieldError('departamento')
    })

    watch(() => form.value.direccion, (newVal) => {
      if (newVal) clearFieldError('direccion')
    })

    // Función para limpiar todos los errores
    const clearAllErrors = () => {
      errors.value = {}
    }

    // Función para resetear formulario
    const resetForm = () => {
      form.value = {
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
        password_confirmation: ''
      }
      errors.value = {}
    }

    return {
      // Estado original
      form,
      showPassword,
      showPasswordConfirm,
      loading,
      errors,
      
      // Datos geográficos
      departamentos,
      ciudades,
      
      // Funciones originales
      register,
      openLoginModal,
      
      // Nuevas funciones
      validateField,
      clearFieldError,
      clearAllErrors,
      resetForm,
      handleDocumentTypeChange,
      onDepartmentChange,
      
      // Funciones de utilidad
      formatPhoneNumber,
      formatDocumentNumber
    }
  }
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.register-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #125705 0%, #0b6d08 100%);
  position: relative;
  overflow-x: hidden;
  padding: 20px 0;
}

.register-page::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.1"><path d="M0,0 Q250,50 500,0 T1000,0 L1000,300 Q750,250 500,300 T0,300 Z" fill="%23ffffff"/><path d="M0,700 Q250,750 500,700 T1000,700 L1000,1000 L0,1000 Z" fill="%23ffffff"/></svg>'),
    radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
  background-size: 100% 100%, cover, cover;
  pointer-events: none;
}

.register-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 1;
}

.register-header {
  margin-bottom: 32px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #e2e8f0;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 8px;
  transition: all 0.2s ease;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.back-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #f1f5f9;
  transform: translateX(-4px);
}

.register-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: start;
}

/* Sección de información */
.info-section {
  padding: 40px 0;
  color: white;
}

.brand-section {
  margin-bottom: 48px;
}

.brand-logo {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 24px;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
}

.brand-section h1 {
  font-size: 40px;
  font-weight: 700;
  margin: 0 0 16px 0;
  background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1.2;
}

.brand-section p {
  font-size: 18px;
  color: #cbd5e1;
  line-height: 1.6;
  margin: 0;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.feature-item:hover {
  background: rgba(255, 255, 255, 0.08);
  transform: translateY(-2px);
}

.feature-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.feature-item h3 {
  font-size: 16px;
  font-weight: 600;
  color: #f1f5f9;
  margin: 0 0 4px 0;
}

.feature-item p {
  font-size: 14px;
  color: #cbd5e1;
  margin: 0;
  line-height: 1.5;
}

/* Sección del formulario */
.form-section {
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: calc(100vh - 100px);
}

.form-container {
  background: rgba(255, 255, 255, 0.98);
  border-radius: 24px;
  padding: 48px;
  box-shadow: 0 32px 64px rgba(0, 0, 0, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(20px);
  width: 100%;
  max-width: 800px;
  margin: 20px 0;
  transform: translateY(0);
  transition: transform 0.3s ease;
}

.form-container:hover {
  transform: translateY(-4px);
}

.form-header {
  text-align: center;
  margin-bottom: 32px;
}

.form-header h2 {
  font-size: 32px;
  font-weight: 800;
  background: linear-gradient(135deg, #076e32 0%, #176308 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 12px 0;
  text-align: center;
}

.form-header p {
  color: #64748b;
  font-size: 18px;
  margin: 0;
  font-weight: 400;
  line-height: 1.6;
}

.register-form {
  width: 100%;
}

.form-group-container {
  margin-bottom: 32px;
  padding-bottom: 24px;
  border-bottom: 1px solid #f3f4f6;
}

.form-group-container:last-of-type {
  border-bottom: none;
  margin-bottom: 24px;
}

.group-title {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
  padding-left: 20px;
}

.group-title::before {
  content: '';
  position: absolute;
  left: 0;
  width: 5px;
  height: 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 3px;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #374151;
  font-weight: 500;
  font-size: 14px;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 16px 20px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 15px;
  transition: all 0.3s ease;
  background: #ffffff;
  font-family: inherit;
  position: relative;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
  transform: translateY(-2px);
}

.form-group input.error,
.form-group select.error {
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
  background-color: #fef2f2;
}

.form-group select:disabled {
  background-color: #f9fafb;
  color: #9ca3af;
  cursor: not-allowed;
  opacity: 0.6;
}

.password-input-wrapper {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.password-toggle:hover {
  color: #374151;
  background: #f9fafb;
}

.error-message {
  color: #ef4444;
  font-size: 12px;
  margin-top: 6px;
  display: block;
  font-weight: 500;
}

.form-actions {
  margin: 32px 0 24px 0;
}

.register-btn {
  width: 100%;
  background: linear-gradient(135deg, #057226 0%, #088d13 100%);
  color: white;
  border: none;
  padding: 18px 32px;
  border-radius: 16px;
  font-size: 17px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  min-height: 64px;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.register-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #013516 0%, 013516 100%);
  box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
  transform: translateY(-4px);
}

.register-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.form-footer {
  text-align: center;
  margin-top: 24px;
}

.divider {
  position: relative;
  margin: 24px 0;
  text-align: center;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e5e7eb;
}

.divider span {
  background: white;
  padding: 0 16px;
  color: #6b7280;
  font-size: 14px;
  position: relative;
  z-index: 1;
}

.form-footer p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.login-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  position: relative;
}

.login-link:hover {
  color: #1b8d04;
}

.login-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: -2px;
  left: 0;
  background: linear-gradient(135deg, #1b8d04 0%, #1b8d04 100%);
  transition: width 0.3s ease;
}

.login-link:hover::after {
  width: 100%;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .form-container {
    max-width: 700px;
    padding: 40px;
  }
}

@media (max-width: 768px) {
  .register-container {
    padding: 0 15px;
  }
  
  .form-container {
    max-width: 100%;
    padding: 32px 24px;
    border-radius: 20px;
    margin: 10px 0;
  }
  
  .form-header h2 {
    font-size: 28px;
  }
  
  .form-header p {
    font-size: 16px;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 0;
  }
  
  .group-title {
    font-size: 18px;
  }
}

@media (max-width: 640px) {
  .register-page {
    padding: 10px 0;
  }
  
  .register-container {
    padding: 0 10px;
  }
  
  .form-container {
    padding: 24px 20px;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  }
  
  .form-header h2 {
    font-size: 24px;
  }
  
  .form-header p {
    font-size: 15px;
  }
  
  .back-link {
    padding: 8px 12px;
    font-size: 13px;
    margin-bottom: 20px;
  }
  
  .form-group input,
  .form-group select {
    padding: 14px 16px;
    font-size: 14px;
  }
  
  .register-btn {
    padding: 16px 24px;
    font-size: 16px;
    min-height: 56px;
  }
  
  .group-title {
    font-size: 17px;
    margin-bottom: 20px;
  }
}

@media (max-width: 480px) {
  .form-container {
    padding: 20px 16px;
  }
  
  .form-header h2 {
    font-size: 22px;
  }
  
  .form-header p {
    font-size: 14px;
  }
  
  .register-btn {
    font-size: 15px;
    padding: 14px 20px;
    min-height: 52px;
  }
  
  .form-group input,
  .form-group select {
    padding: 12px 14px;
  }
}
</style>
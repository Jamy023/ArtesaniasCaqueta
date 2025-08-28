// stores/cartStore.js - Store del carrito de compras con persistencia en localStorage
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  // ==================== ESTADO ====================
  
  /** @type {Array} Lista de productos en el carrito */
  const items = ref([])
  
  /** @type {boolean} Indica si se está realizando alguna operación del carrito */
  const isLoading = ref(false)

  // ==================== GETTERS/COMPUTED ====================
  
  /**
   * Calcula el número total de productos en el carrito
   * @returns {number} Suma de todas las cantidades de productos
   */
  const itemCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  /**
   * Calcula el precio total de todos los productos en el carrito
   * @returns {number} Suma del precio * cantidad de todos los productos
   */
  const totalPrice = computed(() => {
    return items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
  })

  /**
   * Verifica si el carrito está vacío
   * @returns {boolean} True si no hay productos en el carrito
   */
  const isEmpty = computed(() => items.value.length === 0)

  // ==================== ACCIONES ====================

  /**
   * Agrega un producto al carrito o incrementa su cantidad si ya existe
   * @param {Object} product - Objeto del producto a agregar
   * @param {string} product.id - ID único del producto
   * @param {string} product.name - Nombre del producto
   * @param {number} product.price - Precio del producto
   * @param {string} product.main_image - URL de la imagen principal
   * @param {string} product.slug - Slug del producto para URLs
   * @param {number} product.stock - Stock disponible del producto
   * @param {number} [quantity=1] - Cantidad a agregar al carrito
   */
  const addToCart = (product, quantity = 1) => {
    const existingItem = items.value.find(item => item.id === product.id)
    
    if (existingItem) {
      // Si el producto ya existe, incrementar la cantidad
      existingItem.quantity += quantity
    } else {
      // Si es un producto nuevo, agregarlo al carrito
      items.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        main_image: product.main_image,
        slug: product.slug,
        quantity: quantity,
        stock: product.stock || 99
      })
    }
    
    // Guardar cambios en localStorage
    saveToLocalStorage()
  }

  /**
   * Elimina completamente un producto del carrito
   * @param {string} productId - ID del producto a eliminar
   */
  const removeFromCart = (productId) => {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)
      saveToLocalStorage()
    }
  }

  /**
   * Incrementa en 1 la cantidad de un producto en el carrito
   * Solo incrementa si no excede el stock disponible
   * @param {string} productId - ID del producto
   */
  const increaseQuantity = (productId) => {
    const item = items.value.find(item => item.id === productId)
    if (item && item.quantity < item.stock) {
      item.quantity++
      saveToLocalStorage()
    }
  }

  /**
   * Disminuye en 1 la cantidad de un producto en el carrito
   * No permite bajar de 1 unidad (usa removeFromCart para eliminar)
   * @param {string} productId - ID del producto
   */
  const decreaseQuantity = (productId) => {
    const item = items.value.find(item => item.id === productId)
    if (item && item.quantity > 1) {
      item.quantity--
      saveToLocalStorage()
    }
  }

  /**
   * Actualiza la cantidad exacta de un producto en el carrito
   * Valida que la cantidad esté entre 1 y el stock disponible
   * @param {string} productId - ID del producto
   * @param {number} newQuantity - Nueva cantidad (debe ser > 0 y <= stock)
   */
  const updateQuantity = (productId, newQuantity) => {
    const item = items.value.find(item => item.id === productId)
    if (item && newQuantity > 0 && newQuantity <= item.stock) {
      item.quantity = newQuantity
      saveToLocalStorage()
    }
  }

  /**
   * Vacía completamente el carrito y limpia el localStorage
   */
  const clearCart = () => {
    items.value = []
    localStorage.removeItem('cart')
  }

  /**
   * Verifica si un producto específico está en el carrito
   * @param {string} productId - ID del producto a verificar
   * @returns {boolean} True si el producto está en el carrito
   */
  const isInCart = (productId) => {
    return items.value.some(item => item.id === productId)
  }

  /**
   * Obtiene la cantidad de un producto específico en el carrito
   * @param {string} productId - ID del producto
   * @returns {number} Cantidad del producto en el carrito (0 si no está)
   */
  const getItemQuantity = (productId) => {
    const item = items.value.find(item => item.id === productId)
    return item ? item.quantity : 0
  }

  // ==================== PERSISTENCIA ====================

  /**
   * Guarda el estado actual del carrito en localStorage
   * Maneja errores de almacenamiento (cuota excedida, etc.)
   */
  const saveToLocalStorage = () => {
    try {
      localStorage.setItem('cart', JSON.stringify(items.value))
    } catch (error) {
      console.error('Error saving cart to localStorage:', error)
    }
  }

  /**
   * Carga el carrito guardado desde localStorage
   * Valida que los datos sean correctos antes de cargarlos
   * Si hay error, inicializa un carrito vacío
   */
  const loadFromLocalStorage = () => {
    try {
      const savedCart = localStorage.getItem('cart')
      if (savedCart) {
        const parsedCart = JSON.parse(savedCart)
        if (Array.isArray(parsedCart)) {
          items.value = parsedCart
        }
      }
    } catch (error) {
      console.error('Error loading cart from localStorage:', error)
      items.value = []
    }
  }

  // ==================== VALIDACIÓN Y CHECKOUT ====================

  /**
   * Valida que todos los productos en el carrito tengan stock suficiente
   * @returns {Promise<boolean>} Promise que resuelve a true si todo está válido
   * @throws {Error} Si algún producto no tiene stock suficiente
   */
  const validateStock = async () => {
    // Aquí podrías hacer una llamada a la API para verificar stock actual
    // Por ahora, simplemente validamos que todos los items tengan stock > 0
    const invalidItems = items.value.filter(item => item.quantity > item.stock)
    
    if (invalidItems.length > 0) {
      throw new Error(`Algunos productos no tienen stock suficiente: ${invalidItems.map(item => item.name).join(', ')}`)
    }
    
    return true
  }

  /**
   * Prepara y retorna los datos del carrito formateados para el checkout
   * Incluye cálculos de subtotales, envío y total
   * @returns {Object} Objeto con toda la información necesaria para el checkout
   * @returns {Array} return.items - Items del carrito con subtotales calculados
   * @returns {number} return.subtotal - Subtotal de todos los productos
   * @returns {number} return.shipping - Costo de envío (actualmente 0)
   * @returns {number} return.total - Total final a pagar
   * @returns {number} return.itemCount - Cantidad total de productos
   */
  const getCheckoutData = () => {
    return {
      items: items.value.map(item => ({
        id: item.id,
        name: item.name,
        price: item.price,
        quantity: item.quantity,
        subtotal: item.price * item.quantity,
        image: item.main_image
      })),
      subtotal: totalPrice.value,
      shipping: 0, // Por ahora gratis
      total: totalPrice.value,
      itemCount: itemCount.value
    }
  }

  // ==================== EXPOSICIÓN DE LA API ====================

  return {
    // Estado
    items,
    isLoading,
    
    // Getters computados
    itemCount,
    totalPrice,
    isEmpty,
    
    // Acciones principales
    addToCart,
    removeFromCart,
    increaseQuantity,
    decreaseQuantity,
    updateQuantity,
    clearCart,
    
    // Consultas
    isInCart,
    getItemQuantity,
    
    // Persistencia
    loadFromLocalStorage,
    saveToLocalStorage,
    
    // Validación y checkout
    validateStock,
    getCheckoutData
  }
})
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  // State
  const items = ref([])
  const isLoading = ref(false)

  // Getters
  const itemCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  const totalPrice = computed(() => {
    return items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
  })

  const isEmpty = computed(() => items.value.length === 0)

  // Actions
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
    
    saveToLocalStorage()
    
    // Opcional: mostrar notificación
    console.log(`${product.name} agregado al carrito`)
  }

  const removeFromCart = (productId) => {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)
      saveToLocalStorage()
    }
  }

  const increaseQuantity = (productId) => {
    const item = items.value.find(item => item.id === productId)
    if (item && item.quantity < item.stock) {
      item.quantity++
      saveToLocalStorage()
    }
  }

  const decreaseQuantity = (productId) => {
    const item = items.value.find(item => item.id === productId)
    if (item && item.quantity > 1) {
      item.quantity--
      saveToLocalStorage()
    }
  }

  const updateQuantity = (productId, newQuantity) => {
    const item = items.value.find(item => item.id === productId)
    if (item && newQuantity > 0 && newQuantity <= item.stock) {
      item.quantity = newQuantity
      saveToLocalStorage()
    }
  }

  const clearCart = () => {
    items.value = []
    localStorage.removeItem('cart')
  }

  const isInCart = (productId) => {
    return items.value.some(item => item.id === productId)
  }

  const getItemQuantity = (productId) => {
    const item = items.value.find(item => item.id === productId)
    return item ? item.quantity : 0
  }

  // Persistence
  const saveToLocalStorage = () => {
    try {
      localStorage.setItem('cart', JSON.stringify(items.value))
    } catch (error) {
      console.error('Error saving cart to localStorage:', error)
    }
  }

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

  // Validar stock antes de proceder al checkout
  const validateStock = async () => {
    // Aquí podrías hacer una llamada a la API para verificar stock actual
    // Por ahora, simplemente validamos que todos los items tengan stock > 0
    const invalidItems = items.value.filter(item => item.quantity > item.stock)
    
    if (invalidItems.length > 0) {
      throw new Error(`Algunos productos no tienen stock suficiente: ${invalidItems.map(item => item.name).join(', ')}`)
    }
    
    return true
  }

  // Obtener resumen para checkout
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

  return {
    // State
    items,
    isLoading,
    
    // Getters
    itemCount,
    totalPrice,
    isEmpty,
    
    // Actions
    addToCart,
    removeFromCart,
    increaseQuantity,
    decreaseQuantity,
    updateQuantity,
    clearCart,
    isInCart,
    getItemQuantity,
    loadFromLocalStorage,
    saveToLocalStorage,
    validateStock,
    getCheckoutData
  }
})
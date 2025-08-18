// Utilidad global para manejar URLs de imágenes de productos
export const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png';
  if (main_image.startsWith('http')) return main_image;
  
  // Si ya empieza con /storage/, usar tal como está
  if (main_image.startsWith('/storage/')) return main_image;
  if (main_image.startsWith('storage/')) return '/' + main_image;
  
  // Si empieza con products/, agregar solo /storage/
  if (main_image.startsWith('products/')) {
    return '/storage/' + main_image;
  }
  
  // Si no tiene prefijo, asumir que va en products/
  return `/storage/products/${main_image}`;
};

// Manejar errores de imagen (fallback a logo)
export const handleImageError = (event) => {
  event.target.src = '/img/logo.png';
};
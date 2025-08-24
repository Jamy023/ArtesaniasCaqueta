// Utilidad global para manejar URLs de imágenes de productos
export const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png';
  if (main_image.startsWith('http')) return main_image;

  // Limpiar cualquier prefijo y construir ruta correcta
  const cleanImage = main_image.replace(/^(public\/|storage\/|storage\/app\/public\/|products\/)/, '');
  return `/storage/products/${cleanImage}`;
};

// Manejar errores de imagen (fallback a logo)
export const handleImageError = (event) => {
  event.target.src = '/img/logo.png';
};
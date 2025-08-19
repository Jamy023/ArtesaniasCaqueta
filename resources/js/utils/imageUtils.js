// Utilidad global para manejar URLs de imágenes de productos
export const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png';
  if (main_image.startsWith('http')) return main_image;
  
  // Limpiar completamente la ruta y construir desde cero
  let cleanImage = main_image;
  
  // Remover cualquier prefijo conocido
  cleanImage = cleanImage.replace(/^(public\/|storage\/app\/public\/|storage\/|\/storage\/|products\/)/, '');
  
  // Construir la URL final
  const finalUrl = `/storage/products/${cleanImage}`;
  
  return finalUrl;
};

// Manejar errores de imagen (fallback a logo)
export const handleImageError = (event) => {
  event.target.src = '/img/logo.png';
};
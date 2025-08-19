// Utilidad global para manejar URLs de imágenes de productos
export const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png';
  if (main_image.startsWith('http')) return main_image;
  
  // Limpiar la ruta de la imagen
  let cleanImage = main_image;
  cleanImage = cleanImage.replace(/^(public\/|storage\/app\/public\/|storage\/|\/storage\/|products\/)/, '');
  
  // Ahora Laravel manejará esta ruta y servirá el archivo
  return `/storage/products/${cleanImage}`;
};

// Manejar errores de imagen (fallback a logo)
export const handleImageError = (event) => {
  event.target.src = '/img/logo.png';
};
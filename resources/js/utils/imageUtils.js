// Utilidad global para manejar URLs de imágenes de productos
export const getProductImageUrl = (main_image) => {
  if (!main_image) return '/img/logo.png';
  if (main_image.startsWith('http')) return main_image;
  
  // TEMPORAL: usar URLs directas mientras arreglamos las rutas
  const baseUrl = 'https://artesaniascaqueta-production.up.railway.app';
  
  // Limpiar la ruta de la imagen
  let cleanImage = main_image;
  cleanImage = cleanImage.replace(/^(public\/|storage\/app\/public\/|storage\/|\/storage\/|products\/)/, '');
  
  // Intentar primero con API endpoint directo
  return `${baseUrl}/api/image/${encodeURIComponent(cleanImage)}`;
};

// Manejar errores de imagen (fallback a logo)
export const handleImageError = (event) => {
  event.target.src = '/img/logo.png';
};
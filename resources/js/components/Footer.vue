<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section">
        <h3>🎨 Artesanias de la Amazonia</h3>
        <p>Productos únicos hechos a mano con amor y tradición colombiana.</p>
        <div class="social-links">
          <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
          <a href="#" class="social-link">📷</a>
          <a href="#" class="social-link">🐦</a>
        </div>
      </div>
      
      <div class="footer-section">
        <h4>Categorías</h4>
        <ul>
          <li><router-link to="/category/abanicos">Abanicos</router-link></li>
          <li><router-link to="/category/bolsos">Bolsos</router-link></li>
          <li><router-link to="/category/pinturas-arte">Pinturas</router-link></li>
          <li><router-link to="/category/artesanias-madera">Madera</router-link></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Enlaces</h4>
        <ul>
          <li><router-link to="/">Inicio</router-link></li>
          <li><router-link to="/products">Productos</router-link></li>
          <li><router-link to="/about">Acerca de</router-link></li>
          <li><router-link to="/contact">Contacto</router-link></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Contacto</h4>
        <p>📍 Florencia, Caquetá, Colombia</p>
        <p>📧 info@artesaniascolombianas.com</p>
        <p>📱 +57 300 123 4567</p>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; 2024 Artesanías Colombianas. Todos los derechos reservados.</p>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: white;
  padding: 50px 0 20px;
  margin-top: 80px;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
}

.footer-section h3 {
  font-size: 24px;
  margin-bottom: 20px;
  color: #ecf0f1;
}

.footer-section h4 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #ecf0f1;
}

.footer-section p {
  margin-bottom: 10px;
  color: #bdc3c7;
  line-height: 1.6;
}

.footer-section ul {
  list-style: none;
}

.footer-section li {
  margin-bottom: 8px;
}

.footer-section a {
  color: #bdc3c7;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-section a:hover {
  color: #ecf0f1;
}

.social-links {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.social-link {
  display: inline-block;
  padding: 8px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  text-decoration: none;
  font-size: 18px;
  transition: all 0.3s ease;
}

.social-link:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
}

.footer-bottom {
  border-top: 1px solid #34495e;
  margin-top: 40px;
  padding-top: 20px;
  text-align: center;
}

.footer-bottom p {
  color: #95a5a6;
  font-size: 14px;
}

@media (max-width: 768px) {
  .footer-container {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  
  .social-links {
    justify-content: center;
  }
}
</style>
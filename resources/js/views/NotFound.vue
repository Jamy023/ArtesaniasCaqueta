<template>
  <div class="not-found">
    <div class="content">
      <h1>404</h1>
      <h2>Página no encontrada</h2>
      <p>La página que buscas no existe o ha sido movida.</p>
      <div class="actions">
        <router-link to="/" class="btn-primary">Ir al inicio</router-link>
        <router-link to="/products" class="btn-secondary">Ver productos</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
// Componente simple sin lógica adicional
</script>

<style scoped>
.not-found {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2E7D32 0%, #1B5E20 100%);
  color: white;
  text-align: center;
}

.content h1 {
  font-size: 6rem;
  margin-bottom: 1rem;
  opacity: 0.8;
}

.content h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.content p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #4CAF50;
  color: white;
}

.btn-secondary {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-primary:hover {
  background: #45a049;
}

.btn-secondary:hover {
  background: white;
  color: #2E7D32;
}
</style>
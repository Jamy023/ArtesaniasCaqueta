// Script para configurar automáticamente el .env según el entorno
const fs = require('fs');
const path = require('path');

// Detectar si se está usando ngrok
const isNgrok = process.argv.includes('--ngrok');
const ngrokUrl = process.argv.find(arg => arg.startsWith('--url='))?.split('=')[1];

let envContent;

if (isNgrok && ngrokUrl) {
  console.log(`🚀 Configurando para ngrok: ${ngrokUrl}`);
  envContent = fs.readFileSync('.env.local', 'utf8');
  
  // Actualizar URLs para ngrok
  envContent = envContent.replace(/APP_URL=.*/, `APP_URL=${ngrokUrl}`);
  
  // Agregar ASSET_URL si no existe
  if (!envContent.includes('ASSET_URL')) {
    envContent += `\nASSET_URL=${ngrokUrl}\n`;
  } else {
    envContent = envContent.replace(/ASSET_URL=.*/, `ASSET_URL=${ngrokUrl}`);
  }
  
  // Agregar VITE_HMR_HOST si no existe  
  const hostname = ngrokUrl.replace('https://', '').replace('http://', '');
  if (!envContent.includes('VITE_HMR_HOST')) {
    envContent += `VITE_HMR_HOST=${hostname}\n`;
  } else {
    envContent = envContent.replace(/VITE_HMR_HOST=.*/, `VITE_HMR_HOST=${hostname}`);
  }
  
} else {
  console.log('🏠 Configurando para desarrollo local');
  envContent = fs.readFileSync('.env.local', 'utf8');
}

// Escribir el .env
fs.writeFileSync('.env', envContent);
console.log('✅ Archivo .env actualizado');
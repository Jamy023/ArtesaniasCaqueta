// resources/js/axios.js
import axios from 'axios';

// Configuración base de Axios
axios.defaults.baseURL = window.location.origin;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Interceptor para manejar el token CSRF
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Interceptor para requests
axios.interceptors.request.use(
    (config) => {
        // Asegurar que siempre se incluya el CSRF token
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token.content;
        }
        
        // Log para debugging
        console.log('Axios Request:', {
            method: config.method?.toUpperCase(),
            url: config.url,
            withCredentials: config.withCredentials,
            headers: {
                'Content-Type': config.headers['Content-Type'],
                'X-CSRF-TOKEN': config.headers['X-CSRF-TOKEN'] ? 'presente' : 'ausente',
                'X-Requested-With': config.headers['X-Requested-With']
            }
        });
        
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para responses
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        console.log('Axios Error:', {
            status: error.response?.status,
            statusText: error.response?.statusText,
            message: error.message,
            url: error.config?.url,
            data: error.response?.data
        });

        // Manejar errores de autenticación
        if (error.response?.status === 401) {
            // Redirigir al login si no está autenticado
            if (window.location.pathname.startsWith('/admin') && !window.location.pathname.includes('/login')) {
                window.location.href = '/admin/login';
            }
        }

        // Manejar errores CSRF
        if (error.response?.status === 419) {
            console.error('CSRF Token Mismatch - Recargando página...');
            window.location.reload();
        }

        return Promise.reject(error);
    }
);

export default axios;
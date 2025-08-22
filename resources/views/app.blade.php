<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artesanias De La Amazonia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- ePayco SDK Optimizado para Checkout Onpage -->
    <script src="https://checkout.epayco.co/checkout.js"></script>
    <script>
        // Configuración global de ePayco
        window.epaycoConfig = {
            test: {{ strtoupper(env('EPAYCO_TEST_MODE', 'TRUE')) === 'TRUE' ? 'true' : 'false' }},
            public_key: "{{ env('EPAYCO_PUBLIC_KEY', '') }}",
            customer_id: "{{ env('EPAYCO_CUSTOMER_ID', '1556492') }}"
        };
        
        // Validar configuración
        if (!window.epaycoConfig.public_key || window.epaycoConfig.public_key.includes('*')) {
            console.error('❌ ePayco P_KEY inválida o no configurada');
        }
        
        // Debug info mejorado
        console.log('🚀 ePayco SDK Cargado - Checkout Onpage Ready:', {
            test_mode: window.epaycoConfig.test,
            has_public_key: !!window.epaycoConfig.public_key,
            public_key_preview: window.epaycoConfig.public_key.substring(0, 10) + '...',
            customer_id: window.epaycoConfig.customer_id,
            sdk_available: typeof window.ePayco !== 'undefined'
        });
        
        // Verificar SDK cuando esté disponible
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if (typeof window.ePayco !== 'undefined') {
                    console.log('✅ ePayco SDK disponible para Checkout Onpage');
                } else {
                    console.error('❌ ePayco SDK no cargó correctamente');
                }
            }, 1000);
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    <div id="app"></div>
</body>
</html>

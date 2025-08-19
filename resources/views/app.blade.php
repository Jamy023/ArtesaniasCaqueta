<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artesanias De La Amazonia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- ePayco SDK Mejorado -->
    <script src="https://checkout.epayco.co/checkout.js"></script>
    <script>
        // Configuración global de ePayco
        window.epaycoConfig = {
            test: true, // cambiar a false en producción
            public_key: "{{ env('EPAYCO_PUBLIC_KEY', '') }}",
            customer_id: "{{ env('EPAYCO_CUSTOMER_ID', '1556492') }}"
        };
        
        // Debug info
        console.log('ePayco Config loaded:', {
            test: window.epaycoConfig.test,
            has_public_key: !!window.epaycoConfig.public_key,
            customer_id: window.epaycoConfig.customer_id
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    <div id="app"></div>
</body>
</html>

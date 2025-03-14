<?php

return [
    'paths' => ['api/*'], // Permitir todas las rutas API
    'allowed_methods' => ['*'], // Permitir todos los métodos (GET, POST, PUT, DELETE, etc.)
    'allowed_origins' => ['*'], // Permitir cualquier origen en desarrollo
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permitir todos los headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, // Cambia a true si usas autenticación basada en cookies
];

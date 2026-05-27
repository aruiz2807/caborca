<?php

return [
    'default_mode' => 'sync',

    'roles' => [
        'Super-Admin' => [
            'description' => 'Acceso a todas las funcionalidades',
            'permissions' => ['*'],
        ],

        'Admin' => [
            'description' => 'Administración general del sistema',
            'permissions' => ['*'],
        ],

        'Asesor' => [
            'description' => 'Gestión operativa de órdenes y citas',
            'permissions' => [
                'view-home-dashboard',
                'view-tsplus',
                'view-orders-active',
                'view-orders-archive',
                'create-appointment',
                'cancel-appointment',
                'update-order-parts',
                'view-messages',
                'update-message',
                'delete-message',
            ],
        ],

        'Gobierno' => [
            'description' => 'Captura y seguimiento de órdenes',
            'permissions' => [
                'view-home-dashboard',
                'view-tsplus',
                'view-orders-active',
                'view-orders-archive',
                'create-order',
                'view-messages',
                'update-message',
            ],
        ],
    ],
];

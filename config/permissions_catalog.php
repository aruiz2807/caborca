<?php

return [
    'groups' => [
        'home' => [
            'label' => 'Home',
            'permissions' => [
                'view-home-dashboard',
                'view-tsplus',
            ],
        ],
        'orders' => [
            'label' => 'Ordenes',
            'permissions' => [
                'view-orders-active',
                'view-orders-archive',
                'create-order',
                'update-order-parts',
                'create-appointment',
                'cancel-order',
                'cancel-appointment',
            ],
        ],
        'messages' => [
            'label' => 'Mensajes',
            'permissions' => [
                'view-messages',
                'update-message',
                'delete-message',
            ],
        ],
        'settings_locations' => [
            'label' => 'Settings / Localidades',
            'permissions' => [
                'view-locations',
                'create-location',
                'update-location',
                'delete-location',
            ],
        ],
        'settings_dependencies' => [
            'label' => 'Settings / Dependencias',
            'permissions' => [
                'view-dependencies',
                'create-dependency',
                'update-dependency',
                'delete-dependency',
            ],
        ],
        'settings_workshops' => [
            'label' => 'Settings / Talleres',
            'permissions' => [
                'view-workshops',
                'create-workshop',
                'update-workshop',
                'delete-workshop',
            ],
        ],
        'settings_services' => [
            'label' => 'Settings / Servicios',
            'permissions' => [
                'view-services',
                'create-service-type',
                'update-service-type',
                'delete-service-type',
            ],
        ],
        'settings_users' => [
            'label' => 'Settings / Usuarios',
            'permissions' => [
                'view-users',
                'create-user',
                'update-user',
                'delete-user',
            ],
        ],
        'settings_roles' => [
            'label' => 'Settings / Roles',
            'permissions' => [
                'view-roles',
                'create-role',
                'update-role',
                'delete-role',
                'manage-role-permissions',
            ],
        ],
        'settings_smtp' => [
            'label' => 'Settings / SMTP',
            'permissions' => [
                'view-smtp',
                'manage-smtp',
            ],
        ],
        'settings_tsplus' => [
            'label' => 'Settings / TSPlus',
            'permissions' => [
                'view-tsplus-settings',
                'manage-tsplus-settings',
            ],
        ],
        'settings_bi' => [
            'label' => 'Settings / BI',
            'permissions' => [
                'view-bi-settings',
                'manage-bi-settings',
            ],
        ],
    ],
];

<?php

return [
    'rules' => [
        [
            'if_any' => ['create-order', 'cancel-order', 'create-appointment', 'cancel-appointment', 'update-order-parts'],
            'grant' => ['view-orders-active'],
        ],
        [
            'if_any' => ['create-order', 'cancel-order'],
            'grant' => ['view-orders-archive'],
        ],
        [
            'if_any' => ['update-message', 'delete-message'],
            'grant' => ['view-messages'],
        ],
        [
            'if_any' => ['create-location', 'update-location', 'delete-location'],
            'grant' => ['view-locations'],
        ],
        [
            'if_any' => ['create-dependency', 'update-dependency', 'delete-dependency'],
            'grant' => ['view-dependencies'],
        ],
        [
            'if_any' => ['create-workshop', 'update-workshop', 'delete-workshop'],
            'grant' => ['view-workshops'],
        ],
        [
            'if_any' => ['create-service-type', 'update-service-type', 'delete-service-type'],
            'grant' => ['view-services'],
        ],
        [
            'if_any' => ['create-user', 'update-user', 'delete-user'],
            'grant' => ['view-users'],
        ],
        [
            'if_any' => ['create-role', 'update-role', 'delete-role', 'manage-role-permissions'],
            'grant' => ['view-roles'],
        ],
        [
            'if_any' => ['manage-smtp'],
            'grant' => ['view-smtp'],
        ],
        [
            'if_any' => ['manage-tsplus-settings'],
            'grant' => ['view-tsplus-settings'],
        ],
    ],
];

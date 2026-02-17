import {
  File,
  House,
  Mail,
  Settings2,
} from 'lucide-vue-next'

export const MenuItems = [
    {
        title: 'Home',
        url: '/home',
        icon: House,
        options: [
            {
                title: 'Dashboard',
                url: '/home',
            }
        ],
    },
    {
        title: 'Ordenes',
        url: '/orders',
        icon: File,
        options: [
            {
                title: 'Activas',
                url: '/orders/active',
            },
            {
                title: 'Historico',
                url: '/orders/archive',
            },
        ],
    },
    {
        title: 'Mensajes',
        url: '#',
        icon: Mail,
        options: [
            {
                title: 'Nuevos',
                url: '#',
            },
            {
                title: 'Leidos',
                url: '#',
            },
        ],
    },
    {
        title: 'Settings',
        url: '/settings',
        icon: Settings2,
        options: [
            {
                title: 'Localidades',
                url: '/settings/locations',
            },
            {
                title: 'Dependencias',
                url: '/settings/dependencies',
            },
            {
                title: 'Talleres',
                url: '/settings/workshops',
            },
            {
                title: 'Servicios',
                url: '/settings/services',
            },
            {
                title: 'Usuarios',
                url: '/settings/users',
            },
            {
                title: 'Roles',
                url: '/settings/roles',
            },
        ],
    },
];

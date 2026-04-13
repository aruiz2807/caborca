import {
  File,
  House,
  Mail,
  Settings2,
} from 'lucide-vue-next'

export const MenuItems = [
    {
        title: 'Home',
        routeName: 'home',
        activePatterns: ['home', 'dashboard'],
        icon: House,
        options: [
            {
                title: 'Dashboard',
                routeName: 'home',
                activePatterns: ['home', 'dashboard'],
            }
        ],
    },
    {
        title: 'Ordenes',
        activePatterns: ['orders.*'],
        icon: File,
        options: [
            {
                title: 'Activas',
                routeName: 'orders.active',
                activePatterns: ['orders.active'],
            },
            {
                title: 'Historico',
                routeName: 'orders.archive',
                activePatterns: ['orders.archive', 'orders.archive_orders'],
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
        activePatterns: ['users.*', 'roles.*', 'dependencies.*', 'locations.*', 'workshops.*', 'services.*'],
        icon: Settings2,
        options: [
            {
                title: 'Localidades',
                routeName: 'locations.index',
                activePatterns: ['locations.*'],
            },
            {
                title: 'Dependencias',
                routeName: 'dependencies.index',
                activePatterns: ['dependencies.*'],
            },
            {
                title: 'Talleres',
                routeName: 'workshops.index',
                activePatterns: ['workshops.*'],
            },
            {
                title: 'Servicios',
                routeName: 'services.index',
                activePatterns: ['services.*'],
            },
            {
                title: 'Usuarios',
                routeName: 'users.index',
                activePatterns: ['users.*'],
            },
            {
                title: 'Roles',
                routeName: 'roles.index',
                activePatterns: ['roles.*'],
            },
        ],
    },
];

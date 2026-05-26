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
        activePatterns: ['home', 'dashboard', 'tsplus.*'],
        icon: House,
        options: [
            {
                title: 'Dashboard',
                routeName: 'home',
                activePatterns: ['home', 'dashboard'],
            },
            {
                title: 'TSPlus',
                routeName: 'tsplus.index',
                activePatterns: ['tsplus.*'],
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
        routeName: 'messages.index',
        activePatterns: ['messages.*'],
        icon: Mail,
        options: [
            {
                title: 'Recibidos',
                routeName: 'messages.index',
                activePatterns: ['messages.*'],
            },
        ],
    },
    {
        title: 'Settings',
        hiddenForExternal: true,
        activePatterns: ['users.*', 'roles.*', 'dependencies.*', 'locations.*', 'workshops.*', 'services.*', 'smtp.*', 'tsplus-settings.*'],
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
                requiresSuperAdmin: true,
            },
            {
                title: 'Roles',
                routeName: 'roles.index',
                activePatterns: ['roles.*'],
                requiresSuperAdmin: true,
            },
            {
                title: 'SMTP',
                routeName: 'smtp.index',
                activePatterns: ['smtp.*'],
                requiresSuperAdmin: true,
            },
            {
                title: 'TSPlus',
                routeName: 'tsplus-settings.index',
                activePatterns: ['tsplus-settings.*'],
                requiresSuperAdmin: true,
            },
        ],
    },
];

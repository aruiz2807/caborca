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
                requiresPermission: 'view-home-dashboard',
            },
            {
                title: 'TSPlus',
                routeName: 'tsplus.index',
                activePatterns: ['tsplus.*'],
                requiresPermission: 'view-tsplus',
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
                requiresPermission: 'view-orders-active',
            },
            {
                title: 'Historico',
                routeName: 'orders.archive',
                activePatterns: ['orders.archive', 'orders.archive_orders'],
                requiresPermission: 'view-orders-archive',
            },
        ],
    },
    {
        title: 'Mensajes',
        routeName: 'messages.index',
        activePatterns: ['messages.*'],
        icon: Mail,
        requiresPermission: 'view-messages',
        options: [
            {
                title: 'Recibidos',
                routeName: 'messages.index',
                activePatterns: ['messages.*'],
                requiresPermission: 'view-messages',
            },
        ],
    },
    {
        title: 'Settings',
        hiddenForExternal: true,
        activePatterns: ['users.*', 'roles.*', 'dependencies.*', 'locations.*', 'workshops.*', 'services.*', 'smtp.*', 'tsplus-settings.*', 'bi-settings.*'],
        icon: Settings2,
        options: [
            {
                title: 'Localidades',
                routeName: 'locations.index',
                activePatterns: ['locations.*'],
                requiresPermission: 'view-locations',
            },
            {
                title: 'Dependencias',
                routeName: 'dependencies.index',
                activePatterns: ['dependencies.*'],
                requiresPermission: 'view-dependencies',
            },
            {
                title: 'Talleres',
                routeName: 'workshops.index',
                activePatterns: ['workshops.*'],
                requiresPermission: 'view-workshops',
            },
            {
                title: 'Servicios',
                routeName: 'services.index',
                activePatterns: ['services.*'],
                requiresPermission: 'view-services',
            },
            {
                title: 'Usuarios',
                routeName: 'users.index',
                activePatterns: ['users.*'],
                requiresPermission: 'view-users',
            },
            {
                title: 'Roles',
                routeName: 'roles.index',
                activePatterns: ['roles.*'],
                requiresPermission: 'view-roles',
            },
            {
                title: 'SMTP',
                routeName: 'smtp.index',
                activePatterns: ['smtp.*'],
                requiresPermission: 'view-smtp',
            },
            {
                title: 'TSPlus',
                routeName: 'tsplus-settings.index',
                activePatterns: ['tsplus-settings.*'],
                requiresPermission: 'view-tsplus-settings',
            },
            {
                title: 'BI',
                routeName: 'bi-settings.index',
                activePatterns: ['bi-settings.*'],
                requiresPermission: 'view-bi-settings',
            },
        ],
    },
];

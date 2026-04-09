import { usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

export function useTrans( value, replacements = {} )
{
    const array = usePage().props.translations;

    if (array && array[value] != null) {
        return array[value];
    }

    return trans(value, replacements);
}

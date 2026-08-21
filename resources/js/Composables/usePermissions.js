import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();
    const role = computed(() => page.props.auth?.user?.role ?? null);
    const isSuperAdmin = computed(() => role.value === 'super-admin');
    const isAdmin = computed(() => role.value === 'admin');
    const isHod = computed(() => role.value === 'hod');

    function hasRole(roles) {
        const list = Array.isArray(roles) ? roles : [roles];

        return list.includes(role.value);
    }

    function can(ability) {
        const [group, action] = String(ability).split('.');

        return Boolean(page.props.can?.[group]?.[action]);
    }

    return {
        role,
        isSuperAdmin,
        isAdmin,
        isHod,
        hasRole,
        can,
    };
}

import { ref } from 'vue';

export function useConfirm() {
    const open = ref(false);
    const title = ref('Are you sure?');
    const message = ref('This action cannot be undone.');
    let resolver = null;

    function ask(options = {}) {
        title.value = options.title ?? 'Are you sure?';
        message.value = options.message ?? 'This action cannot be undone.';
        open.value = true;

        return new Promise((resolve) => {
            resolver = resolve;
        });
    }

    function confirm() {
        open.value = false;
        resolver?.(true);
        resolver = null;
    }

    function cancel() {
        open.value = false;
        resolver?.(false);
        resolver = null;
    }

    return {
        open,
        title,
        message,
        ask,
        confirm,
        cancel,
    };
}

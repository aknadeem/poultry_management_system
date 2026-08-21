import { onMounted } from 'vue';

const APP_LAYOUT = JSON.stringify({
    mode: 'light',
    width: 'fluid',
    menuPosition: 'fixed',
    sidebar: { color: 'dark', size: 'default', showuser: true },
    topbar: { color: 'dark' },
    showRightSidebarOnPageLoad: false,
});

function refreshHyperPlugins() {
    window.feather?.replace?.();
    window.Waves?.init?.();
}

export function useAppBody() {
    onMounted(() => {
        const body = document.body;
        body.classList.remove('authentication-bg', 'authentication-bg-pattern');
        body.removeAttribute('style');
        body.classList.add('loading');
        body.setAttribute('data-layout-mode', 'horizontal');
        body.setAttribute('data-layout', APP_LAYOUT);
        refreshHyperPlugins();
        body.classList.remove('loading');
    });
}

export function useAuthBody() {
    onMounted(() => {
        const body = document.body;
        body.classList.add('authentication-bg', 'authentication-bg-pattern');
        body.style.backgroundColor = '#38414a';
        body.removeAttribute('data-layout-mode');
        body.removeAttribute('data-layout');
        refreshHyperPlugins();
        body.classList.remove('loading');
    });
}

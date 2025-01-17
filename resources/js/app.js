import './bootstrap';
import '../css/app.css';
import '../css/notification.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import useClickOutSide from './Components/Base/hooks/useClickOutSide';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast)
            .directive('click-outside', useClickOutSide)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Registrar el Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then(() => console.log('Service Worker registrado con éxito.'))
        .catch((error) => console.error('Error al registrar el Service Worker:', error));
}

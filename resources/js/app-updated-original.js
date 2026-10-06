import { createApp, defineAsyncComponent } from 'vue';
import App from './views/layouts/App.vue';
import router from './router';

// Keep layout components static at the top level
import Navbar from './views/layouts/Navbar.vue';
import Sidebar from './views/layouts/Sidebar.vue';

async function bootstrap() {
    
    // Dynamic imports for base dependencies
    await import('./bootstrap');
    const mitt = (await import('mitt')).default;
    
    const app = createApp(App).use(router);

    window.Broadcast = new mitt();
    window.Broadcast.$emit = window.Broadcast.emit;
    window.Broadcast.$on = window.Broadcast.on;

    // Dynamic import for Chart.js
    const Chart = (await import('chart.js/auto')).default;
    window.Chart = Chart;

    // Dynamic imports for custom plugins loaded in parallel
    const [
        DiscountFormatter,
        DocumentGenerator,
        MoneyFormatter,
        AmountToWordsFormatter,
        UseLocalStorage
    ] = await Promise.all([
        import('./plugins/discount-formatter'),
        import('./plugins/document-generator'),
        import('./plugins/money-formatter'),
        import('./plugins/amount-to-words-converter'),
        import('./plugins/use-local-storage')
    ]);

    app.use(DiscountFormatter.default);
    app.use(DocumentGenerator.default);
    app.use(MoneyFormatter.default);
    app.use(AmountToWordsFormatter.default);
    app.use(UseLocalStorage.default);

    // Dynamic auto-registration for directory components
    Object.entries(import.meta.glob('./components/*.vue')).forEach(([path, importAsyncComponent]) => {
        const componentName = path.split('/').pop().replace(/\.\w+$/, '');
        app.component(componentName, defineAsyncComponent(importAsyncComponent));
    });

    // Statically registered layouts
    app.component('navbar', Navbar);
    app.component('sidebar', Sidebar);

    // Final Mount
    app.mount('#app');
}

bootstrap();

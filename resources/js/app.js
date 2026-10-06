/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
// import store from './store';
import Chart from 'chart.js/auto';
import router from './router';
import mitt from 'mitt';
import DiscountFormatter from './plugins/discount-formatter';
import DocumentGenerator from './plugins/document-generator';
import MoneyFormatter from './plugins/money-formatter';
import AmountToWordsFormatter from './plugins/amount-to-words-converter';
import UseLocalStorage from './plugins/use-local-storage';
import App from './views/layouts/App.vue';

const app = createApp(App).use(router);

window.Broadcast = new mitt();
Broadcast.$emit = Broadcast.emit;
Broadcast.$on = Broadcast.on;

// Charts
window.Chart = Chart;

// Custom Plugins
app.use(DiscountFormatter);
app.use(DocumentGenerator);
app.use(MoneyFormatter);
app.use(AmountToWordsFormatter);
app.use(UseLocalStorage);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// OAuth2
/*Vue.component('passport-clients', require('./components/passport/Clients.vue'));
Vue.component('passport-authorized-clients', require('./components/passport/AuthorizedClients.vue'));
Vue.component('passport-personal-access-tokens', require('./components/passport/PersonalAccessTokens.vue'));*/

Object.entries(import.meta.glob('./components/*.vue', { eager: true })).forEach(([path, definition]) => {
    app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
});

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */
// app.use(Vuelidate);

// Layouts
import Navbar from './views/layouts/Navbar.vue';
app.component('navbar', Navbar);

import Sidebar from './views/layouts/Sidebar.vue';
app.component('sidebar', Sidebar);

app.mount('#app');

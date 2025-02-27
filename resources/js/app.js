import { createPinia } from 'pinia';
import { createApp } from 'vue';
import App from './App.vue';
import './bootstrap';
import router from './router/index.js'; // Update this line with explicit path

// Create Vue app
const app = createApp(App);

// Use plugins
app.use(createPinia());
app.use(router);

// Mount app
app.mount('#app');
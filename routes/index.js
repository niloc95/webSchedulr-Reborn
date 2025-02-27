import { createRouter, createWebHistory } from 'vue-router';
import Calendar from '../views/Calendar.vue';
import Home from '../views/Home.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/calendar',
        name: 'calendar',
        component: Calendar
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
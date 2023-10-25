import { createRouter, createWebHistory } from 'vue-router';

// qui importo viste/componenti
import Register from './components/Register.vue';
import Login from './components/Login.vue';
import HomePage from './components/HomePage.vue';
import TicketsPage from './components/TicketsPage.vue';

const routes = [
    {
      path: '/register',
      name: 'Register',
      component: Register
    },
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
        path: '/',
        name: 'Home',
        component: HomePage
    },
    {
        path: '/tickets',
        name: 'TicketsPage',
        component: TicketsPage
    },
];

const router = createRouter({
history: createWebHistory(),
routes
});

export default router;
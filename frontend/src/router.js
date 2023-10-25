import { createRouter, createWebHistory } from 'vue-router';

// qui importo viste/componenti
import Register from './components/Register.vue';
import Login from './components/Login.vue';
import HomePage from './components/HomePage.vue';
import TicketsPage from './components/TicketsPage.vue';
import MessagesPage from './components/MessagesPage.vue';

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
    {
        path: '/tickets/:ticketId/messages',
        name: 'MessagesPage',
        component: MessagesPage,
        // questo mi serve per passarmi il ticketId nel componente MessagesPage
        props: true
    },
];

const router = createRouter({
history: createWebHistory(),
routes
});

export default router;
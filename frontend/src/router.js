import { createRouter, createWebHistory } from 'vue-router';

// qui importo viste/componenti
import Register from './components/Register.vue';
import Login from './components/Login.vue';

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
];

const router = createRouter({
history: createWebHistory(),
routes
});

export default router;
<script>
import axios from 'axios';
import Navbar from './components/Navbar.vue';
import Cookies from 'js-cookie';
import { store } from './store.js';

export default {
  name: 'App',
  components: {
    Navbar,
  },
  created() {
    this.checkUserAuthentication();
  },

  methods: {
    async checkUserAuthentication() {
      try {
        const token = localStorage.getItem('token');
        if (token) {
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

          const response = await axios.get('http://localhost:8001/api/user');

          if (response.data.user) {
            store.isLoggedIn = true;
            store.user = response.data.user;
            store.token = token;
          }
        }
        //questo lo metto qui in modo da impostarlo sempre a true quando questo created finisce
        store.isAppInitialized = true;
      } catch (error) {
        if (error.response && error.response.status === 401) {
          // Il token non è valido o l'utente non è autenticato
          store.isLoggedIn = false;
          localStorage.removeItem('token');
          this.$router.push('/login');
        } else {
          console.error('Errore API:', error);
        }
      }
    },
  },
};
</script>

<template>
  <Navbar />
  <router-view></router-view>
</template>

<style scoped></style>

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
    checkUserAuthentication() {
      console.log('Sto per effettuare la chiamata API');
      store.token = localStorage.getItem('token');
      console.log('Token da inviare:', store.token);
      axios
        .get('http://localhost:8001/api/verify-token', {
          headers: {
            Authorization: `Bearer ${store.token}`,
          },
          withCredentials: true,
        })
        .then(response => {
          console.log('Risposta dal server:', response.data);
        })
        .catch(error => {
          console.error('Errore durante la verifica del token:', error);
        });
    },
  },
};
</script>

<template>
  <Navbar />
  <router-view></router-view>
</template>

<style scoped></style>

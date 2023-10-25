<template>
  <nav>
    <!-- se non loggato, mostra link per Register e Log in -->
    <div v-if="!store.isLoggedIn">
      <router-link to="/register">Register</router-link>
      <router-link to="/login">Login</router-link>
    </div>

    <!-- Se loggato, mostra il nome utente e il menu a tendina e link per la home -->
    <div class="logged-navbar-elements" v-else>
      <div class="navbar-element" @click="toggleDropdown">
        Ciao, {{ store.user.name }}!
        <span class="dropdown-arrow">&#x25BC;</span>
        <div v-if="showDropdown" class="dropdown">
          <router-link to="/">Home</router-link>
          <router-link to="/tickets">Tickets</router-link>
          <a href="#" @click="logout">Logout</a>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import axios from 'axios';
import { store } from '../store.js';

export default {
  name: 'Navbar',
  data() {
    return {
      store,
      showDropdown: false,
    };
  },
  methods: {
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },
    async logout() {
      try {
        // prendo il token dallo store più che altro per leggibilità
        const token = store.token;

        // configura gli headers della richiesta che vanno inseriti come terzo argomento nella chiamata axios
        // with credentials mi serve per i cookie e authorization per rimandare indietro il token per api
        const config = {
          headers: {
            Authorization: 'Bearer ' + token,
          },
          withCredentials: true,
        };

        // invia una richiesta POST all'endpoint di logout
        const response = await axios.post(
          'http://localhost:8001/api/logout',
          {},
          config
        );

        // aggiorna lo stato dell'applicazione
        store.isLoggedIn = false;
        store.token = null;
        store.user = null;

        // reindirizza l'utente alla homepage
        console.log(response.data.message);
        this.$router.push('/');
      } catch (error) {
        console.error('Errore durante il logout:', error);
      }
    },
  },
};
</script>

<style scoped>
nav {
  padding: 10px;
  display: flex;
  justify-content: end;
  align-items: center;
  width: 100%;

  height: 5vh;
  box-shadow: 0px 5px 5px rgba(120, 120, 120, 0.5);
}

nav a {
  padding: 10px;
  cursor: pointer;
}

.navbar-element {
  padding: 10px;
}

.dropdown {
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
}

.dropdown a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown a:hover {
  background-color: #f1f1f1;
}

.logged-navbar-elements {
  cursor: pointer;
}

.dropdown-arrow {
  margin-left: 5px;
  color: grey;
  font-size: 1em;
  vertical-align: middle;
}
</style>

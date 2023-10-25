<template>
  <div class="login-container">
    <h2>Login</h2>

    <form @submit.prevent="loginUser">
      <div>
        <label for="email">Email:</label>
        <input type="text" id="email" v-model="email" required />
      </div>

      <div>
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required />
      </div>

      <button type="submit">Accedi</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
import { store } from '../store.js';

export default {
  data() {
    return {
      email: '',
      password: '',
    };
  },
  methods: {
    async loginUser() {
      try {
        // invia una richiesta POST all'endpoint di login
        const response = await axios.post(
          'http://localhost:8001/api/login',
          {
            email: this.email,
            password: this.password,
          },
          // questo mi serve per poter mandare cookie da BE a FE su domini diversi,(credo?)
          // va inserito come terzo argomento della chiamata axios
          {
            withCredentials: true,
          }
        );

        // gestione della risposta positiva (popolazione dello store)
        if (response.data.success) {
          console.log('Login effettuato con successo:', response.data);
          store.isLoggedIn = true;
          store.token = response.data.token;
          store.user = response.data.user;
        } else {
          console.error('Errore durante il login:', response.data.message);
          // qua si può mostrare un messaggio di errore invece di un console log
        }
      } catch (error) {
        console.error('Errore API:', error);
        // gestisci gli errori di rete o altri errori non legati alla risposta
      }
    },
  },
};
</script>

<style scoped></style>

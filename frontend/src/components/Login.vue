<template>
  <div class="login-container">
    <div class="form-container">
      <h2>Login</h2>

      <form @submit.prevent="loginUser">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="text" id="email" v-model="email" required />
        </div>

        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" v-model="password" required />
        </div>

        <div class="button-wrapper">
          <button type="submit">Accedi</button>
        </div>

        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
      </form>
    </div>
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
      errorMessage: '',
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
        console.log('Login effettuato con successo:', response.data);
        store.isLoggedIn = true;
        store.token = response.data.token;
        store.user = response.data.user;
        localStorage.setItem('token', response.data.token);
        this.$router.push('/');
      } catch (error) {
        console.error('Errore API:', error);
        // Qui stai gestendo l'errore:
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = 'Errore durante il login. Riprova più tardi.';
        }
      }
    },
  },
};
</script>

<style scoped></style>

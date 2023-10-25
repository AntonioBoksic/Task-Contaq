<template>
  <div class="register-container">
    <h2>Registrazione</h2>

    <form @submit.prevent="registerUser">
      <div>
        <label for="email">Email:</label>
        <input type="text" id="email" v-model="email" required />
      </div>

      <div>
        <label for="name">Nome e cognome:</label>
        <input type="text" id="name" v-model="name" required />
      </div>

      <div>
        <label for="role">Ruolo:</label>
        <select id="role" v-model="role" required>
          <option value="">Seleziona un ruolo</option>
          <option value="operator">Operatore</option>
          <option value="technician">Tecnico</option>
        </select>
      </div>

      <div>
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required />
      </div>

      <div>
        <label for="password_confirmation">Conferma password:</label>
        <input
          type="password"
          id="password_confirmation"
          v-model="password_confirmation"
          required />
      </div>

      <button type="submit">Registrati</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      name: '',
      email: '',
      role: '',
      password: '',
      password_confirmation: '',
    };
  },
  methods: {
    async registerUser() {
      try {
        const response = await axios.post(
          'http://localhost:8001/api/register',
          {
            name: this.name,
            email: this.email,
            role: this.role,
            password: this.password,
            password_confirmation: this.password_confirmation,
          }
        );

        if (response.data.success) {
          // gestisci il successo
          console.log('Utente registrato con successo:', response.data);
          //reindirizza su pagina di login
          this.$router.push('/login');
        } else {
          // gestisci l'errore
          console.error(
            'Errore durante la registrazione:',
            response.data.message
          );
          // mostra un messaggio di errore all'utente
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

<template>
  <div v-if="store.isLoggedIn">
    <h2>
      Benvenuto <strong>{{ store.user.name }}</strong>
    </h2>
    <h3>Qui puoi vedere i ticket</h3>

    <div class="tickets-container">
      <table class="tickets-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Created at</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in tickets" :key="ticket.id">
            <td>{{ ticket.id }}</td>
            <td>{{ ticket.title }}</td>
            <td>{{ ticket.category_id }}</td>
            <td>{{ formatDateTime(ticket.created_at) }}</td>
            <td>{{ ticket.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { store } from '../store.js';

export default {
  name: 'HomePage',
  data() {
    return {
      store,
      tickets: [], // Aggiunto una proprietà per i ticket
    };
  },
  methods: {
    formatDateTime(dateTimeString) {
      const dateObj = new Date(dateTimeString);
      const year = dateObj.getFullYear();
      const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // +1 perché i mesi iniziano da 0
      const day = String(dateObj.getDate()).padStart(2, '0');
      const hours = String(dateObj.getHours()).padStart(2, '0');
      const minutes = String(dateObj.getMinutes()).padStart(2, '0');

      return `${day}/${month}/${year} ${hours}:${minutes}`;
    },
  },
  async mounted() {
    console.log('partito il mounted');
    try {
      const token = store.token;
      const config = {
        headers: {
          Authorization: 'Bearer ' + token,
        },
      };

      const response = await axios.get(
        'http://localhost:8001/api/tickets',
        config
      );
      this.tickets = response.data; // Assumendo che la risposta contenga direttamente l'array dei ticket
    } catch (error) {
      console.error('Errore durante il recupero dei ticket:', error);
    }
  },
};
</script>

<style scoped>
h2,
h3 {
  text-align: center;
  padding-top: 20px;
  padding-bottom: 20px;
}

.tickets-container {
  margin: 0 auto;
  border: white 1px solid;
  overflow: auto;
  display: flex;
  flex-direction: column;
  padding: 20px;
}

.tickets-table {
  text-align: center;
}
</style>

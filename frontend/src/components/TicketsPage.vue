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

    <div class="create-ticket">
      <h4>Crea un nuovo ticket</h4>

      <form @submit.prevent="createTicket">
        <div>
          <label for="title">Title:</label>
          <input v-model="newTicket.title" type="text" id="title" required />
        </div>

        <div>
          <label for="category">Categoria:</label>
          <input
            v-model="newTicket.category_id"
            type="number"
            id="category"
            required />
        </div>

        <div>
          <button type="submit">Crea Ticket</button>
        </div>
      </form>
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
      newTicket: {
        title: '',
        category_id: '',
      },
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
    async createTicket() {
      try {
        const token = store.token;
        const config = {
          headers: {
            Authorization: 'Bearer ' + token,
          },
        };

        const response = await axios.post(
          'http://localhost:8001/api/tickets',
          this.newTicket,
          config
        );

        // aggiungi il nuovo ticket all'elenco
        this.tickets.push(response.data.ticket);

        // resetta il form
        this.newTicket.title = '';
        this.newTicket.category_id = '';

        alert('Ticket creato con successo!');
      } catch (error) {
        console.error('Errore durante la creazione del ticket:', error);
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          alert(error.response.data.message);
        } else {
          alert('Si è verificato un errore nella creazione del ticket.');
        }
      }
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

<template>
  <div v-if="store.isLoggedIn">
    <h2>
      Benvenuto <strong>{{ store.user.name }}</strong>
    </h2>
    <h3>
      Qui puoi vedere i messaggi relativi al tuo ticket con ID: {{ ticketId }}
    </h3>

    <div class="messages-container">
      <ul>
        <li v-for="message in messages" :key="message.id">
          <strong>{{ formatDateTime(message.created_at) }}</strong> --
          {{ message.content }}
        </li>
      </ul>
    </div>

    <div class="create-ticket">
      <h4>Crea un nuovo messaggio</h4>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { store } from '../store.js';

export default {
  name: 'MessagesPage',
  props: ['ticketId'],
  data() {
    return {
      store,
      messages: [],
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
    try {
      const token = store.token;
      const config = {
        headers: {
          Authorization: 'Bearer ' + token,
        },
      };
      const response = await axios.get(
        `http://localhost:8001/api/tickets/${this.ticketId}/messages`,
        config
      );
      this.messages = response.data.messages;
    } catch (error) {
      console.error('Errore nel recupero dei messaggi:', error);
      alert('Errore nel recupero dei messaggi.');
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

.messages-container {
  margin: 0 auto;
  border: white 1px solid;
  overflow: auto;
  display: flex;
  flex-direction: column;
  padding: 20px;
  max-height: 50vh;
}
</style>

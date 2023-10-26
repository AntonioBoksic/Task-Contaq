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
        <li
          v-for="message in messages"
          :key="message.id"
          :class="getMessageClass(message)">
          <strong>{{ formatDateTime(message.created_at) }}</strong>
          <span
            v-if="
              message.user.role === 'technician' ||
              store.user.role === 'technician'
            ">
            ({{ message.user.name }}, id: {{ message.user.id }})
          </span>

          <!-- contenuto del messaggio -->
          <div>
            {{ message.content }}
          </div>
        </li>
      </ul>
    </div>

    <div class="create-message">
      <h4>Crea un nuovo messaggio</h4>
      <div class="form-group">
        <textarea
          v-model="newMessageContent"
          placeholder="Scrivi il tuo messaggio..."></textarea>
      </div>
      <button @click="createMessage">Invia</button>
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
      messages: [], //su questo faccio il vfor e lo popolo con la index
      newMessageContent: '', // per memorizzare il contenuto del nuovo messaggio
    };
  },
  //   il watch fa partire la chiamata quando app.vue viene inizializzata (ovvero recupera i dati dell'utente con la chiamata che ha in created) modificando il valore di store,isAppInitialized
  watch: {
    'store.isAppInitialized'(newValue) {
      if (newValue) {
        this.fetchMessages();
      }
    },
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
    // getMessageClass(message) determina la classe da applicare al messaggio
    getMessageClass(message) {
      // ------SE UTENTE LOGGATO è UN TECNICO------
      if (this.store.user.role === 'technician') {
        if (this.messageIsFromTechnician(message)) {
          if (message.user_id === this.store.user.id) {
            return 'message-self'; // messaggio inviato dall'utente corrente (technician)
          }
          return 'message-left'; // messaggio inviato da un altro technician
        }
        return 'message-right'; // messaggio inviato da un non-technician
      } else {
        // ------SE UTENTE LOGGATO NON è UN TECNICO------
        return this.messageIsFromTechnician(message)
          ? 'message-right'
          : 'message-left';
      }
    },
    // messageIsFromTechnician(message) restituisce true se messaggio è mandato da un tecnico
    messageIsFromTechnician(message) {
      return message.user.role === 'technician';
    },
    async createMessage() {
      try {
        const token = store.token;
        const config = {
          headers: {
            Authorization: 'Bearer ' + token,
          },
        };

        const response = await axios.post(
          `http://localhost:8001/api/tickets/${this.ticketId}/messages`,
          { content: this.newMessageContent },
          config
        );

        if (response.status === 201) {
          this.messages.push(response.data.data); // Aggiungi il nuovo messaggio alla lista
          this.newMessageContent = ''; // Pulisci il campo del messaggio
          alert(response.data.message); // Mostra un messaggio di conferma
        }
      } catch (error) {
        console.error('Errore nella creazione del messaggio:', error);
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          alert(error.response.data.message); // ,ostra un messaggio di errore dal backend
        } else {
          alert('Errore nella creazione del messaggio.');
        }
      }
    },
    async fetchMessages() {
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
  },
  // il created parte in caso store.isAppInitialized sia già true nel momento in cui viene eseguito, questo mi serve per evitare che store.isAppInitialized diventi true prima che il watch possa accorgersene (99% delle volte sarà il watch a far partire la chiamata e non il created ma teniamolo comunque)
  created() {
    if (store.isAppInitialized) {
      this.fetchMessages();
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

.message-left {
  text-align: left;
  background-color: #f1f1f1; /* Un colore di sfondo per evidenziare i tuoi messaggi */
  margin-right: 50.5%; /* Per ridurre la larghezza del messaggio e spingerlo a sinistra */
  padding: 10px;
  border-radius: 5px;
  color: black;
}

.message-right {
  text-align: left;
  background-color: #f1f1f1; /* Un colore di sfondo per evidenziare i messaggi degli altri */
  margin-left: 50.5%; /* Per ridurre la larghezza del messaggio e spingerlo a destra */
  padding: 10px;
  border-radius: 5px;
  color: black;
}

.message-left,
.message-right {
  margin-top: 3px;
  margin-bottom: 3px;
}

/* questo vale solo per il tecnico loggato, vede il suo messaggio leggermente diverso da quelli degli altri tecnici che comunque sono messi sulla sinistra dello schermo */
.message-self {
  text-align: left;
  background-color: #b8b8b8;
  margin-right: 50.5%;
  padding: 10px;
  border-radius: 5px;
  color: black;
  border: 3px solid #007bff; /* un bordo blu per distinguere i tuoi messaggi */
}

ul {
  list-style-type: none;
  padding-left: 0;
}
</style>

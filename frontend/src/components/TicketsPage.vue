<template>
  <div v-if="store.isLoggedIn">
    <h2>
      Benvenuto <strong>{{ store.user.name }}</strong>
    </h2>
    <h3>Qui puoi vedere i ticket</h3>

    <!-- container ticket salvati in database o creati  in frontend-->
    <div class="tickets-container">
      <table class="tickets-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Created at</th>
            <th>Status</th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in tickets" :key="ticket.id">
            <!-- id -->
            <td>{{ ticket.id }}</td>

            <!-- titolo -->
            <td v-if="!ticket.isEditing">{{ ticket.title }}</td>
            <td v-else>
              <input v-model="ticket.title" type="text" />
            </td>

            <!-- category_id (poi andrà cambiato nel nome della categoria) -->
            <td v-if="!ticket.isEditing">{{ ticket.category.name }}</td>
            <td v-else>
              <select v-model="ticket.category_id">
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </td>

            <!-- created at -->
            <td>
              {{ formatDateTime(ticket.created_at) }}
            </td>

            <!-- status -->
            <td v-if="!ticket.isEditing">{{ ticket.status }}</td>
            <td v-else>
              <select v-model="ticket.status">
                <option
                  v-for="status in statuses"
                  :key="status.value"
                  :value="status.value">
                  {{ status.label }}
                </option>
              </select>
            </td>

            <!-- bottoni -->
            <!-- se viene premuto modifica cambiano i bottoni nella riga in cui è stato cliccato modifica -->
            <td>
              <button v-if="!ticket.isEditing" @click="deleteTicket(ticket.id)">
                Elimina
              </button>
              <button v-if="!ticket.isEditing" @click="startEditing(ticket)">
                Modifica
              </button>
              <button v-if="!ticket.isEditing" @click="viewMessages(ticket.id)">
                Messaggi
              </button>
              <!-- da qui ci sono i bottoni renderizzati dopo il click di "modifica" -->
              <button v-if="ticket.isEditing" @click="confirmEdit(ticket)">
                Conferma
              </button>
              <button v-if="ticket.isEditing" @click="cancelEdit(ticket)">
                Annulla
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- creazione nuovo ticket -->
    <div class="create-ticket">
      <h4>Crea un nuovo ticket</h4>

      <form @submit.prevent="createTicket">
        <div class="form-group">
          <label for="title">Title:</label>
          <input v-model="newTicket.title" type="text" id="title" required />
        </div>

        <div class="form-group">
          <label for="category">Categoria:</label>
          <select v-model="newTicket.category_id" id="category" required>
            <option
              v-for="category in categories"
              :key="category.id"
              :value="category.id">
              {{ category.name }}
            </option>
          </select>
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
      tickets: [], // questi sono i dati che ottengo dopo il created quando chiamo la index a cui poi aggiungo,elimino o modifico nel frontend per non dover rieseguire la index dopo ogni operazione di crud
      preEditTicket: {}, // qui salvo tickets quando premo modifica, in modo che se cambio i campi e poi premo annulla posso riportare il valore del campo modificato al valore pre-modifca
      newTicket: {
        title: '',
        category_id: '',
      },
      categories: [], //anche questi li ottengo dal created e mi servono per mostrare opzioni possibili in fase di creazione all utente
      // statuses mi serve per gestire la select di status durante la modifica
      statuses: [
        { label: 'Aperto', value: 'aperto' },
        { label: 'In Lavorazione', value: 'in lavorazione' },
        { label: 'Chiuso', value: 'chiuso' },
      ],
    };
  },
  //   il watch fa partire la chiamata quando app.vue viene inizializzata (ovvero recupera i dati dell'utente con la chiamata che ha in created) modificando il valore di store,isAppInitialized
  watch: {
    'store.isAppInitialized'(newValue) {
      if (newValue) {
        this.fetchTicketsAndCategories();
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
    async deleteTicket(ticketId) {
      try {
        const token = store.token;
        const config = {
          headers: {
            Authorization: 'Bearer ' + token,
          },
        };

        const response = await axios.delete(
          `http://localhost:8001/api/tickets/${ticketId}`,
          config
        );

        console.log(response);

        if (response.status === 200) {
          // rimuovi il ticket dall'array dei ticket nel frontend
          const index = this.tickets.findIndex(
            ticket => ticket.id === ticketId
          );
          if (index !== -1) {
            this.tickets.splice(index, 1);
          }
          alert('Ticket eliminato con successo!');
        } else {
          alert("Errore durante l'eliminazione del ticket.");
        }
      } catch (error) {
        console.error("Errore durante l'eliminazione del ticket:", error);
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          alert(error.response.data.message);
        } else {
          alert("Si è verificato un errore durante l'eliminazione del ticket.");
        }
      }
    },
    //questa la attivo sul click "modifica" che poi mi fa vedere campi input modificabili e cambi i bottoni in "conferma" e "annulla"
    startEditing(ticket) {
      ticket.isEditing = true;
      // qua non posso fare this.preEditTicket = ticket ; perchè mi prende il riferimento e non il contenuto;
      this.preEditTicket = JSON.parse(JSON.stringify(ticket));
    },
    // questa la attivo quando clicco su "conferma" dopo aver cliccato "modifica" e mi cambia effettivamente i dati in database
    async confirmEdit(ticket) {
      try {
        const token = store.token;
        const config = {
          headers: {
            Authorization: 'Bearer ' + token,
          },
        };

        const response = await axios.put(
          `http://localhost:8001/api/tickets/${ticket.id}`,
          ticket,
          config
        );

        if (response.status === 200) {
          const index = this.tickets.findIndex(t => t.id === ticket.id);
          if (index !== -1) {
            this.tickets[index] = response.data.ticket; // aggiorna con i dati dal server
            this.tickets[index].isEditing = false; // esci dalla modalità di modifica
          }
          alert('Ticket aggiornato con successo!');
        } else {
          alert("Errore durante l'aggiornamento del ticket.");
        }
      } catch (error) {
        console.error("Errore durante l'aggiornamento del ticket:", error);
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          alert(error.response.data.message);
        } else {
          alert(
            "Si è verificato un errore durante l'aggiornamento del ticket."
          );
        }
      }
    },
    // questa la attivo sul click "annulla" e mi annulla le modifiche fatte in caso abbia inserito qualcosa nei campi input ma non voglio procedere con la modifica
    //qui è troviamo il ticket corrente nell'array tickets e sostituiamo l'oggetto con la versione "pre-modifica" salvata in preEditTicket
    cancelEdit(ticket) {
      const index = this.tickets.findIndex(t => t.id === ticket.id);
      if (index !== -1) {
        this.tickets[index] = JSON.parse(JSON.stringify(this.preEditTicket));
        this.tickets[index].isEditing = false;
      }
    },
    viewMessages(ticketId) {
      this.$router.push({
        name: 'MessagesPage',
        params: { ticketId: ticketId },
      });
    },
    async fetchTicketsAndCategories() {
      console.log('partito il fetchTickets');
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

        //   invece di salvare i ticket come mi tornano dal backend:
        //   this.tickets = response.data.tickets;
        // gli aggiungo una proprietà isEditing = false; questo mi servirà per la modifica del ticket, per far apparire campi di input al posto di valori e cambiare i bottoni della riga

        this.tickets = response.data.tickets.map(ticket => ({
          ...ticket,
          isEditing: false,
        }));
        this.categories = response.data.categories;
      } catch (error) {
        console.error('Errore durante il recupero dei ticket:', error);
      }
    },
  },
  //   il created parte in caso store.isAppInitialized sia già true nel momento in cui viene eseguito, questo mi serve per evitare che store.isAppInitialized diventi true prima che il watch possa accorgersene (99% delle volte sarà il watch a far partire la chiamata e non il created ma teniamolo comunque)
  created() {
    if (store.isAppInitialized) {
      this.fetchTicketsAndCategories();
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
  max-height: 50vh;
}

.tickets-table {
  text-align: center;
}

.tickets-table input {
  width: 100%; /* occupa l'intero spazio disponibile della cella */
  box-sizing: border-box;
  padding: 4px;
  max-width: 150px;
}

.tickets-table td {
  white-space: nowrap; /* questo è fondamentale per non far diventare il campo input più largo di come era la sua colonna prima di diventare un input */
}
</style>

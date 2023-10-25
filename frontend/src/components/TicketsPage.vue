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
            <td v-if="!ticket.isEditing">{{ ticket.category_id }}</td>
            <td v-else>
              <input v-model="ticket.category_id" type="text" />
            </td>

            <!-- created at -->
            <td>
              {{ formatDateTime(ticket.created_at) }}
            </td>

            <!-- status -->
            <td v-if="!ticket.isEditing">{{ ticket.status }}</td>
            <td v-else>
              <input v-model="ticket.status" type="text" />
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
      tickets: [], // questi sono i dati che ottengo dopo il mounted quando chiamo la index a cui poi aggiungo,elimino o modifico nel frontend per non dover rieseguire la index dopo ogni operazione di crud
      preEditTicket: {}, // qui salvo tickets quando premo modifica, in modo che se cambio i campi e poi premo annulla posso riportare il valore del campo modificato al valore pre-modifca
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
      //   invece di salvare i ticket come mi tornano dal backend:
      //   this.tickets = response.data;
      // gli aggiungo una proprietà isEditing = false; questo mi servirà per la modifica del ticket
      this.tickets = response.data.map(ticket => ({
        ...ticket,
        isEditing: false,
      }));
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

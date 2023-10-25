import { reactive } from 'vue';

export const store = reactive({
  user: null,        // questo conterrà le informazioni dell'utente
  token: null,       // questo conterrà il token dell'utente loggato
  isLoggedIn: false, // questo ci dice se utente è loggato o meno
  isAppInitialized: false,  // indica se l'applicazione è stata inizializzata / questo viene impostato a true ogni volta che refresho la pagina e ottiene i dati dell'utente indietro dal server
});


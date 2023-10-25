import { reactive } from 'vue';

export const store = reactive({
  user: null, // questo conterrà le informazioni dell'utente
  token: null, // questo conterrà il token dell'utente loggato
  isLoggedIn: false, // questo ci dice se utente è oggato o meno
});

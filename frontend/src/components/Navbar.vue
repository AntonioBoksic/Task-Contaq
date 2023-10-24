<template>
  <nav>
    <!-- Se non loggato, mostra Registrati e Log in -->
    <div v-if="!isLoggedIn">
      <a @click="goToRegister">Registrati</a>
      <a @click="goToLogin">Log in</a>
    </div>

    <!-- Se loggato, mostra il nome utente e il menu a tendina -->
    <div v-else>
      <span>{{ username }}</span>
      <div>
        <!-- Qui potresti avere ulteriori opzioni nel menù a tendina, ad es. logout -->
        <button @click="logout">Logout</button>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Navbar',
  data() {
    return {
      // Presumibilmente, avrai bisogno di questi dati, che potrebbero provenire da uno stato globale o da un cookie/sessione.
      isLoggedIn: false, // Ad esempio, potresti controllare se esiste un token nell'localStorage
      username: 'Nome Utente', // Questo dovrebbe provenire da una fonte autentica, come uno stato globale o un cookie/sessione.
    };
  },
  methods: {
    goToRegister() {
      // Naviga alla pagina di registrazione
      this.$router.push('/register');
    },
    goToLogin() {
      // Naviga alla pagina di login
      this.$router.push('/login');
    },
    logout() {
      // Esegui il logout: potresti voler cancellare i token, aggiornare lo stato globale, ecc.
      this.isLoggedIn = false;
      this.username = '';
      this.$router.push('/login');
    },
  },
};
</script>

<style scoped>
nav {
  display: flex;
  justify-content: end;
  align-items: center;
  width: 100%;
  border: 1px solid white;
  height: 5vh;
}

nav a {
  padding: 5px;
  cursor: pointer;
}
</style>

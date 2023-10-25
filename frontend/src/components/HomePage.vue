<template>
  <div v-if="store.isLoggedIn">
    <h2>
      benvenuto in homepage <strong>{{ store.user.name }}</strong>
    </h2>
  </div>

  <div v-else>
    <h2>Per favore esegui il login per poter accedere al ticket system</h2>
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
    };
  },
};
</script>

<style scoped>
h2 {
  text-align: center;
  padding-top: 20px;
}
</style>

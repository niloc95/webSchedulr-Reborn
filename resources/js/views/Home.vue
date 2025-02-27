<template>
  <div class="home">
      <h1>Welcome to WebSchedulr</h1>
      <p>Your modern scheduling solution</p>
      <div class="actions" v-if="!isAuthenticated">
          <router-link to="/login" class="btn btn-primary">Login</router-link>
          <router-link to="/register" class="btn btn-secondary">Register</router-link>
      </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isAuthenticated = ref(false);

onMounted(() => {
  isAuthenticated.value = !!localStorage.getItem('token');
});
</script>

<style scoped>
.home {
  padding: 2rem;
  text-align: center;
}

.actions {
  margin-top: 2rem;
}

.btn {
  margin: 0 0.5rem;
  padding: 0.5rem 1rem;
  text-decoration: none;
  border-radius: 4px;
}

.btn-primary {
  background: #42b983;
  color: white;
}

.btn-secondary {
  background: #2c3e50;
  color: white;
}
</style>
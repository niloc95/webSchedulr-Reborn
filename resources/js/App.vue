<template>
  <div class="app">
      <nav class="navbar" v-if="showNav">
          <div class="navbar-brand">
              <router-link to="/" class="brand">WebSchedulr</router-link>
          </div>
          <div class="navbar-menu">
              <router-link to="/calendar">Calendar</router-link>
              <a href="#" @click.prevent="logout" v-if="isAuthenticated">Logout</a>
              <router-link to="/login" v-else>Login</router-link>
          </div>
      </nav>
      <main>
          <router-view></router-view>
      </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const isAuthenticated = ref(false);

const showNav = computed(() => {
  return !['login', 'register'].includes(route.name);
});

onMounted(() => {
  isAuthenticated.value = !!localStorage.getItem('token');
});

const logout = () => {
  localStorage.removeItem('token');
  isAuthenticated.value = false;
  router.push('/login');
};
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.app {
  min-height: 100vh;
  font-family: Arial, sans-serif;
}

.navbar {
  padding: 1rem 2rem;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar-brand .brand {
  font-size: 1.5rem;
  font-weight: bold;
  color: #42b983;
  text-decoration: none;
}

.navbar-menu a {
  margin-left: 1rem;
  color: #2c3e50;
  text-decoration: none;
}

.navbar-menu a:hover {
  color: #42b983;
}

main {
  padding: 1rem;
}
</style>
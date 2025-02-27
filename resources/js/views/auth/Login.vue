<template>
  <div class="login-form">
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
          <div class="form-group">
              <label for="email">Email</label>
              <input 
                  type="email" 
                  id="email" 
                  v-model="email" 
                  required
                  autocomplete="email"
              >
          </div>
          <div class="form-group">
              <label for="password">Password</label>
              <input 
                  type="password" 
                  id="password" 
                  v-model="password" 
                  required
                  autocomplete="current-password"
              >
          </div>
          <button type="submit" class="btn-submit">Login</button>
      </form>
      <p class="register-link">
          Don't have an account? 
          <router-link to="/register">Register</router-link>
      </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const email = ref('');
const password = ref('');

const handleLogin = async () => {
  try {
      // TODO: Implement login logic
      console.log('Login attempt:', { email: email.value, password: password.value });
      router.push('/calendar');
  } catch (error) {
      console.error('Login failed:', error);
  }
};
</script>

<style scoped>
.login-form {
  max-width: 400px;
  margin: 2rem auto;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}

.form-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn-submit {
  width: 100%;
  padding: 0.75rem;
  background: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.register-link {
  text-align: center;
  margin-top: 1rem;
}
</style>
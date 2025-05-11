<template>
  <div class="auth-container">
    <section class="form-box">
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
        <input type="text" v-model="loginData.username" placeholder="Nombre de usuario" required />
        <input type="password" v-model="loginData.password" placeholder="Contraseña" required /><br>
        <button type="submit" :disabled="loginLoading">
          <span v-if="loginLoading">Iniciando...</span>
          <span v-else>Iniciar Sesión</span>
        </button>
        <p v-if="loginError" class="error">{{ loginError }}</p>
      </form><br>
      <router-link to="/registro" class="enlace">¿No tienes cuenta? Regístrate</router-link>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import api from '../axios';
import { useRouter } from 'vue-router';
import { userStore } from '../stores/authStore';

const router = useRouter();
const loginData = ref({ username: '', password: '' });
const loginError = ref('');
const loginLoading = ref(false);

const handleLogin = async () => {
  loginError.value = '';
  loginLoading.value = true;
  try {
    await api.post('/api/login', loginData.value);
    const res = await api.get('/api/me', { withCredentials: true });
    userStore.value = res.data;
    router.push('/perfil');
  } catch (err: any) {
    loginError.value = err.response?.data?.error || 'Error al iniciar sesión.';
  } finally {
    loginLoading.value = false;
  }
};
</script>


<style scoped>
body{
  font-family: Lexend;
}

.auth-container {
  min-height: 75vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-box {
  width: 100%;
  max-width: 400px;
  min-width: 300px;
  padding: 24px 32px;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.08);
}

input {
  display: block;
  width: 97.5%;
  padding: 8px;
  margin: 10px 0;
  font-size: 1rem;
  font-family: Lexend;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #3b3b3b;
  color: white;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  font-family: Lexend;
}

button:disabled {
  background-color: #888;
  cursor: not-allowed;
}

button:hover:not(:disabled) {
  background-color: #2b2b2b;
}

.error {
  color: red;
  margin-top: 10px;
}

.enlace {
  color: #2381e0;
  text-decoration: underline;
  font-weight: bold;
  transition: color 0.2s;
}

.enlace:hover {
  color: #4882da;
}
</style>

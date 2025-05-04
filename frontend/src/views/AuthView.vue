<template>
  <div class="auth-container">
    <section class="form-box">
      <h2>Registro</h2>
      <form @submit.prevent="handleRegister">
        <input type="text" v-model="registerData.username" placeholder="Nombre de usuario" required />
        <input type="email" v-model="registerData.email" placeholder="Correo electrónico" required />
        <input type="password" v-model="registerData.password" placeholder="Contraseña" required />
        
        <button type="submit" :disabled="registerLoading">
          <span v-if="registerLoading">Registrando...</span>
          <span v-else>Registrarse</span>
        </button>
        
        <p v-if="registerError" class="error">{{ registerError }}</p>
      </form>
    </section>

    <section class="form-box">
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
        <input type="text" v-model="loginData.username" placeholder="Nombre de usuario" required />
        <input type="password" v-model="loginData.password" placeholder="Contraseña" required />

        <button type="submit" :disabled="loginLoading">
          <span v-if="loginLoading">Iniciando...</span>
          <span v-else>Iniciar Sesión</span>
        </button>
        
        <p v-if="loginError" class="error">{{ loginError }}</p>
      </form>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import api from '../axios';
import { useRouter } from 'vue-router';
import { username } from '../stores/authStore';
import { useToast } from 'vue-toastification';

const toast = useToast();

const router = useRouter();

const registerData = ref({ username: '', email: '', password: '' });
const loginData = ref({ username: '', password: '' });

const registerError = ref('');
const loginError = ref('');

const registerLoading = ref(false);
const loginLoading = ref(false);

const handleRegister = async () => {
  registerError.value = '';
  registerLoading.value = true;
  try {
    await api.post('/api/register', registerData.value);
    toast.success('Usuario registrado correctamente 😀');
  } catch (err: any) {
    registerError.value = err.response?.data?.error || 'Error en el registro.';
  } finally {
    registerLoading.value = false;
  }
};

const handleLogin = async () => {
  loginError.value = '';
  loginLoading.value = true;
  try {
    await api.post('/api/login', loginData.value);
    const res = await api.get('/api/me', { withCredentials: true });
    username.value = res.data.username;
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
  display: flex;
  justify-content: space-around;
  padding: 40px;
  flex-wrap: wrap;
}

.form-box {
  width: 45%;
  min-width: 300px;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
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
</style>

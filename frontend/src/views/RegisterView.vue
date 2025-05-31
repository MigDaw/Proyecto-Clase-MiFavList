<template>
    <div class="auth-container">
        <section class="form-box">
            <h2>Registro</h2>
            <form @submit.prevent="handleRegister">
            <input type="text" v-model="registerData.username" placeholder="Nombre de usuario" required />
            <input type="email" v-model="registerData.email" placeholder="Correo electrónico" required />
            <input type="password" v-model="registerData.password" placeholder="Contraseña" required /><br>
            <button type="submit" :disabled="registerLoading">
                <span v-if="registerLoading">Registrando...</span>
                <span v-else>Registrarse</span>
            </button>
            <p v-if="registerError" class="enlace">{{ registerError }}</p>
            </form><br>
            <router-link to="/" class="enlace">¿Ya tienes cuenta? Inicia sesión</router-link>
        </section>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import api from '../axios';
import { useToast } from 'vue-toastification';
import router from '../router';

const toast = useToast();
const registerData = ref({ username: '', email: '', password: '' });
const registerError = ref('');
const registerLoading = ref(false);

function validarEmail(email: string) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Solo letras, números, sin espacios, 1-20 caracteres
function validarUsername(username: string) {
  return /^[A-Za-z0-9]{1,20}$/.test(username);
}

const handleRegister = async () => {
  registerError.value = '';
  if (!validarUsername(registerData.value.username)) {
    registerError.value = 'El nombre de usuario solo puede contener letras y números, sin espacios, y máximo 20 caracteres.';
    toast.info('El nombre de usuario solo puede contener letras y números, sin espacios, y máximo 20 caracteres.');
    return;
  }
  if (!validarEmail(registerData.value.email)) {
    registerError.value = 'Introduce un correo electrónico válido.';
    toast.info('Introduce un correo electrónico válido.');
    return;
  }
  registerLoading.value = true;
  try {
    await api.post('/api/register', registerData.value);
    toast.success('Usuario registrado correctamente 😀');
    router.push('/');//redirijo al login
  } catch (err: any) {
    registerError.value = err.response?.data?.error || 'Error en el registro.';
  } finally {
    registerLoading.value = false;
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
  border: 2px solid #ccc;
  border-radius: 10px;
  box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
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
  color: #5994ec;
}
</style>
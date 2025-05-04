<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '../axios';
import { useRouter } from 'vue-router';
import { username } from '../stores/authStore';

const menuOpen = ref(false);
const router = useRouter();

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
};

const logout = async () => {
  await api.post('/api/logout', {}, { withCredentials: true });
  username.value = null;
  router.push('/');
};

onMounted(async () => {
  try {
    const response = await api.get('/api/me', {
      withCredentials: true,
    });
    username.value = response.data.username;
  } catch {
    username.value = null;
  }
});
</script>

<template>
  <header>
    <img src="../assets/Logo 1.png" alt="logo" />

    <div v-if="username" class="user-wrapper">
      <div class="user-display" @click="toggleMenu">
        {{ username }}
        <span class="chevron">{{ menuOpen ? '▲' : '▼' }}</span>
      </div>

      <div v-if="menuOpen" class="dropdown">
        <router-link to="/perfil">Ver perfil</router-link>
        <a @click="logout">Cerrar sesión</a>
      </div>
    </div>
  </header>
</template>

<style scoped>
header {
  background-color: rgb(61, 58, 58);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
}

img {
  width: 150px;
}

.user-wrapper {
  position: relative;
}

.user-display {
  background-color: white;
  color: rgb(61, 58, 58);
  padding: 8px 12px;
  border-radius: 20px;
  cursor: pointer;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 6px;
}

.chevron {
  font-size: 0.8rem;
}

.dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 8px;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  min-width: 150px;
  z-index: 10;
}

.dropdown a,
.dropdown router-link {
  padding: 10px;
  text-align: left;
  color: rgb(61, 58, 58);
  text-decoration: none;
  border-bottom: 1px solid #eee;
  transition: background-color 0.2s;
  cursor: pointer;
}

.dropdown a:last-child,
.dropdown router-link:last-child {
  border-bottom: none;
}

.dropdown a:hover,
.dropdown router-link:hover {
  background-color: #f0f0f0;
}
</style>

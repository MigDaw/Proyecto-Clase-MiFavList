<template>
  <div class="users-list-container">
    <ul class="users-list">
      <li v-for="user in users" :key="user.id" class="user-item">
        <img :src="user?.profilePic ? `http://localhost:8080${user.profilePic}` 
        : imagenGenerica" alt="Foto de perfil" class="profile-pic" />
        <span class="username">{{ user.username }}</span>
        <span class="contenido-count">Contenido: {{ user.contenidoCount }}</span>
        <span class="perfil-status" :class="{ publico: user.isPublic, privado: !user.isPublic }">
          {{ user.isPublic ? 'público' : 'privado' }}
        </span>
        <button 
          :disabled="!user.isPublic"
          @click="goToProfile(user.id)"
          class="ver-perfil-btn"
        >
          Ver perfil
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import imagenGenerica from '../assets/Imagen-generica.png'
const props = defineProps({ users: Array });
const router = useRouter();

function goToProfile(userId) {
  router.push({ name: 'perfil-usuario', params: { id: userId } });
}
</script>

<style scoped>
.users-list-container {
  max-width: 1000px;
  margin: 2rem auto;
  padding: 1.5rem 1rem;
  background: #565256;
  border-radius: 14px;
  box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
}

.users-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.user-item {
  display: grid;
  grid-template-columns: 44px minmax(0, 1fr) 110px 90px 100px;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.7rem;
  padding: 0.5rem 0.7rem;
  border-radius: 8px;
  background: #272424;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.profile-pic {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  background: #e0e0e0;
}

.username {
  font-weight: bold;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 0;
  color: #fff;
}

.contenido-count {
  color: #e0e0e0;
  font-size: 0.95em;
  text-align: center;
  white-space: nowrap;
}

.perfil-status {
  padding: 0.2em 0.7em;
  border-radius: 12px;
  font-size: 0.9em;
  text-align: center;
  white-space: nowrap;
}
.perfil-status.publico {
  background: #e0ffe0;
  color: #218838;
}
.perfil-status.privado {
  background: #ffe0e0;
  color: #c82333;
}

.ver-perfil-btn {
  font-family: Lexend;
  padding: 0.4em 0.8em;
  border: none;
  border-radius: 6px;
  background-color: #3b3b3b;
  color: white;
  cursor: pointer;
  transition: background 0.2s;
  font-size: 0.97em;
}
.ver-perfil-btn:disabled {
  background: #cccccc;
  cursor: not-allowed;
}

.ver-perfil-btn:hover:not(:disabled) {
  background-color: #5c5c5c;
}

/* Responsive */
@media (max-width: 600px) {
  .users-list-container {
    max-width: 98vw;
    padding: 0.5rem 0.2rem;
  }
  .user-item {
    grid-template-columns: 34px minmax(0, 1fr) 70px 60px 70px;
    gap: 0.4rem;
    font-size: 0.95em;
  }
  .profile-pic {
    width: 30px;
    height: 30px;
  }
}
</style>
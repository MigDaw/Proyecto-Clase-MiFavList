<template>
    <div class="profile">
      <div class="profile-card">
        <img :src="profilePicture" alt="Foto de perfil" />
        <h2>{{ userStore?.username }}</h2>
        <p>{{ userStore?.email }}</p>
        <p><strong>Contenido subido:</strong> {{ contentCount }}</p>
  
        <div class="toggles">
          <div class="toggle">
            <span>Perfil público</span>
            <button :class="{'on': isProfilePublic, 'off': !isProfilePublic}" @click="toggleProfile">
              {{ isProfilePublic ? 'Sí' : 'No' }}
            </button>
          </div>
  
          <div class="toggle">
            <span>Chat público</span>
            <button :class="{'on': isChatPublic, 'off': !isChatPublic}" @click="toggleChat">
              {{ isChatPublic ? 'Sí' : 'No' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref } from 'vue';
  import api from '../axios';
  import imagenGenerica from '../assets/Imagen-generica.png'
  import { userStore } from '../stores/authStore';
  
  const contentCount = ref(0);
  
  // Toggles
  const isProfilePublic = ref(false);
  const isChatPublic = ref(false);
  
  // Foto de perfil (por ahora imagen genérica)
  const profilePicture = imagenGenerica;
  
  const toggleProfile = async () => {
    isProfilePublic.value = !isProfilePublic.value;
    await api.put(`/api/user/${userStore.value?.id}/settings`, {
      profilePublic: isProfilePublic.value,
    });
  };
  
  const toggleChat = async () => {
    isChatPublic.value = !isChatPublic.value;
    await api.put(`/api/user/${userStore.value?.id}/settings`, {
      chatPublic: isChatPublic.value,
    });
  };
  </script>
  
  <style scoped>
  .profile {
    display: flex;
    justify-content: center;
    padding: 40px;
  }
  
  .profile-card {
    width: 400px;
    text-align: center;
    border: 1px solid #ccc;
    padding: 30px;
    border-radius: 20px;
  }
  
  img {
    width: 150px;
    border-radius: 50%;
    margin-bottom: 20px;
  }
  
  .toggle {
    margin: 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  button {
    padding: 8px 15px;
    font-weight: bold;
    border-radius: 8px;
    border: none;
    color: white;
    cursor: pointer;
  }
  
  button.on {
    background-color: green;
  }
  
  button.off {
    background-color: red;
  }
  </style>
  
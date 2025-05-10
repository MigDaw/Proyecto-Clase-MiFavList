<template>
  <div class="profile">
    <div class="profile-card">
      <img :src="userStore?.profilePic ? `http://localhost:8080${userStore.profilePic}` 
      : imagenGenerica" alt="Foto de perfil" class="profile-pic" />
      <input type="file" @change="handleImageUpload" />
      <h2>{{ userStore?.username }}</h2>
      <div class="email-section">
        <p v-if="!editingEmail">
          {{ userStore?.email }}
          <button @click="editingEmail = true">✏️</button>
        </p>
        <div v-else>
          <input v-model="newEmail" type="email" />
          <button @click="updateEmail">Guardar</button>
          <button @click="cancelEdit">Cancelar</button>
        </div>
      </div>
      <p><strong>Contenido subido:</strong> {{ contentCount }}</p>

      <div class="toggles">
        <div class="toggle">
          <span>Perfil público</span>
          <button :class="{'on': userStore?.perfilPublic, 'off': userStore?.perfilPublic}" @click="toggleProfile">
            {{ userStore?.perfilPublic ? 'Sí' : 'No' }}
          </button>
        </div>

        <div class="toggle">
          <span>Chat público</span>
          <button :class="{'on': userStore?.chatPublic, 'off': !userStore?.chatPublic}" @click="toggleChat">
            {{ userStore?.chatPublic ? 'Sí' : 'No' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup lang="ts">
  import { ref } from 'vue';
  import imagenGenerica from '../assets/Imagen-generica.png'
  import { userStore } from '../stores/authStore';
  import { useToast } from 'vue-toastification';
  import api from '../axios';

  const toast = useToast();

  const contentCount = ref(0);

  const editingEmail = ref(false);
  const newEmail = ref(userStore.value?.email || '');

  const cancelEdit = () => {
  editingEmail.value = false;
  newEmail.value = userStore.value?.email || '';
};

const handleImageUpload = async (event: Event) => {
  const file = (event.target as HTMLInputElement)?.files?.[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('profilePic', file);

  try {
    const res = await api.post('/api/profile/upload-picture', formData);
    if (userStore.value) {
      userStore.value.profilePic = res.data.profilePic;
      toast.success('Imagen actualizada correctamente');
    }
  } catch {
    toast.error('Error al subir imagen');
  }
};

const updateEmail = async () => {
  try {
    await api.put('/api/profile', { email: newEmail.value });
    if (userStore.value) userStore.value.email = newEmail.value;
    toast.success('Correo actualizado correctamente');
    editingEmail.value = false;
  } catch {
    toast.error('Error al actualizar el correo');
  }
};

const toggleProfile = async () => {
  if (!userStore.value) return;
  const newValue = !userStore.value.perfilPublic;
  try {
    await api.put('/api/profile', { perfilPublic: newValue });
    userStore.value.perfilPublic = newValue;
    toast.success('Visibilidad del perfil actualizada');
  } catch {
    toast.error('Error al actualizar visibilidad del perfil');
  }
};

const toggleChat = async () => {
  if (!userStore.value) return;
  const newValue = !userStore.value.chatPublic;
  try {
    await api.put('/api/profile', { chatPublic: newValue });
    userStore.value.chatPublic = newValue;
    toast.success('Configuración del chat actualizada');
  } catch {
    toast.error('Error al actualizar chat');
  }
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
  
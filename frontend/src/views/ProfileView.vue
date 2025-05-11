<template>
  <div class="profile">
    <div class="profile-card">
      <img :src="userStore?.profilePic ? `http://localhost:8080${userStore.profilePic}` 
      : imagenGenerica" alt="Foto de perfil" class="profile-pic" />
      <span v-if="loadingImage" class="spinner"></span>
      <label for="file">
         <img :src="iconoEdit" alt="Subir imagen" class="icono" />
      </label>
      <input type="file" id="file" @change="handleImageUpload" style="display: none" />

      <h2>{{ userStore?.username }}</h2>

      <div class="email-section">
        <p v-if="!editingEmail">
          {{ userStore?.email }}
          <button @click="editingEmail = true" class="editar-email">
            <img :src="iconoEdit" alt="Editar email" class="icono" />
          </button>
        </p>
        <div v-else>
          <input v-model="newEmail" type="email" class="input-email"/>
          <br>
          <button @click="updateEmail">Guardar</button>
          <button @click="cancelEmailEdit">Cancelar</button>
        </div>
        <span v-if="loadingEmail" class="spinner"></span>
      </div>

      <div class="password-section">
        <p v-if="!editingPassword">
          <button @click="editingPassword = true" class="editar-password">
            Cambiar contraseña
            <img :src="iconoEdit" alt="Editar contraseña" class="icono" />
          </button>
        </p>
        <div v-else>
          <input v-model="currentPassword" type="password" class="input-pass" placeholder="Contraseña actual"/>
          <br>
          <input v-model="newPassword" type="password" class="input-pass" placeholder="Nueva contraseña"/>
          <br>
          <button @click="updatePassword">Guardar</button>
          <button @click="cancelPasswordEdit">Cancelar</button>
        </div>
        <span v-if="loadingPassword" class="spinner"></span>
      </div>

      <p><strong>Contenido subido:</strong> {{ contentCount }}</p>

      <div class="toggle">
        <span>Perfil público</span>
        <button :class="{'on': userStore?.perfilPublic, 'off': !userStore?.perfilPublic}" @click="toggleProfile">
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
</template>
  
<script setup lang="ts">
  import { ref } from 'vue';
  import imagenGenerica from '../assets/Imagen-generica.png'
  import { userStore } from '../stores/authStore';
  import { useToast } from 'vue-toastification';
  import api from '../axios';
  import "../assets/estilos/spinner.css";
  import iconoEdit from "../assets/icono-edit.svg";

  const toast = useToast();

  const loadingImage = ref(false);
  const loadingEmail = ref(false);
  const loadingPassword = ref(false);

  const contentCount = ref(0);

  const editingEmail = ref(false);
  const newEmail = ref(userStore.value?.email || '');

  const editingPassword = ref(false);
  const currentPassword = ref('');
  const newPassword = ref('');

  const cancelEmailEdit = () => {
    editingEmail.value = false;
    newEmail.value = userStore.value?.email || '';
  };

  const cancelPasswordEdit = () => {
  editingPassword.value = false
  currentPassword.value = ''
  newPassword.value = ''
}

  const handleImageUpload = async (event: Event) => {
    const file = (event.target as HTMLInputElement)?.files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('profilePic', file);
    loadingImage.value = true;//spiner cargando
    try {
      const res = await api.post('/api/profile/upload-picture', formData);
      if (userStore.value) {
        userStore.value.profilePic = res.data.profilePic;
        toast.success('Imagen actualizada correctamente');
      }
    } catch {
      toast.error('Error al subir imagen');
    } finally {
      loadingImage.value = false;
    }
  };

  const updateEmail = async () => {
    loadingEmail.value = true;
    try {
      await api.put('/api/profile', { email: newEmail.value });
      if (userStore.value) userStore.value.email = newEmail.value;
      toast.success('Correo actualizado correctamente');
      editingEmail.value = false;
    } catch {
      toast.error('Error al actualizar el correo');
    }finally {
      loadingEmail.value = false;
    }
  };

  const updatePassword = async () => {
    loadingPassword.value = true;
    try {
      await api.put('/api/profile/password', {
        currentPassword: currentPassword.value,
        newPassword: newPassword.value,
      });
      toast.success('Contraseña actualizada correctamente');
      editingPassword.value = false;
      currentPassword.value = '';
      newPassword.value = '';
    } catch (err:any) {
      toast.error(
        err.response?.data?.error || 'Error al actualizar la contraseña'
      );
    } finally {
      loadingPassword.value = false;
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
  input{
    font-family: Lexend;
    padding: 10px;
    border-radius: 10px;
  }

  .input-email{
    width: 90%;
    margin: 10px 0;
    font-size: 1rem;
  }

  .profile {
    display: flex;
    justify-content: center;
    padding: 40px;
  }

  .icono{
    cursor: pointer;
    border-radius: 0%;
    width: 24px;
    height: 24px;
    margin-right: 8px;
    vertical-align: middle;
  }

  .profile-card {
    width: 400px;
    text-align: center;
    border: 2px solid #ccc;
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
    margin: 5px;
    padding: 10px;
    font-weight: bold;
    border-radius: 8px;
    border: none;
    background-color: #3b3b3b;
    color: white;
    cursor: pointer;
    font-size: 1rem;
  }

  .editar-email {
    margin: 0;
    padding: 0px;
    background: none;
    border: none;
    cursor: pointer;
  }

  .input-pass{
    width: 90%;
    margin: 10px 0;
    font-size: 1rem;
  }

  .editar-password:hover{
    background-color: #5c5c5c;
  }

  button.on {
    background-color: green;
  }

  button.off {
    background-color: red;
  }
</style>
  
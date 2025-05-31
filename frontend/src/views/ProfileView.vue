<template>
  <div class="profile-layout">
    <div class="profile-nav">
      <NavbarVertical
        v-if="!isOwnProfile"
        :userId="profileUser?.id || route.params.id"
      />
    </div>
    <div class="profile-center">
      <div class="profile-card">
        <img
          :src="
            profileUser?.profilePic
              ? `http://localhost:8080${profileUser.profilePic}`
              : imagenGenerica
          "
          alt="Foto de perfil"
          class="profile-pic"
        />
        <span v-if="loadingImage" class="spinner"></span>
        <label v-if="isOwnProfile" for="file">
          <img :src="iconoEdit" alt="Subir imagen" class="icono" />
        </label>
        <input
          v-if="isOwnProfile"
          type="file"
          id="file"
          @change="handleImageUpload"
          style="display: none"
        />

        <h2>{{ profileUser?.username }}</h2>

        <div class="email-section">
          <p v-if="!editingEmail">
            {{ profileUser?.email }}
            <button
              v-if="isOwnProfile"
              @click="editingEmail = true"
              class="editar-email"
            >
              <img :src="iconoEdit" alt="Editar email" class="icono" />
            </button>
          </p>
          <div v-else>
            <input v-model="newEmail" type="email" class="input-email" />
            <br />
            <button @click="updateEmail" class="btn" :disabled="loadingEmail">
              Guardar
            </button>
            <button
              @click="cancelEmailEdit"
              class="btn"
              :disabled="loadingEmail"
            >
              Cancelar
            </button>
          </div>
          <span v-if="loadingEmail" class="spinner"></span>
        </div>

        <div class="password-section" v-if="isOwnProfile">
          <p v-if="!editingPassword">
            <button @click="editingPassword = true" class="editar-password">
              Cambiar contraseña
              <img :src="iconoEdit" alt="Editar contraseña" class="icono" />
            </button>
          </p>
          <div v-else>
            <input
              v-model="currentPassword"
              type="password"
              class="input-pass"
              placeholder="Contraseña actual"
            />
            <br />
            <input
              v-model="newPassword"
              type="password"
              class="input-pass"
              placeholder="Nueva contraseña"
            />
            <br />
            <button
              @click="updatePassword"
              class="btn"
              :disabled="loadingPassword"
            >
              Guardar
            </button>
            <button
              @click="cancelPasswordEdit"
              class="btn"
              :disabled="loadingPassword"
            >
              Cancelar
            </button>
          </div>
          <span v-if="loadingPassword" class="spinner"></span>
        </div>

        <ul class="content-stats">
          <li><strong>Contenido total:</strong> {{ contentStats.total }}</li>
          <li><strong>Películas:</strong> {{ contentStats.peliculas }}</li>
          <li><strong>Series:</strong> {{ contentStats.series }}</li>
          <li><strong>Libros:</strong> {{ contentStats.libros }}</li>
          <li><strong>Cómics:</strong> {{ contentStats.comics }}</li>
          <li><strong>Manga:</strong> {{ contentStats.manga }}</li>
          <li><strong>Anime:</strong> {{ contentStats.anime }}</li>
          <li><strong>Videojuegos:</strong> {{ contentStats.videojuegos }}</li>
        </ul>

        <div class="toggle" v-if="isOwnProfile">
          <span>Perfil público</span>
          <div class="toggle-btn-group">
            <button
              :class="{
                on: profileUser?.perfilPublic,
                off: !profileUser?.perfilPublic,
              }"
              @click="toggleProfile"
              :disabled="loadingProfile"
            >
              {{ profileUser?.perfilPublic ? "Sí" : "No" }}
            </button>
            <span v-if="loadingProfile" class="spinner spinner-inline"></span>
          </div>
        </div>

        <div class="toggle" v-if="isOwnProfile">
          <span>Chat público</span>
          <div class="toggle-btn-group">
            <button
              :class="{
                on: profileUser?.chatPublic,
                off: !profileUser?.chatPublic,
              }"
              @click="toggleChat"
              :disabled="loadingChat"
            >
              {{ profileUser?.chatPublic ? "Sí" : "No" }}
            </button>
            <span v-if="loadingChat" class="spinner spinner-inline"></span>
          </div>
        </div>
      </div>
    </div>
    <ProfileComments
      :profileUser="profileUser"
      :isOwnProfile="isOwnProfile"
      :userStore="userStore"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import imagenGenerica from "../assets/Imagen-generica.png";
import { userStore } from "../stores/authStore";
import { useToast } from "vue-toastification";
import api from "../axios";
import "../assets/estilos/spinner.css";
import iconoEdit from "../assets/icono-edit.svg";
import { useRoute } from "vue-router";
import NavbarVertical from "../components/NavbarVertical.vue";
import ProfileComments from "../components/ProfileComments.vue";

const toast = useToast();
const route = useRoute();

const loadingImage = ref(false);
const loadingEmail = ref(false);
const loadingPassword = ref(false);
const loadingProfile = ref(false);
const loadingChat = ref(false);

const editingEmail = ref(false);
const newEmail = ref(userStore.value?.email || "");

const editingPassword = ref(false);
const currentPassword = ref("");
const newPassword = ref("");

const contentStats = ref({
  total: 0,
  peliculas: 0,
  series: 0,
  libros: 0,
  comics: 0,
  manga: 0,
  anime: 0,
  videojuegos: 0,
});

const profileUser = ref<any>(null);
const isOwnProfile = computed(
  () => !route.params.id || route.params.id === userStore.value?.id
);

const cancelEmailEdit = () => {
  editingEmail.value = false;
  newEmail.value = profileUser.value?.email || "";
};

const cancelPasswordEdit = () => {
  editingPassword.value = false;
  currentPassword.value = "";
  newPassword.value = "";
};

const handleImageUpload = async (event: Event) => {
  const file = (event.target as HTMLInputElement)?.files?.[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("profilePic", file);
  loadingImage.value = true;
  try {
    const res = await api.post("/api/profile/upload-picture", formData);
    if (userStore.value) {
      userStore.value.profilePic = res.data.profilePic;
    }
    if (profileUser.value) {
      profileUser.value.profilePic = res.data.profilePic;
    }
    toast.success("Imagen actualizada correctamente");
  } catch {
    toast.error("Error al subir imagen");
  } finally {
    loadingImage.value = false;
  }
};

const updateEmail = async () => {
  loadingEmail.value = true;
  try {
    await api.put("/api/profile", { email: newEmail.value });
    if (userStore.value) userStore.value.email = newEmail.value;
    if (profileUser.value) profileUser.value.email = newEmail.value;
    toast.success("Correo actualizado correctamente");
    editingEmail.value = false;
  } catch {
    toast.error("Error al actualizar el correo");
  } finally {
    loadingEmail.value = false;
  }
};

const updatePassword = async () => {
  loadingPassword.value = true;
  try {
    await api.put("/api/profile/password", {
      currentPassword: currentPassword.value,
      newPassword: newPassword.value,
    });
    toast.success("Contraseña actualizada correctamente");
    editingPassword.value = false;
    currentPassword.value = "";
    newPassword.value = "";
  } catch (err: any) {
    toast.error(
      err.response?.data?.error || "Error al actualizar la contraseña"
    );
  } finally {
    loadingPassword.value = false;
  }
};

const toggleProfile = async () => {
  if (!userStore.value) return;
  const newValue = !userStore.value.perfilPublic;
  loadingProfile.value = true;
  try {
    await api.put("/api/profile", { perfilPublic: newValue });
    userStore.value.perfilPublic = newValue;
    if (profileUser.value) profileUser.value.perfilPublic = newValue;
    toast.success("Visibilidad del perfil actualizada");
  } catch {
    toast.error("Error al actualizar visibilidad del perfil");
  } finally {
    loadingProfile.value = false;
  }
};

const toggleChat = async () => {
  if (!userStore.value) return;
  const newValue = !userStore.value.chatPublic;
  loadingChat.value = true;
  try {
    await api.put("/api/profile", { chatPublic: newValue });
    userStore.value.chatPublic = newValue;
    if (profileUser.value) profileUser.value.chatPublic = newValue;
    toast.success("Configuración del chat actualizada");
  } catch {
    toast.error("Error al actualizar chat");
  } finally {
    loadingChat.value = false;
  }
};

const fetchContentStats = async () => {
  try {
    let res;
    if (isOwnProfile.value) {
      res = await api.get("/api/user-content");
    } else {
      res = await api.get(`/api/user-content/${route.params.id}`);
    }
    const items = res.data as { type: string }[];
    contentStats.value.total = items.length;
    contentStats.value.peliculas = items.filter(
      (i) => i.type === "peliculas"
    ).length;
    contentStats.value.series = items.filter((i) => i.type === "series").length;
    contentStats.value.libros = items.filter((i) => i.type === "libros").length;
    contentStats.value.comics = items.filter((i) => i.type === "comics").length;
    contentStats.value.manga = items.filter((i) => i.type === "manga").length;
    contentStats.value.anime = items.filter((i) => i.type === "anime").length;
    contentStats.value.videojuegos = items.filter(
      (i) => i.type === "videojuegos"
    ).length;
  } catch {
    toast.error("Error al cargar estadísticas de contenido");
  }
};

const fetchProfileUser = async () => {
  if (isOwnProfile.value) {
    profileUser.value = userStore.value;
  } else {
    try {
      const res = await api.get(`/api/users/${route.params.id}`);
      profileUser.value = res.data;
    } catch {
      profileUser.value = null;
      toast.error("No se pudo cargar el perfil");
    }
  }
};

watch(isOwnProfile, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    fetchProfileUser();
    fetchContentStats();
  }
});

onMounted(() => {
  fetchProfileUser();
  fetchContentStats();
});
</script>

<style scoped>
input {
  font-family: Lexend;
  padding: 10px;
  border-radius: 10px;
}

.input-email {
  width: 90%;
  margin: 10px 0;
  font-size: 1rem;
}

.profile-layout {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  justify-content: center;
  gap: 3.5rem; 
  background: #232323;
  padding: 40px 0;
}

.profile-nav {
  flex: 0 0 220px;
  display: flex;
  justify-content: flex-end;
  align-items: flex-start;
  min-height: 600px;
}

.profile-center {
  flex: 0 0 auto;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

.profile-card {
  width: 350px;
  max-width: 95vw;
  text-align: center;
  border: 2px solid #ccc;
  padding: 30px 24px;
  border-radius: 20px;
  background: #565256;
  margin: 0;
  box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
}

.profile-pic {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 50%;
  margin-bottom: 18px;
  border: 3px solid #393a3b;
}

.icono {
  cursor: pointer;
  border-radius: 0%;
  width: 24px;
  height: 24px;
  margin-right: 8px;
  vertical-align: middle;
  transition: 0.2;
}

.icono:hover {
  transform: scale(1.2);
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

button[disabled] {
  background: #888 !important;
  color: #ccc !important;
  cursor: not-allowed !important;
  opacity: 0.7;
}

.editar-email {
  margin: 0;
  padding: 0px;
  background: none;
  border: none;
  cursor: pointer;
}

.input-pass {
  width: 90%;
  margin: 10px 0;
  font-size: 1rem;
}

.editar-password {
  font-family: Lexend;
}
.editar-password:hover {
  background-color: #5c5c5c;
}

button.on {
  min-width: 50px;
  background-color: rgb(29, 145, 74);
  box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
}

button.on:hover {
  background-color: rgb(7, 173, 71);
}

button.off {
  min-width: 50px;
  background-color: rgb(226, 42, 42);
  box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
}

button.off:hover {
  background-color: rgb(254, 53, 53);
}

.toggle-btn-group {
  display: flex;
  align-items: center;
}

.spinner-inline {
  margin-left: 10px;
  width: 24px !important;
  height: 24px !important;
  border-width: 3px !important;
}

.content-stats {
  list-style: none;
  padding: 0;
  margin: 20px 0;
  text-align: left;
}

.content-stats li {
  margin: 5px 0;
}

/* Modal igual que UserContentList */

.spinner-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 24px;
}
.spinner {
  width: 24px !important;
  height: 24px !important;
  border-width: 3px !important;
}

@media (max-width: 1200px) {
  .profile-layout {
    flex-direction: column;
    gap: 1.5rem;
    align-items: stretch;
    padding: 20px 0;
  }
  .profile-nav,
  .profile-center,
  .profile-comments-col {
    min-height: unset;
    justify-content: center;
    align-items: center;
  }
  .profile-card,
  .profile-comments {
    margin: 0 auto;
    width: 95vw;
    max-width: 500px;
  }
}
</style>

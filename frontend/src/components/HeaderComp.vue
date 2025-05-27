<template>
  <header>
    <img src="../assets/Logo 1.png" alt="logo" />

    <div class="socials">
      <a href="/contacto" target="_blank"
        ><img :src="logoGmail" alt="Icono gmail" title="Contacto por correo electrónico"
      /></a>
      <a href="https://github.com/MigDaw" target="_blank"
        ><img :src="logoGithub" alt="Logo de GitHub" title="Perfil de GitHub"
      /></a>
      <a href="https://instagram.com/" target="_blank"
        ><img :src="logoInstagram" alt="Logo de Instagram" title="Perfil de Instagram"
      /></a>
      <a href="https://x.com/?lang=es" target="_blank"
        ><img :src="logoX" alt="Logo de X" title="Perfil de X"
      /></a>
    </div>

    <div v-if="userStore" class="user-wrapper">
      <div class="user-display" @click="toggleMenu">
        <img
          :src="
            userStore?.profilePic
              ? `http://localhost:8080${userStore.profilePic}`
              : imagenGenerica
          "
          alt="Foto de perfil"
          class="img-user"
        />
        {{ userStore.username }}
        <span class="chevron">{{ menuOpen ? "▲" : "▼" }}</span>
      </div>

      <div v-if="menuOpen" class="dropdown">
        <router-link to="/perfil">Ver perfil</router-link>
        <a @click="logout">Cerrar sesión</a>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../axios";
import { useRouter } from "vue-router";
import { userStore } from "../stores/authStore";
import imagenGenerica from "../assets/Imagen-generica.png";
import { useToast } from "vue-toastification";
import logoGithub from "../assets/logo-github.svg";
import logoGmail from "../assets/logo-gmail.svg";
import logoInstagram from "../assets/logo-instagram.svg";
import logoX from "../assets/logo-x.svg";

const toast = useToast();

const menuOpen = ref(false);
const router = useRouter();

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
};

const logout = async () => {
  await api.post("/api/logout", {}, { withCredentials: true });
  userStore.value = null;
  router.push("/");
  toast.success("Has cerrado sesión");
};

onMounted(async () => {
  try {
    const response = await api.get("/api/me", {
      withCredentials: true,
    });
    userStore.value = response.data;
  } catch {
    userStore.value = null;
  }
});
</script>

<style scoped>
header {
  background-color: rgb(61, 58, 58);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
}

.socials {
  display: flex;
  gap: 18px;
  justify-content: center;
  align-items: center;
  flex: 1;
}

.socials img {
  width: 28px;
  height: 28px;
  filter: brightness(0) invert(1); /* Blanco sobre fondo oscuro */
  transition: transform 0.2s;
  
}

.socials img:hover {
  transform: scale(1.15);
}

.img-user {
  width: 40px;
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
  box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
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

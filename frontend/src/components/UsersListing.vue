<template>
  <ul>
    <li v-for="user in users" :key="user.id">
      {{ user.username }}: 
      <span v-if="user.isPublic">perfil público</span>
      <span v-else>perfil privado</span>
      <button 
        :disabled="!user.isPublic"
        @click="goToProfile(user.id)"
      >
        Ver perfil
      </button>
    </li>
  </ul>
</template>

<script setup>
import { useRouter } from 'vue-router';
const props = defineProps({ users: Array });
const router = useRouter();

function goToProfile(userId) {
  // Para ir al perfil de otro usuario
  router.push({ name: 'perfil-usuario', params: { id: userId } });
}
</script>
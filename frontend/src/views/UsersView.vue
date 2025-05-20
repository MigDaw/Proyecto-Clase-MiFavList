<template>
  <div>
    <h1>Usuarios Registrados</h1>
    <UsersListing :users="users" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import UsersListing from '../components/UsersListing.vue';
import api from '../axios';

const users = ref([]);

onMounted(async () => {
  try {
    const res = await api.get('/api/users');
    users.value = res.data;
  } catch (error) {
    console.error('Error al cargar usuarios:', error);
  }
});
</script>
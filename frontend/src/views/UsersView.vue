<template>
  <div>
    <h1>Usuarios Registrados</h1>
    <div v-if="loading" class="spinner-container">
      <div class="spinner"></div>
    </div>
    <UsersListing v-else :users="users" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import UsersListing from '../components/UsersListing.vue';
import api from '../axios';
import '../assets/estilos/spinner.css';

const users = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await api.get('/api/users');
    const usersWithContent = await Promise.all(
      res.data.map(async user => {
        try {
          const contentRes = await api.get(`/api/user-content/${user.id}`);
          return {
            ...user,
            contenidoCount: contentRes.data.length
          };
        } catch {
          return {
            ...user,
            contenidoCount: 0
          };
        }
      })
    );
    users.value = usersWithContent;
  } catch (error) {
    console.error('Error al cargar usuarios:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>

h1 {
  text-align: center;
  margin: 2rem 0;
}

.spinner-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 200px;
}
</style>
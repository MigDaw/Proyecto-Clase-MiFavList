<template>
  <div>
    <div class="header-controls">
      <h1>Usuarios Registrados</h1>
      <div class="controls">
        <label>
          Ordenar por:
          <select v-model="sortBy">
            <option value="nombre">Nombre</option>
            <option value="contenido">Cantidad de contenido</option>
          </select>
        </label>
        <label class="checkbox-label">
          <input type="checkbox" v-model="onlyPublic" class="inp-checkbox"/>
          Solo perfiles públicos
        </label>
      </div>
    </div>
    <div v-if="loading" class="spinner-container">
      <div class="spinner"></div>
    </div>
    <UsersListing v-else :users="sortedAndFilteredUsers" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import UsersListing from '../components/UsersListing.vue';
import api from '../axios';
import '../assets/estilos/spinner.css';

const users = ref([]);
const loading = ref(true);
const sortBy = ref('nombre');
const onlyPublic = ref(false);

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

// Computed para ordenar y filtrar
const sortedAndFilteredUsers = computed(() => {
  let filtered = users.value;
  if (onlyPublic.value) {
    filtered = filtered.filter(u => u.isPublic);
  }
  if (sortBy.value === 'nombre') {
    filtered = [...filtered].sort((a, b) => a.username.localeCompare(b.username));
  } else if (sortBy.value === 'contenido') {
    filtered = [...filtered].sort((a, b) => b.contenidoCount - a.contenidoCount);
  }
  return filtered;
});
</script>

<style scoped>
  .header-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 700px;
    margin: 2rem auto 1rem auto;
    gap: 1.5rem;
  }
  h1 {
    margin: 0;
  }
  .checkbox-label{
    cursor: pointer;
  }
  .inp-checkbox{
    width: 1.2em;
    height: 1.2em;
    margin-right: 0.5rem;
    cursor: pointer;
  }

  select{
    font-family: Lexend;
    font-size: 1em;
    padding: 0.4em 0.6em;
    border-radius: 1rem;
    margin: 7px 0; 
    cursor: pointer;
  }

  .controls {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    font-size: 1em;
    background: #444;
    padding: 0.6em 1em;
    border-radius: 8px;
  }
  .controls label {
    color: #fff;
    font-weight: 400;
  }
  .spinner-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
  }
</style>
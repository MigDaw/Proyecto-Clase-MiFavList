<template>
  <div class="user-content-list">
    <h2>Mis {{ capitalizedTipo }}</h2>

    <div v-if="filteredContent.length === 0">
      <p>No tienes contenido añadido de este tipo.</p>
    </div>

    <div class="content-cards" v-else>
      <div class="content-card" v-for="item in filteredContent" :key="item.id">
        <img :src="`http://localhost:8080${item.image}`|| defaultImage" alt="Portada" />
        <h3>{{ item.title }}</h3>
        <p>Género: {{ item.genre }}</p>
        <p>Estado: {{ item.status }}</p>
        <p>Nota: {{ item.rating }}/10</p>
        <p>Añadido el: {{ formatDate(item.addedAt) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../axios'; // Asegúrate de tener configurado tu cliente axios
import defaultImage from '../assets/Imagen-generica.png';

const route = useRoute();
const tipo = computed(() => route.params.tipo as string);

const capitalizedTipo = computed(() =>
  tipo.value.charAt(0).toUpperCase() + tipo.value.slice(1)
);

const allContent = ref<any[]>([]);
const filteredContent = computed(() =>
  allContent.value.filter((item) => item.type === tipo.value)
);

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('es-ES');
};

onMounted(async () => {
  try {
    const response = await api.get('/api/user-content');
    allContent.value = response.data;
  } catch (error) {
    console.error('Error cargando contenido del usuario:', error);
  }
});
</script>

<style scoped>
.user-content-list {
  padding: 2rem;
}

.content-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.content-card {
  background: #413d3d;
  border-radius: 6px;
  padding: 1rem;
  width: 200px;
  text-align: center;
}

.content-card img {
  width: 100%;
  height: auto;
  border-radius: 4px;
}
</style>

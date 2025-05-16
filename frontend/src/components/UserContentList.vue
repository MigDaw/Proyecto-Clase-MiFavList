<template>
  <div class="user-content-list">
    <h2>Mis {{ capitalizedTipo }}</h2>

    <div v-if="filteredContent.length === 0">
      <p>No tienes contenido añadido de este tipo.</p>
    </div>

    <div class="content-cards" v-else>
      <div class="content-card" v-for="item in filteredContent" :key="item.id">
        <img :src="`http://localhost:8080${item.image}` || defaultImage" alt="Portada" />
        <h3>{{ item.title }}</h3>
        <p>Género: {{ item.genre }}</p>

        <div v-if="editingId === item.id">
          <label>
            Estado:
            <select v-model="item.status" class="status-select">
              <option value="viendo">Viendo</option>
              <option value="completado">Completado</option>
              <option value="pendiente">Pendiente</option>
            </select>
          </label>
          <div v-if="item.status === 'completado'">
            <label>
              Valoración:
              <input
                type="number"
                min="0"
                max="10"
                v-model.number="item.rating" class="rating-input"
                placeholder="Valoración (0-10)"
              />
            </label>
            <span>/10</span>
          </div>
          <div class="edit-actions">
            <button @click="saveEdit(item)">Guardar</button>
            <button @click="cancelEdit">Cancelar</button>
          </div>
        </div>
        <div v-else>
          <p>Estado: {{ item.status }}</p>
          <p v-if="item.status === 'completado' && item.rating !== null">Valoración: {{ item.rating }}/10</p>
          <p>Añadido el: {{ formatDate(item.addedAt) }}</p>
          <img
            :src="iconoEdit"
            alt="Editar"
            class="edit-icon"
            @click="startEdit(item)"
            title="Editar"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted , watch} from 'vue';
  import { useRoute } from 'vue-router';
  import api from '../axios';
  import { useToast } from 'vue-toastification';
  import defaultImage from '../assets/Imagen-generica.png';
  import iconoEdit from '../assets/icono-edit.svg';
  

  const toast = useToast();
  const route = useRoute();

  const tipo = computed(() => route.params.tipo as string);
  const props = defineProps<{ tipo: string; refresh?: number }>();
  const editingId = ref<string | null>(null);

  const previousData = ref<{ status: string;rating: number | null;} | null>(null);

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

  const fetchContent = async () => {
    try {
      const response = await api.get('/api/user-content');
      allContent.value = response.data;
    } catch (error) {
      console.error('Error cargando contenido del usuario:', error);
    }
  };

  const startEdit = (item: any) => {
  editingId.value = item.id;
  previousData.value = {
    status: item.status,
    rating: item.rating,
  };
};

  const updateUserContent = async (item: any, newStatus?: string, newRating?: number) => {
  try {
    await api.put(`/api/user-content/${item.id}`, {
      status: newStatus ?? item.status,
      rating: newRating ?? item.rating,
    });
    fetchContent();
    toast.success('Actualizado correctamente');
  } catch (error) {
    toast.error('Error al actualizar');
  }
};


  const saveEdit = async (item: any) => {
  // Si el estado no es 'completado', borra la nota antes de guardar
  if (item.status !== 'completado') {
    item.rating = null;
  }
  await updateUserContent(item, item.status, item.rating);
  editingId.value = null;
};

  const cancelEdit = () => {
  if (editingId.value && previousData.value) {
    // Busca el item que se estaba editando
    const item = allContent.value.find((i) => i.id === editingId.value);
    if (item) {
      item.status = previousData.value.status;
      item.rating = previousData.value.rating;
    }
  }
  editingId.value = null;
};

  onMounted(fetchContent);

  // Refresca la lista cuando cambie la prop refresh
  watch(() => props.refresh, fetchContent);
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
    height: 370px;
    text-align: center;
  }

  .content-card img {
    width: 100%;
    height: 100px;
    border-radius: 4px;
  }

  .content-card .edit-icon {
    cursor: pointer;
    width: 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.12);
    display: flex;
  }

  .content-card .status-select {
    font-family: Lexend;
    margin: 10px 0;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid #ccc;
  }

  .content-card .rating-input {
    font-family: Lexend;
    margin: 10px 0;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid #ccc;
  }

  .edit-actions button{
    font-family: Lexend;
    padding: 4px;
    margin: 10px;
    font-size: 0.9rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}
</style>

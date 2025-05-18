<template>
  <div class="user-content-list">
    <h2>Mis {{ capitalizedTipo }}</h2>

    <div v-if="loadingContent" class="spinner-container">
      <span class="spinner carga-todo"></span>
    </div>
    <div v-else>
      <div v-if="filteredContent.length === 0">
        <p>Aún no tienes contenido añadido de este tipo.</p>
      </div>

      <div class="content-cards" v-else>
        <div class="content-card" v-for="item in filteredContent" :key="item.id">
          <img
            :src="item.image ? `http://localhost:8080${item.image}` : imagenPredefinida"
            alt="Portada"
            class="portada"
          />
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
              <button @click="saveEdit(item)" :disabled="loadingEditId === item.id">Guardar</button>
              <button @click="cancelEdit" :disabled="loadingEditId === item.id">Cancelar</button>
            </div>
            <div v-if="loadingEditId === item.id" class="spinner-container">
              <span class="spinner"></span>
            </div>
          </div>
          <div v-else>
            <p>Estado: {{ item.status }}</p>
            <p v-if="item.status === 'completado' && item.rating !== null">Valoración: {{ item.rating }}/10</p>
            <p v-else>Valoración: -/10</p>
            <p>Añadido el: {{ formatDate(item.addedAt) }}</p>
            <div class="icon-actions">
              <img
                :src="iconoEdit"
                alt="Editar"
                class="edit-icon"
                @click="startEdit(item)"
                title="Editar"
              />
              <img
                :src="iconoDelete"
                alt="Eliminar"
                class="edit-icon"
                @click="openDeleteModal(item)"
                title="Eliminar"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showDeleteModal" class="modal-overlay">
  <div class="modal-content">
    <p>¿Seguro que deseas eliminar este contenido?</p>
    <div class="modal-actions">
      <button @click="confirmDelete" :disabled="loadingDelete">Confirmar</button>
      <button @click="showDeleteModal = false" :disabled="loadingDelete">Cancelar</button>
    </div>
    <div v-if="loadingDelete" class="spinner-container">
      <span class="spinner"></span>
    </div>
  </div>
</div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted , watch} from 'vue';
  import { useRoute } from 'vue-router';
  import api from '../axios';
  import { useToast } from 'vue-toastification';
  import imagenPredefinida from '../assets/imagen-predefinida.png';
  import iconoEdit from '../assets/icono-edit.svg';
  import iconoDelete from '../assets/icono-delete.svg';
  import '../assets/estilos/spinner.css';
  
  const toast = useToast();
  const route = useRoute();

  const tipo = computed(() => route.params.tipo as string);
  const props = defineProps<{ tipo: string; refresh?: number }>();
  const editingId = ref<string | null>(null);
  const loadingDelete = ref(false);
  const loadingEditId = ref<string | null>(null);
  const loadingContent = ref(true);
  
  const showDeleteModal = ref(false);
  const itemToDelete = ref<any>(null);

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
    loadingContent.value = true;
    try {
      const response = await api.get('/api/user-content');
      allContent.value = response.data;
    } catch (error) {
      console.error('Error cargando contenido del usuario:', error);
    }
    loadingContent.value = false;
  };

  const startEdit = (item: any) => {
    editingId.value = item.id;
    previousData.value = {
      status: item.status,
      rating: item.rating,
    };
    console.log(item.image)
    console.log(item)
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
    loadingEditId.value = item.id;
    if (item.status !== 'completado') {
      item.rating = null;
    }
    await updateUserContent(item, item.status, item.rating);
    editingId.value = null;
    loadingEditId.value = null;
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

  const openDeleteModal = (item: any) => {
    itemToDelete.value = item;
    showDeleteModal.value = true;
  };

  const confirmDelete = async () => {
    if (!itemToDelete.value) return;
    loadingDelete.value = true;
    try {
      await api.delete(`/api/user-content/${itemToDelete.value.id}`);
      toast.success('Contenido eliminado, espera unos segundos y lo verás reflejado.');
      fetchContent();
    } catch (error) {
      toast.error('Error al eliminar');
    }
    loadingDelete.value = false;
    showDeleteModal.value = false;
    itemToDelete.value = null;
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
    height: 390px;
    text-align: center;
  }

  .content-card .portada {
    width: 100%;
    height: 160px;
    border-radius: 4px;
  }

  .content-card .status-select {
    font-family: Lexend;
    margin: 10px 0;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid #ccc;
    cursor: pointer;
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

.edit-actions button[disabled],
.edit-actions button:disabled {
  background: #888 !important;
  color: #ccc !important;
  cursor: not-allowed !important;
  opacity: 0.7;
}

  .modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(4px) brightness(0.8);
  }

  .modal-content {
    background: #222;
    padding: 2rem 2.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 32px rgba(0,0,0,0.25);
    text-align: center;
    min-width: 300px;
  }

  .modal-actions {
    margin-top: 1.5rem;
  }

  .modal-actions button {
    margin: 0 10px;
    padding: 8px 20px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-family: Lexend;
    font-size: 1rem;
  }

  .modal-actions button[disabled] {
  margin-top:10px;
  background: #888 !important;
  color: #ccc !important;
  cursor: not-allowed !important;
  opacity: 0.7;
}

  .icon-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
  }

  .icon-actions .edit-icon {
    width: 24px;
    cursor: pointer;
    transition: transform 0.2s;
  }

  .icon-actions .edit-icon:hover {
    transform: scale(1.15);
  }

  .spinner-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 24px; /* Aumenta el margen superior */
}

.spinner {
  width: 24px !important;
  height: 24px !important;
  border-width: 3px !important;
}

.spinner.carga-todo {
  width: 54px !important;
  height: 54px !important;
  border-width: 5px !important;
}
</style>

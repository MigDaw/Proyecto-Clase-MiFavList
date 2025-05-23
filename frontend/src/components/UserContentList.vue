<template>
  <h2 v-if="props.titulo">{{ props.titulo }}</h2>
      <h2 v-else-if="props.editable !== false">Mis {{ capitalizedTipo }}</h2>
  <div class="user-content-list">
    <div class="top-controls">
      
      <div class="content-controls">
        <AddUserContent
          v-if="props.editable !== false"
          :tipo="tipo"
          @content-added="fetchContent"
        />
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por título..."
          class="search-input"
        />
        <label>
          Ordenar por:
          <select v-model="sortBy">
            <option value="titulo">Título</option>
            <option value="valoracion">Valoración</option>
          </select>
        </label>
        <label>
          Orden:
          <select v-model="sortOrder">
            <option value="asc">Ascendente</option>
            <option value="desc">Descendente</option>
          </select>
        </label>
        <label>
          Estado:
          <select v-model="statusFilter">
            <option value="">Todos</option>
            <option value="viendo">Viendo</option>
            <option value="completado">Completado</option>
            <option value="pendiente">Pendiente</option>
          </select>
        </label>
      </div>
    </div>

    <div v-if="loadingContent" class="spinner-container">
      <span class="spinner carga-todo"></span>
    </div>
    <div v-else>
      <div v-if="filteredContent.length === 0">
        <p>No hay contenido añadido de este tipo.</p>
      </div>
      <div v-else class="content-cards">
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
            <div v-if="props.editable !== false" class="icon-actions">
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
  import AddUserContent from './AddUserContent.vue';
  import '../assets/estilos/spinner.css';

  const toast = useToast();
  const route = useRoute();

  const tipo = computed(() => route.params.tipo as string);
  const props = defineProps<{
    tipo: string;
    refresh?: number;
    editable?: boolean;
    userId?: string;
    titulo?: string;
  }>();
  
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
  const sortBy = ref<'titulo' | 'valoracion'>('titulo');
  const sortOrder = ref<'asc' | 'desc'>('asc');
  const statusFilter = ref<string>('');
  const search = ref<string>('');

  const filteredContent = computed(() => {
    let filtered = allContent.value.filter((item) => item.type === tipo.value);

    if (statusFilter.value) {
      filtered = filtered.filter(item => item.status === statusFilter.value);
    }

    if (search.value) {
      filtered = filtered.filter(item => item.title.toLowerCase().includes(search.value.toLowerCase()));
    }

    if (sortBy.value === 'titulo') {
      filtered = [...filtered].sort((a, b) =>
        a.title.localeCompare(b.title)
      );
    } else if (sortBy.value === 'valoracion') {
      filtered = [...filtered].sort((a, b) =>
        (a.rating ?? 0) - (b.rating ?? 0)
      );
    }

    if (sortOrder.value === 'desc') {
      filtered.reverse();
    }

    return filtered;
  });

  const formatDate = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-ES');
  };

  const fetchContent = async () => {
    loadingContent.value = true;
    try {
      let url = '/api/user-content';
      if (props.userId) {
        url = `/api/user-content/userConcreto/${props.userId}`;
      }
      const response = await api.get(url);
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
    max-width: 1800px;
    margin: 2rem auto;
    padding: 1.5rem 1rem;
    background: #565256;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  }

  /* Centrado de controles y título */
  .top-controls {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.2rem;
    margin-bottom: 2rem;
  }

  .content-controls {
    display: flex;
    gap: 1rem;
    margin-bottom: 0;
    flex-wrap: wrap;
    justify-content: center;
  }

  .content-controls label {
    font-family: Lexend;
    font-size: 0.9rem;
    display: flex;
    flex-direction: column;
    font-size: 1em;
  }

  .content-controls select {
    font-family: Lexend;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid #ccc;
    cursor: pointer;
    margin-top: 4px;
    font-size: 1em;
    padding: 0.6em 1em;
    border-radius: 1rem;
  }

  .search-input {
    font-family: Lexend;
    padding: 0.4em 0.7em;
    border-radius: 1rem;
    border: none;
    font-size: 1em;
    margin-top: 30px;
    outline: none;
    height: 20px;
  }

  .content-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
  }

  .content-card {
    background: #393a3b;
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
    padding: 5px;
    margin: 10px;
    font-size: 0.9rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    background-color: rgb(97, 111, 104);
    color: white;
  }

  .edit-actions button:hover{
    background-color: rgb(67, 78, 73);
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
    background-color: rgb(97, 111, 104);
    color: white;
  }

  .modal-actions button:hover {
    background-color: rgb(67, 78, 73);
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
    margin-top: 24px;
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

  .content-controls > *:first-child {
    margin-top: 0.8rem;
    margin-right: 1.5rem;
  }
  @media (max-width: 700px) {
    .user-content-list {
      max-width: 98vw;
      padding: 0.5rem 0.2rem;
    }
  }
  h2{
    text-align: center;
  }
</style>

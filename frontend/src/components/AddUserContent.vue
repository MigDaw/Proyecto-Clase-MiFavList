<template>
  <div class="add-user-content">
    <button @click="showForm = true" class="toggle-button-addContent">
      + Añadir contenido
    </button>

    <div v-if="showForm" class="modal-overlay">
      <div class="modal-content">
        <form class="form-container" @submit.prevent="addContent">
          <label for="title">Título:</label>
          <input v-model="title" type="text" required placeholder="Título" />

          <label for="genre">Género:</label>
          <select v-model="genre" required>
            <option disabled value="">Selecciona un género</option>
            <option>Acción</option>
            <option>Aventura</option>
            <option>Ciencia ficción</option>
            <option>Comedia</option>
            <option>Comedia romántica</option>
            <option>Crimen / Policíaco</option>
            <option>Drama</option>
            <option>Terror/Horror</option>
            <option>Fantasía</option>
            <option>Romance</option>
          </select>

          <label for="image">Imagen:</label>
          <input type="file" @change="onImageChange" />

          <label for="status">Estado:</label>
          <select v-model="status">
            <option value="En curso">En curso</option>
            <option value="completado">Completado</option>
            <option value="pendiente">Pendiente</option>
          </select>

          <label v-if="status === 'completado'" for="rating">Valoración:</label>
          <input
            v-if="status === 'completado'"
            v-model.number="rating"
            type="number"
            min="0"
            max="10"
          />

          <div class="modal-actions">
            <button type="submit" :disabled="loading">Añadir</button>
            <button type="button" @click="showForm = false" :disabled="loading">Cancelar</button>
          </div>
          <div v-if="loading" class="spinner-container">
            <span class="spinner"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import api from "../axios";
import { useToast } from "vue-toastification";
import '../assets/estilos/spinner.css';

const toast = useToast();

const showForm = ref(false);
const title = ref("");
const genre = ref("");
const status = ref("pendiente");
const rating = ref(0);
const imageFile = ref<File | null>(null);
const props = defineProps<{ tipo: string }>();
const emit = defineEmits(['content-added']);
const loading = ref(false);

const onImageChange = (e: Event) => {
  const files = (e.target as HTMLInputElement).files;
  if (files && files[0]) imageFile.value = files[0];
};

const addContent = async () => {
  loading.value = true;
  let imagePath = "";
  if (imageFile.value) {
    const formData = new FormData();
    formData.append("image", imageFile.value);
    try {
      const uploadRes = await api.post("/api/content/upload-image", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      imagePath = uploadRes.data.image;
    } catch (err) {
      toast.error("Error subiendo la imagen.");
      loading.value = false;
      return;
    }
  }

  try {
    const contentRes = await api.post("/api/content", {
      title: title.value,
      genre: genre.value,
      type: props.tipo,
      image: imagePath,
    });

    const contentId = contentRes.data.id;
    await api.post("/api/user-content", {
      contentId,
      status: status.value,
      rating: rating.value,
    });

    toast.success("Contenido añadido correctamente, porfavor espera unos segundos y lo verás en la lista.");
    emit("content-added");

    // Limpieza
    title.value = "";
    genre.value = "";
    status.value = "pendiente";
    rating.value = 0;
    imageFile.value = null;
    showForm.value = false;
  } catch (err) {
    toast.error("Error al añadir contenido.");
  }
  loading.value = false;
};
</script>

<style scoped>
  .toggle-button-addContent {
    font-family: Lexend;
    background: #393a3b;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 10px 20px;
    font-size: 1rem;
    cursor: pointer;
    box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
    -webkit-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
    -moz-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  }

  .toggle-button-addContent:hover {
    background: #5c5c5c;
  }

  .add-user-content {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  .modal-content {
    background: #222;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    padding: 24px;
    width: 90%;
    max-width: 500px;
    position: relative;
  }

  .form-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .form-container label {
    font-weight: 600;
    margin-bottom: 4px;
    color: white;;
  }

  .form-container input[type="text"],
  .form-container input[type="number"],
  .form-container select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
    background: #f9f9f9;
    transition: border 0.2s;
  }

  .form-container input[type="text"]:focus,
  .form-container input[type="number"]:focus,
  .form-container select:focus {
    border-color: #0078d4;
    outline: none;
  }

  .form-container input[type="file"] {
    border: none;
    background: none;
    padding: 0;
  }

  .form-container button[type="submit"],
  .modal-actions button {
    padding: 10px;
    background-color: #3b3b3b;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s;
  }

  .form-container button[type="submit"]:hover,
  .modal-actions button:hover {
    background-color: #5c5c5c;
  }

  .modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  select{
    font-family: Lexend;
  }

  .spinner-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
  }

  .form-container button[disabled],
  .modal-actions button[disabled] {
    background: #888 !important;
    color: #ccc !important;
    cursor: not-allowed !important;
    opacity: 0.7;
  }

  input{
    font-family: Lexend;
  }
</style>

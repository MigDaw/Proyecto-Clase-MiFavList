<template>
  <div class="add-user-content">
    <button @click="showForm = !showForm" class="toggle-button-addContent">
      {{ showForm ? "Cancelar" : "Añadir contenido" }}
    </button>

    <form v-if="showForm" class="form-container" @submit.prevent="addContent">
      <label for="title">Título:</label>
      <input v-model="title" type="text" required placeholder="Título" />

      <label for="genre">Género:</label>
      <input v-model="genre" type="text" required placeholder="Género" />

      <label for="type">Tipo:</label>
      <select v-model="type" required>
        <option value="peliculas">Película</option>
        <option value="series">Serie</option>
        <option value="libros">Libro</option>
      </select>

      <label for="image">Imagen:</label>
      <input type="file" @change="onImageChange" />

      <label for="status">Estado:</label>
      <select v-model="status">
        <option value="viendo">Viendo</option>
        <option value="completado">Completado</option>
        <option value="pendiente">Pendiente</option>
      </select>

      <label for="rating">Valoración:</label>
      <input v-model.number="rating" type="number" min="0" max="10" />

      <button type="submit">Añadir</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import api from "../axios";
import { useToast } from "vue-toastification";

const toast = useToast();

const showForm = ref(false);
const title = ref("");
const genre = ref("");
const type = ref("pelicula");
const status = ref("pendiente");
const rating = ref(0);
const imageFile = ref<File | null>(null);

const onImageChange = (e: Event) => {
  const files = (e.target as HTMLInputElement).files;
  if (files && files[0]) imageFile.value = files[0];
};

const addContent = async () => {
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
      return;
    }
  }

  try {
    // 1. Crear el contenido
    const contentRes = await api.post("/api/content", {
      title: title.value,
      genre: genre.value,
      type: type.value,
      image: imagePath,
    });

    // 2. Añadir el contenido al usuario
    const contentId = contentRes.data.id;
    await api.post("/api/user-content", {
      contentId,
      status: status.value,
      rating: rating.value,
    });

    toast.success("Contenido añadido correctamente");
    // Limpieza
    title.value = "";
    genre.value = "";
    type.value = "pelicula";
    status.value = "pendiente";
    rating.value = 0;
    imageFile.value = null;
    showForm.value = false;
  } catch (err) {
    toast.error("Error al añadir contenido.");
  }
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
  transition: background 0.2s;
}

.add-user-content {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.form-container {
  margin: 16px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  padding: 24px 32px;
  min-width: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.form-container label {
  font-weight: 00;
  margin-bottom: 4px;
  color: #333;
}

.form-container input[type="text"],
.form-container input[type="number"],
.form-container select {
  padding: 8px 10px;
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

.form-container button[type="submit"] {
  margin-top: 10px;
  padding: 10px 0;
  background: #0078d4;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.form-container button[type="submit"]:hover {
  background: #005fa3;
}
</style>

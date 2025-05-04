<template>
    <nav v-if="isLoggedIn" class="navbar">
      <router-link to="/contenido/peliculas">Películas</router-link>
      <router-link to="/contenido/series">Series</router-link>
      <router-link to="/contenido/libros">Libros</router-link>
      <router-link to="/contenido/comics">Cómics</router-link>
      <router-link to="/contenido/manga">Manga</router-link>
      <router-link to="/contenido/anime">Anime</router-link>
      <router-link to="/contenido/videojuegos">Videojuegos</router-link>
      <router-link to="/usuarios">Usuarios</router-link>
    </nav>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import api from '../axios';
  
  const isLoggedIn = ref(false);
  
  onMounted(async () => {
    try {
      const res = await api.get('/api/me');
      isLoggedIn.value = !!res.data;
    } catch {
      isLoggedIn.value = false;
    }
  });
  </script>
  
  <style scoped>
  .navbar {
    display: flex;
    gap: 1rem;
    background-color: #3b3b3b;
    padding: 10px;
    justify-content: center;
  }
  
  a {
    color: white;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background-color 0.2s;
  }
  
  a:hover {
    background-color: #5c5c5c;
  }
  
  .router-link-active {
    font-weight: bold;
    border-bottom: 2px solid white;
  }
  </style>
  
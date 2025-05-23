<template>
  <nav v-if="userStore" class="navbar">
    <router-link to="/contenido/peliculas">Mis Películas</router-link>
    <router-link to="/contenido/series">Mis Series</router-link>
    <router-link to="/contenido/libros">Mis Libros</router-link>
    <router-link to="/contenido/comics">Mis Cómics</router-link>
    <router-link to="/contenido/manga">Mis Mangas</router-link>
    <router-link to="/contenido/anime">Mis Animes</router-link>
    <router-link to="/contenido/videojuegos">Mis Videojuegos</router-link>
    <router-link to="/usuarios">Usuarios</router-link>
  </nav>
</template>
  
<script setup lang="ts">
  import { userStore } from '../stores/authStore';
</script>
  
<style scoped>
  .navbar {
    display: flex;
    gap: 1rem;
    background-color: #484545;
    padding: 10px;
    justify-content: center;
    flex-wrap: wrap;
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
  
  /* Responsive para tablets */
  @media (max-width: 900px) {
    .navbar {
      gap: 0.5rem;
      padding: 8px;
      font-size: 0.95em;
    }
    a {
      padding: 5px 8px;
      font-size: 0.95em;
    }
  }
  
  /* Responsive para móviles */
  @media (max-width: 600px) {
    .navbar {
      flex-direction: column;
      align-items: stretch;
      gap: 0.5rem;
      padding: 8px 0;
    }
    a {
      padding: 10px 0;
      text-align: center;
      font-size: 1em;
    }
  }
</style>

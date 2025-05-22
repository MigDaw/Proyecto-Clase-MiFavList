<template>
  <nav class="navbar-vertical">
    <router-link
      v-for="item in items"
      :key="item.tipo"
      :to="`/contenido/${item.tipo}/${props.userId}`"
      class="nav-link"
      active-class="active"
    >
      {{ item.label }}
    </router-link>
  </nav>
</template>

<script setup lang="ts">
    const props = defineProps<{
    userId: string;
    }>();

    const items = [
    { tipo: 'peliculas', label: 'Películas' },
    { tipo: 'series', label: 'Series' },
    { tipo: 'libros', label: 'Libros' },
    { tipo: 'comics', label: 'Cómics' },
    { tipo: 'manga', label: 'Manga' },
    { tipo: 'anime', label: 'Anime' },
    { tipo: 'videojuegos', label: 'Videojuegos' },
    ];
</script>

<style scoped>
    .navbar-vertical {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    background: #484545;
    padding: 2rem 1rem;
    border-radius: 16px;
    min-width: 180px;
    align-items: flex-start;
    }
    .nav-link {
    color: #fff;
    text-decoration: none;
    font-size: 1.1rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: background 0.2s;
    }
    .nav-link.active,
    .nav-link:hover {
    background: #6a6767;
    }
</style>
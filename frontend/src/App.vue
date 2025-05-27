<template>
  <div class="app-layout">
    <HeaderComp />
    <Navbar />
    <div class="main-content">
      <RouterView />
    </div>
    <FooterComp />
  </div>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router';
import FooterComp from './components/FooterComp.vue';
import HeaderComp from './components/HeaderComp.vue';
import Navbar from './components/Navbar.vue';
</script>

<style scoped>

body {
  font-family: Lexend, sans-serif;
  margin: 0;
  padding: 0;
}

.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

header,
HeaderComp {
  flex-shrink: 0;
  /* Puedes poner height: 60px; si quieres un alto fijo */
}

nav,
Navbar {
  flex-shrink: 0;
}

.main-content {
  flex: 1 0 auto;
  /* Hace que ocupe el espacio restante */
  display: flex;
  flex-direction: column;
}

footer,
FooterComp {
  flex-shrink: 0;
}

body {
  font-family: Lexend, sans-serif;
  margin: 0;
  padding: 0;
}
</style>
<template>
    <div>
      <h1>{{ capitalizedTipo }}</h1>
      <!-- Aquí iría tu lógica para mostrar los contenidos filtrados por tipo -->
      <p>Mostrando contenido de tipo: <strong>{{ tipo }}</strong></p>
    </div>
  </template>
  
  <script setup lang="ts">
  import { useRoute } from 'vue-router';
  import { computed } from 'vue';
  
  const route = useRoute();
  const tipo = computed(() => route.params.tipo as string);
  
  // Si quieres mostrarlo bonito
  const capitalizedTipo = computed(() => {
    return tipo.value.charAt(0).toUpperCase() + tipo.value.slice(1);
  });
  </script>
  
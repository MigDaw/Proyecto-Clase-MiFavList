<template>
  <div>
    <UserContentList :tipo="tipo" :refresh="refreshKey" />
    <AddUserContent :tipo="tipo" @content-added="refreshList" />
  </div>
</template>

<script setup lang="ts">
  import { useRoute } from 'vue-router';
  import { ref, computed } from 'vue';
  import UserContentList from '../components/UserContentList.vue';
  import AddUserContent from '../components/AddUserContent.vue';

  const route = useRoute();
  const tipo = computed(() => route.params.tipo as string);

  // Clave reactiva para forzar el refresco de la lista
  const refreshKey = ref(0);
  const refreshList = () => {
    refreshKey.value++;
};
</script>
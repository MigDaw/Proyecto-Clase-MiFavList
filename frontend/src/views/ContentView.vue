<template>
    <div>
      <UserContentList :tipo="tipo"></UserContentList>
      <AddUserContent :tipo="tipo"></AddUserContent>
    </div>
  </template>
  
  <script setup lang="ts">
    import { useRoute } from 'vue-router';
    import { computed } from 'vue';
    import UserContentList from '../components/UserContentList.vue';
    import AddUserContent from '../components/AddUserContent.vue';
    
    const route = useRoute();
    const tipo = computed(() => route.params.tipo as string);
    
    
  </script>
  
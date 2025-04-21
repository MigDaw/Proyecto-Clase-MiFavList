<template>
  <HeaderComp></HeaderComp>
</template>


<script setup lang="ts">
  import HeaderComp from './components/HeaderComp.vue'

  //A PARTIR DE AQUÍ ES LA PRUEBA DEL BACKEND
  /* 
  import { onMounted } from 'vue'
  onMounted(async () => {
    try {
      const response = await fetch('http://localhost:8080/api/test')
      const data = await response.json()
      console.log('✅ Respuesta del backend:', data)
    } catch (error) {
      console.error('❌ Error al conectarse al backend:', error)
    }
  }) */
</script>
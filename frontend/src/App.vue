<template>
  <HeaderComp></HeaderComp>
  <Navbar></Navbar>
  <RouterView></RouterView>
  <FooterComp></FooterComp>
</template>


<script setup lang="ts">
  import { RouterView } from 'vue-router';
import FooterComp from './components/FooterComp.vue';
import HeaderComp from './components/HeaderComp.vue'
import Navbar from './components/Navbar.vue';

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
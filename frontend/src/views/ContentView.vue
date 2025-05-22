<template>
  <div>
    <UserContentList
      :tipo="tipo"
      :refresh="refreshKey"
      :editable="!idUser"
      :userId="idUser"
      :titulo="idUser ? `${capitalizedTipo} del usuario:  ${nombreUsuario}` : undefined"
    />
  </div>
</template>

<script setup lang="ts">
  import { useRoute } from 'vue-router';
  import { ref, computed, watch } from 'vue';
  import UserContentList from '../components/UserContentList.vue';
  import api from '../axios';

  const route = useRoute();
  const tipo = computed(() => route.params.tipo as string);
  const idUser = computed(() => route.params.idUser as string | undefined);

  const nombreUsuario = ref('');

  watch(idUser, async (newId) => {
    if (newId) {
      try {
        const res = await api.get(`/api/users/${newId}`);
        nombreUsuario.value = res.data.username;
      } catch (e) {
        nombreUsuario.value = '';
      }
    } else {
      nombreUsuario.value = '';
    }
  }, { immediate: true });

  const capitalizedTipo = computed(() =>
    tipo.value.charAt(0).toUpperCase() + tipo.value.slice(1)
  );

  const refreshKey = ref(0);
  const refreshList = () => {
    refreshKey.value++;
  };
</script>
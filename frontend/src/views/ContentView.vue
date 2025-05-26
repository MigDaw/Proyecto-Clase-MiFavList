<template>
  <div>
    <div v-if="idUser" class="content-header">
      <h2>
        {{ capitalizedTipo }} del usuario: {{ nombreUsuario }}
        
      </h2>
      <router-link
          :to="`/perfil/${idUser}`"
          class="volver-perfil-btn"
        >
          Volver al perfil
      </router-link>
    </div>
    <UserContentList
      :tipo="tipo"
      :refresh="refreshKey"
      :editable="!idUser"
      :userId="idUser"
      :titulo="!idUser ? `Mis ${capitalizedTipo}` : undefined"
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

<style scoped>
  .content-header {
    display: flex;
    justify-content: center;
    text-align: center;
    align-items: center;
    gap: 1.5rem;
    margin: 10px 0;
  }

  .volver-perfil-btn {
    background: #484545;
    color: #fff;
    padding: 0.4em 1em;
    border-radius: 8px;
    text-decoration: none;
    margin-left: 1em;
    transition: background 0.2s;
    box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
    -webkit-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
    -moz-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
  }
  .volver-perfil-btn:hover {
    background: #6a6767;
  }
</style>
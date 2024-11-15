<template>
  <div>
    <Menubar :model="items">
      <template #end>
        <Button label="Perfil" text outlined icon="pi pi-user" />
        <Button text outlined icon="pi pi-sign-out" />
      </template>
    </Menubar>
  </div>
</template>

<script setup lang="ts">
import Menubar from 'primevue/menubar';
import { computed, ref } from 'vue';
import Button from 'primevue/button';

const items = ref([
  {
    label: 'Home',
    icon: 'pi pi-home',
    command: () => {
      window.location.href = '/';
    },
  },
  {
    label: 'Alugar',
    icon: 'pi pi-book',
    command: () => {
      window.location.href = '/rent';
    },
  },
  {
    label: 'Anunciar',
    icon: 'pi pi-megaphone',
    command: () => {
      window.location.href = '/announce';
    },
  },
  {
    label: 'Devolver',
    icon: 'pi pi-backward',
    command: () => {
      window.location.href = '/return';
    },
  },
]);
</script>

<template>
  <div>
    <ApplicationMenu />
    <Toast />
    <div
      class="flex flex-column justify-content-center align-items-center mb-4"
    >
      <h2 class="font-bold mt-2">Qual ferramenta você procura?</h2>
      <InputText
        placeholder="Pesquise o nome da ferramenta"
        class="home-input"
        type="text"
        v-model="searchToolString"
      />
    </div>
    <div
      v-if="isLoading"
      class="flex justify-content-center align-items-center w-full h-20rem"
    >
      <ProgressSpinner />
    </div>
    <div class="p-6" v-if="!isLoading" v-show="searchToolString">
      <div class="card">
        <div
          v-if="filteredAnnounces.length"
          class="flex announces-container gap-4 px-2 py-1 overflow-auto"
        >
          <div
            v-for="announce in filteredAnnounces"
            :key="announce.codproduto"
            class="flex announce-item"
          >
            <div class="flex flex-column">
              <span class="font-medium"
                >Ferramenta: <span>{{ announce.nomeproduto }}</span></span
              >
              <span class="font-medium"
                >Valor diária:
                <span>{{ formatCurrency(announce.valordiaria) }}</span></span
              >
              <Image
                class="image"
                :src="announce.imagemproduto || defaultImage"
                :alt="announce.nomeproduto"
                width="350"
                height="180"
              />
            </div>
          </div>
        </div>
        <div
          v-if="!filteredAnnounces.length"
          class="card flex justify-content-center align-items-center"
        >
          <span class="font-medium">Nenhum anúncio encontrado</span>
        </div>
      </div>
    </div>
    <div class="p-6" v-if="!isLoading" v-show="!searchToolString">
      <div class="flex align-items-center gap-2">
        <h2 class="font-bold">Anúncios que cabem no seu bolso</h2>
        <i
          class="pi pi-info-circle text-xl"
          v-tooltip="{
            value: 'Anúncios com a diária abaixo de R$ 10,00.',
            show: true,
            pt: {
              arrow: { style: { display: 'none' } },
              text: {
                style: {
                  width: '300px',
                  padding: '10.5px',
                  background: '#495057',
                },
              },
            },
          }"
        />
      </div>
      <div>
        <div
          v-if="cheapestAnnounces.length"
          class="card flex gap-3 px-2 py-1 overflow-auto"
        >
          <div
            v-for="announce in cheapestAnnounces"
            :key="announce.codproduto"
            class="flex"
          >
            <div class="flex flex-column">
              <span class="font-medium"
                >Ferramenta: <span>{{ announce.nomeproduto }}</span></span
              >
              <span class="font-medium"
                >Valor diária:
                <span>{{ formatCurrency(announce.valordiaria) }}</span></span
              >
              <Image
                class="image"
                :src="announce.imagemproduto || defaultImage"
                :alt="announce.nomeproduto"
                width="350"
                height="180"
              />
            </div>
          </div>
        </div>
        <div
          v-if="!cheapestAnnounces.length"
          class="card flex justify-content-center align-items-center"
        >
          <span class="font-medium">Nenhum anúncio encontrado</span>
        </div>
      </div>
      <div class="flex align-items-center gap-2 mt-5">
        <h2 class="font-bold">Ferramentas Bivolt</h2>
        <i
          class="pi pi-info-circle text-xl"
          v-tooltip="{
            value: 'Anúncios de ferramentas Bivolt.',
            show: true,
            pt: {
              arrow: { style: { display: 'none' } },
              text: {
                style: {
                  width: '230px',
                  padding: '10.5px',
                  background: '#495057',
                },
              },
            },
          }"
        />
      </div>
      <div>
        <div
          v-if="bivoltTools.length"
          class="card flex gap-3 px-2 py-1 overflow-auto"
        >
          <div
            v-for="announce in bivoltTools"
            :key="announce.codproduto"
            class="flex"
          >
            <div class="flex flex-column">
              <span class="font-medium"
                >Ferramenta: <span> {{ announce.nomeproduto }}</span></span
              >
              <span class="font-medium"
                >Valor diária:
                <span> {{ formatCurrency(announce.valordiaria) }}</span></span
              >
              <Image
                class="image"
                :src="announce.imagemproduto || defaultImage"
                :alt="announce.nomeproduto"
                width="350"
                height="180"
              />
            </div>
          </div>
        </div>
        <div
          v-if="!bivoltTools.length"
          class="card flex justify-content-center align-items-center"
        >
          <span class="font-medium">Nenhum anúncio encontrado</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Imports
import { ref, onMounted, computed } from 'vue';
import ApplicationMenu from '@/components/ApplicationMenu.vue';
import InputText from 'primevue/inputtext';
import axios from 'axios';
import ProgressSpinner from 'primevue/progressspinner';
import Image from 'primevue/image';
import { formatCurrency } from '@/shared/util/methods';
import defaultImage from '@/assets/no-image-available.png';

// Interfaces
interface Announce {
  codproduto?: string;
  nomeproduto?: string;
  valordiaria?: number;
  imagemproduto?: string;
  voltagem?: string;
}

// Refs
const searchToolString = ref('');
const cheapestAnnounces = ref<Announce[]>([]);
const bivoltTools = ref<Announce[]>([]);
const isLoading = ref(false);

const announces = ref<Announce[]>([]);

// Computed
const filteredAnnounces = computed(() => {
  return announces.value.filter((announce) =>
    announce.nomeproduto
      .toLowerCase()
      .includes(searchToolString.value.toLowerCase())
  );
});

// Lyfecycle
onMounted(async () => {
  isLoading.value = true;
  await fetchAnnounces();
  cheapestAnnounces.value = announces.value.filter(
    (announce) => announce.valordiaria < 10
  );
  bivoltTools.value = announces.value.filter(
    (announce) => announce.voltagem === '0'
  );
  isLoading.value = false;
});

// Methods
const fetchAnnounces = async () => {
  try {
    const response = await axios.get(
      'http://localhost/AluFerr/backend/src/services/getAllAnnounces.php'
    );
    if (!response.error) {
      announces.value = response.data;
    } else {
      console.error('Error fetching data from API:', response);
    }
  } catch (error) {
    console.error('Error fetching data from API:', error);
  }
};
</script>

<style>
.home-input {
  width: 21rem;
  margin-top: 10px;
}
.card {
  height: fit-content;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
}
.announces-container {
  display: flex;
  flex-wrap: wrap;
  gap: 16px; /* Adjust the gap between items as needed */
}

.announce-item {
  flex: 1 1 calc(33.333% - 16px); /* Adjust the width and gap as needed */
  box-sizing: border-box;
}
</style>

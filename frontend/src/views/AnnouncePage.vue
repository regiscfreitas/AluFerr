<template>
  <div>
    <ApplicationMenu class="mb-3" />
    <Toast />
    <div class="flex justify-content-center gap-4">
      <div class="flex flex-column">
        <h2 class="font-bold">Anunciar</h2>
        <div class="announce-form w-25rem p-3 flex flex-column gap-3">
          <div class="flex flex-column gap-1">
            <LabelRequired
              for="product-name"
              :label="'Nome produto'"
              :required="!newAnnounceDTO.nomeproduto"
            />
            <InputText
              :invalid="isFormSubmitted && !newAnnounceDTO.nomeproduto"
              id="product-name"
              v-model="newAnnounceDTO.nomeproduto"
            />
          </div>
          <div class="flex flex-column gap-1">
            <LabelRequired
              for="product-diary-price"
              :label="'Diária'"
              :required="!newAnnounceDTO.valordiaria"
            />
            <InputNumber
              id="product-diary-price"
              mode="currency"
              currency="BRL"
              fluid
              locale="pt-BR"
              :invalid="isFormSubmitted && !newAnnounceDTO.valordiaria"
              v-model="newAnnounceDTO.valordiaria"
            />
          </div>
          <div class="flex flex-column gap-1">
            <LabelRequired
              for="product-brand"
              :label="'Marca'"
              :required="false"
            />
            <InputText id="product-brand" v-model="newAnnounceDTO.marca" />
          </div>
          <div class="flex flex-column gap-1">
            <LabelRequired
              for="product-model"
              :label="'Modelo'"
              :required="false"
            />
            <InputText id="product-model" v-model="newAnnounceDTO.modelo" />
          </div>
          <div class="flex flex-column gap-1">
            <LabelRequired
              for="product-voltage"
              :label="'Voltagem'"
              :required="false"
            />
            <Select
              id="product-voltage"
              v-model="newAnnounceDTO.voltagem"
              :options="voltageOptions"
              optionLabel="label"
              optionValue="value"
            />
          </div>
          <div class="flex flex-column gap-1">
            <LabelRequired
              for="product-image"
              :label="'Imagem'"
              :required="!fileName"
            />
            <div class="flex flex-column file-upload">
              <input
                type="file"
                id="imagemProduto"
                accept="image/*"
                @change="getFileName"
              />
              <label for="imagemProduto" class="upload-label"
                >Escolha uma imagem</label
              >
              <label v-if="fileName">{{ fileName }}</label>
            </div>
          </div>
          <div class="flex gap-2 justify-content-end mt-auto">
            <Button
              label="Cancelar"
              outlined
              text
              :loading="isFormLoading"
              @click="clearForm"
            />
            <Button
              label="Anunciar"
              :loading="isFormLoading"
              @click="handleSubmit"
            />
          </div>
        </div>
      </div>
      <div class="flex flex-column">
        <h2 class="font-bold">Últimos anúncios</h2>
        <div class="announce-form w-25rem p-3 flex flex-column gap-3">
          <div
            v-if="isFormLoading"
            class="flex justify-content-center align-items-center h-full"
          >
            <ProgressSpinner />
          </div>
          <div
            v-if="!isFormLoading && !lastAnnounces.length"
            class="flex justify-content-center align-items-center h-full"
          >
            <span class="font-medium">Nenhum anúncio encontrado.</span>
          </div>
          <div v-if="!isFormLoading && lastAnnounces.length">
            <div
              v-for="announce in lastAnnounces"
              :key="announce.codproduto"
              class="flex flex-column gap-1"
            >
              <div class="flex flex-column justify-content-between">
                <span>Ferramenta: {{ announce.nomeproduto }}</span>
                <span>Diária: {{ formatCurrency(announce.valordiaria) }}</span>
                <span
                  >Data anúncio: {{ formatDate(announce.datacriacao) }}</span
                >
              </div>
              <div class="flex justify-content-between">
                <Image
                  :key="announce.codproduto"
                  class="image"
                  :src="announce.imagemproduto"
                  :alt="announce.nomeproduto"
                  width="350"
                  height="160"
                />
              </div>
              <hr />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Imports
import { ref, onMounted } from 'vue';
import { v4 as uuidv4 } from 'uuid';
import ApplicationMenu from '@/components/ApplicationMenu.vue';
import FileUpload from 'primevue/fileupload';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import axios from 'axios';
import InputNumber from 'primevue/inputnumber';
import ProgressSpinner from 'primevue/progressspinner';
import Select from 'primevue/select';
import Image from 'primevue/image';
import LabelRequired from '@/components/label/LabelRequired.vue';
import { useToast } from '@/composables';
import { formatCurrency, formatDate } from '@/shared/util/methods';

// LifeCycle
onMounted(() => {
  getRecentAnnounces();
});

// Uses
const toast = useToast();

// Refs
const isFormLoading = ref(false);
const isFormSubmitted = ref(false);
const lastAnnounces = ref([]);
const fileName = ref('');
const newAnnounceDTO = ref({
  nomeproduto: '',
  valordiaria: null,
  marca: '',
  modelo: '',
  voltagem: null,
  imagemproduto: null,
});
const voltageOptions = ref([
  { label: '110v', value: 110 },
  { label: '220v', value: 220 },
  { label: 'Bivolt', value: 0 },
]);

// Methods
const handleSubmit = async () => {
  isFormSubmitted.value = true;

  if (isAnyRequiredInputEmpty()) {
    toast.showError(
      'Preencha todos os campos obrigatórios! Eles são indicados com um *'
    );
    return;
  }

  try {
    isFormLoading.value = true;

    // Cria um novo FormData
    const formData = new FormData();

    // Adiciona o codproduto usando uuidv4()
    const codproduto = uuidv4();
    formData.append('codproduto', codproduto);

    // Adiciona a data de criação
    formData.append('datacriacao', new Date().toISOString());

    // Adiciona os outros dados do formulário
    for (const [key, value] of Object.entries(newAnnounceDTO.value)) {
      if (value !== null && value !== undefined) {
        formData.append(key, value);
      } else {
        console.warn(`Campo ${key} está vazio ou indefinido.`);
      }
    }

    // Obtém o input do arquivo e adiciona ao FormData
    const fileInput = document.getElementById(
      'imagemProduto'
    ) as HTMLInputElement;
    if (fileInput && fileInput.files && fileInput.files[0]) {
      formData.append('imagemproduto', fileInput.files[0]);
    } else {
      console.warn('Nenhum arquivo de imagem foi selecionado.');
    }

    // Envia o FormData para a API
    const response = await axios.post(
      'http://localhost/AluFerr/backend/src/services/announceTool.php',
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );

    // Verifica a resposta da API
    if (!response.error) {
      toast.showSuccess('Anúncio criado com sucesso!');
      clearForm();
      getRecentAnnounces();
    } else {
      toast.showError('Erro ao criar anúncio!');
      clearForm();
    }
  } catch (error) {
    console.error('Error fetching data from API:', error);
    toast.showError('Erro ao criar anúncio!');
    clearForm();
  } finally {
    isFormLoading.value = false;
  }
};

const getRecentAnnounces = async () => {
  try {
    isFormLoading.value = true;
    const response = await axios.get(
      'http://localhost/AluFerr/backend/src/services/getRecentAnnounces.php'
    );
    if (response.status === 200) {
      lastAnnounces.value = response.data;
      lastAnnounces.value.forEach((announce) => {
        const imgElement = document.createElement('img');
        imgElement.src = announce.imagemproduto;
        imgElement.alt = announce.nomeproduto;

        document.getElementById('announcesContainer')?.appendChild(imgElement);
      });
    } else {
      console.error('Error fetching data from API:', response);
    }
    isFormLoading.value = false;
  } catch (error) {
    console.error('Error fetching data from API:', error);
    isFormLoading.value = false;
  }
};

const isAnyRequiredInputEmpty = () => {
  return (
    !newAnnounceDTO.value.nomeproduto ||
    !newAnnounceDTO.value.valordiaria ||
    !fileName.value
  );
};

const clearForm = () => {
  isFormSubmitted.value = false;
  isFormLoading.value = false;
  newAnnounceDTO.value = {
    nomeproduto: '',
    valordiaria: null,
    marca: '',
    modelo: '',
    voltagem: null,
    imagemproduto: null,
  };
  fileName.value = '';
  // depois de limpar a imagem tem algum bug que eu n consigo subir outra img
};

const getFileName = (event: any) => {
  const file = event.target.files[0];
  if (file) {
    fileName.value = file.name;
  } else {
    fileName.value = '';
  }
};
</script>

<style lang="scss">
.announce-form {
  border: 1px solid #ccc;
  border-radius: 6px;
  max-width: 25rem;
  height: -webkit-fill-available;
  max-height: 35rem;
  overflow: auto;
}
.image img {
  object-fit: cover;
}

.file-upload input[type='file'] {
  display: none;
}

.file-upload .upload-label {
  display: inline-block;
  padding: 10px 20px;
  background-color: var(--p-primary-color);
  color: #fff;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  width: 190px;
  transition: background-color 0.3s;
}
</style>

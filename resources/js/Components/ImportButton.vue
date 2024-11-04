<template>
  <div>
    <button @click="chooseFile" class="group margin-10 text-white font-bold rounded-xl flex items-center flex-col transition-colors duration-300 ease-in-out">
  <div class="w-96 h-82 border border-black transition hover:scale-105 ease-in-out duration-150 bg-white dark:bg-gray-800 overflow-hidden p-11 sm:rounded-lg group-hover:bg-gray-900 group-hover:dark:bg-white flex flex-col justify-center items-center">
    <div class="flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-36 w-36 text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
      </svg>
    </div>
    <div class="text-4xl text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">Choose File</div>
  </div>
</button>
    
    <div class="w-full flex-col items-center justify-center mb-6">
      <h2 class="flex items-center justify-center text-lg">Upload SQL Backup File</h2>
      
      <div v-if="!fileName" class="flex items-center justify-center text-lg text-gray-600">
        Example: Backup_YYYY-MM-DD_HH-MM-SS.sql
      </div>
      
      <div v-if="fileName" class="flex items-center justify-center text-lg text-gray-600">
        Selected file: {{ fileName }}
      </div>
      <div class="flex items-center justify-center">
        <button @click="openDeleteModal" class="text-lg bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Import Backup
        </button>
      </div>

    </div>
    <input type="file" ref="fileInput" @change="onFileChange" style="display: none;" />
  </div>

  <transition name="modal-fade" >
      <div v-show="showDeleteModal" @click="closeDeleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
          <div @click.stop class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
              <font-awesome-icon icon="fa-solid fa-question" size="8x" style="margin-top:2px; color: blue;"/>
              <h2 class="mt-4 text-xl text-center font-bold mb-2">WARNING: Importing Backup</h2>
              <p class="text-center">Are you sure you want to import this backup?</p>
              <p class="mb-4 text-sm text-center">The contents of your current database will be overwritten.</p>
              <div class="flex justify-center space-x-2">
                  <button @click="closeDeleteModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      No
                  </button>
                  <button @click="uploadFile" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">
                      Yes
                  </button>
              </div>
          </div>
      </div>
  </transition>
  <transition name="modal-fade" >
      <div v-if="showSuccessModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
          <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
              <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
              <h2 class="text-xl font-bold mb-4">Success!</h2>
              <p class="mb-4">The Database has been successfully Restored.</p>
          </div>
      </div>
  </transition>

  
</template>


<script setup>
import { ref } from 'vue';
import axios from 'axios';

const showSuccessModal = ref(false);
const showDeleteModal = ref(false);
const openDeleteModal = () => {
  showDeleteModal.value = true
}
const closeDeleteModal = () => {
  showDeleteModal.value = false
}


const sqlFile = ref(null);
const fileName = ref('');

const fileInput = ref(null);

function chooseFile() {
  fileInput.value.click();
}

function onFileChange(event) {
  sqlFile.value = event.target.files[0];
  fileName.value = sqlFile.value ? sqlFile.value.name : '';
}

function uploadFile() {
  
  if (!sqlFile.value) {
    alert("Please select a file first!");
    return;
  }

  const formData = new FormData();
  formData.append('sql_file', sqlFile.value);

  axios.post('/api/import', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
  .then(() => {
    showDeleteModal.value = false;
            showSuccessModal.value = true;
            setTimeout(() => {
            showSuccessModal.value = false;
            }, 1000) 
  })
  .catch(error => {
    console.error('Error importing file:', error);
  });
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.modal-fade-enter-to,
.modal-fade-leave-from {
  opacity: 1;
  transform: scale(1);
}
</style>
<template>
  <div>
    <button @click="downloadBackup" class="group margin-10 text-white font-bold rounded-xl flex items-center flex-col transition-colors duration-300 ease-in-out">
  <div class="w-96 h-82 border border-black transition hover:scale-105 ease-in-out duration-150 bg-white dark:bg-gray-800 overflow-hidden p-11 sm:rounded-lg group-hover:bg-gray-900 group-hover:dark:bg-white flex flex-col justify-center items-center">
    <div class="flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-36 w-36 text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
      </svg>
    </div>

    <div class="whitespace-nowrap text-4xl text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">Download Backup</div>
  </div>
</button>
  </div>
</template>

<script setup>
import axios from 'axios';

function downloadBackup() {
  const today = new Date();
  const date = `${today.getFullYear()}-${(today.getMonth() + 1).toString().padStart(2, '0')}-${today.getDate().toString().padStart(2, '0')}`;
  const time = `${today.getHours().toString().padStart(2, '0')}-${today.getMinutes().toString().padStart(2, '0')}-${today.getSeconds().toString().padStart(2, '0')}`;
  const filename = `Backup_${date}_${time}.sql`;

  axios.get('/api/backup', { responseType: 'blob' })
    .then(response => {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', filename);
      document.body.appendChild(link);
      link.click();
    })
    .catch(error => {
      console.log('Error downloading the backup:', error);
    });
}
</script>
<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

let businessImage = ref('');
let businessName = ref('');
let isLoading = ref(true);

onMounted(() => {
    getWebsiteInfo();
});

async function getWebsiteInfo() {
    const getBusinessInfo = await axios.get('/api/business_info', {
        params: { user_id: 1 }
    });
    console.log(getBusinessInfo.data);

    if (getBusinessInfo.data.business_image) {
        businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
        isLoading.value = false;
    }
    businessName.value = getBusinessInfo.data.business_Name;
}
</script>

<template>
    <div class="min-h-screen flex flex-col md:flex-row items-stretch overflow-hidden relative"
    style="background: linear-gradient(90deg, rgba(30, 45, 60, 1) 0%, rgba(50, 70, 90, 1) 50%, rgba(20, 60, 80, 1) 100%); color: #1a202c;"
    >
      
      <div style="background-color: #1a202c;" class="absolute top-0 left-0 right-0 h-8 z-10"></div>
      
      <div class="flex-1 flex flex-col justify-center items-center ml-32 p-8">
        <a href="/" class="max-w-lg h-auto flex flex-col items-center">
          <div class="text-white font-bold text-5xl mb-6 text-center">
            {{ businessName }}
          </div>
          <div class="relative flex justify-center items-center">
            <div class="absolute w-[300px] h-[300px] md:w-[400px] md:h-[400px] rounded-full bg-transparent -z-10"
                 style="box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);"></div>
            <img v-if="isLoading" src='/storage/business_logos/default-profile.png' 
                 class="rounded-full shadow-2xl shadow-slate-950" 
                 :style="{ 
                   border: '10px solid #081626', 
                   backgroundColor: '#FFFFFF', 
                   width: '300px', 
                   height: '300px', 
                   objectFit: 'cover',
                   '@media (min-width: 768px)': {
                     width: '400px',
                     height: '400px'
                   }
                 }" />
            <img v-else-if="businessImage" :src='businessImage' 
                 class="rounded-full shadow-2xl shadow-slate-950" 
                 :style="{ 
                   border: '10px solid #081626', 
                   backgroundColor: '#FFFFFF', 
                   width: '300px', 
                   height: '300px', 
                   objectFit: 'cover',
                   '@media (min-width: 768px)': {
                     width: '400px',
                     height: '400px'
                   }
                 }" />
            <img v-else src='/storage/business_logos/default-profile.png' 
                 class="rounded-full shadow-2xl shadow-slate-950" 
                 :style="{ 
                   border: '10px solid #081626', 
                   backgroundColor: '#FFFFFF', 
                   width: '300px', 
                   height: '300px', 
                   objectFit: 'cover',
                   '@media (min-width: 768px)': {
                     width: '400px',
                     height: '400px'
                   }
                 }" />
          </div>
        </a>
      </div>
      
      <div style="background-color: #1a202c;" class="absolute bottom-0 left-0 right-0 h-8 z-10"></div>

      <div class="flex-1 flex justify-center items-center mr-32 p-8">
        <div class="w-full max-w-md px-6 py-12 bg-white shadow-2xl shadow-slate-950 overflow-hidden rounded-3xl"
             style="border: 5px solid #081626;">
          <slot />
        </div>
      </div>
  
    </div>
  </template>

<style scoped>
/* Ensure the layout takes up the full viewport height */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
}
</style>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const textAreas = {
    businessImage: ref(''),
    businessName: ref(''),
    businessDescription: ref(''),
    businessDetails: ref(''),
    homePageImage: ref('')
}
const showSuccessAddModal = ref(false);

onMounted(()=>{
    function insertBreakLines(businessDetails){
        let words = businessDetails.textContent.split(' ');
        let formattedParagraph = '';

        for(let i=0; i<=words.length;i++){
            formattedParagraph+=" " +words[i];
            if((i+1)%11===0){
                formattedParagraph+='<br>';
            }
        }
        businessDetails.innerHTML=formattedParagraph.trim();
    }
    const paragraph = document.getElementById('business-details');
    if(paragraph){
        insertBreakLines(paragraph);
    }
    getWebsiteInfo();
});

//store default / to be changed data:

async function getWebsiteInfo(){
    try{
        const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log(userId);

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        console.log(getBusinessInfo.data);
        const businessId = getBusinessInfo.data.business_id;

        const getWebsiteInfo = await axios.get('/api/website', {
            params: {business_id: businessId}
        });
        console.log(getWebsiteInfo.data);

        textAreas.businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
        textAreas.businessName.value = getBusinessInfo.data.business_Name;
        textAreas.businessDescription.value = getWebsiteInfo.data.website_description;
        textAreas.businessDetails.value = getWebsiteInfo.data.website_details;
        let imgUrl = `/storage/${getWebsiteInfo.data.website_image}`;
        // storage/app/public//app/public/images
        textAreas.homePageImage.value=imgUrl;
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}


const editButton=ref(null);
function edit(area){
    editButton.value = area;
}

async function save(){
    editButton.value = false;

    const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log("Save function called");
    const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        const businessId = getBusinessInfo.data.business_id;

        const formData = new FormData();
        formData.append('business_id', businessId);
        formData.append('website_description', textAreas.businessDescription.value);
        formData.append('website_details', textAreas.businessDetails.value);


        if(uploadedFile){
            const imgFormData = new FormData();
            imgFormData.append('business_id', businessId);
            imgFormData.append('website_image', uploadedFile);

            await axios.post('/api/website-update', imgFormData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        }uploadedFile=null;
    const saveBusinessDesc = await axios.post('/api/website-update', formData, {
        headers:{
            'Content-Type': 'multipart/form-data'
        }
    });
    console.log('Save response:', saveBusinessDesc.data);
    showSuccessAddModal.value = true;
    setTimeout(() => {
        showSuccessAddModal.value = false;
        }, 1000) 
    
}

let uploadedFile = null;
async function imageUpload(event){
    const file = event.target.files[0];
    if (file){
        uploadedFile=file;
        const reader = new FileReader();
        reader.onload = (e) => {
            textAreas.homePageImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
function goToEditWebsite2(){
    Inertia.visit(route('editWebsite2'));
}
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- header of edit website to save changes -->
    
        <!-- <div>
            <button @click="getWebsiteInfo">Show</button>
        </div> -->
     
        <div class="bg-gray-300 flex items-center p-2">
            <p class="flex-grow text-center mr-3">You are currently using the edit mode.</p>
                <button @click="save" class="px-6 py-1 bg-gray-600 ml-auto">Save</button>
        </div>

        <!-- header of business editable template-->
        <div class="ml-1 bg-business-website-header flex items-center p-2">
            <div class="ml-6 w-10 h-10">
                <img :src='textAreas.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-4 ">
                    <a class="text-white">Home</a>
                    <a class="text-white">Chat with Us</a>
                    <a class="text-white">Products & Services</a>
                    <a class="text-white">About Us</a>
                    <p>|</p>
                    <a class="text-white">Register</a>
                    <a class="bg-white border border-white rounded-sm py-1 px-3">Log In</a>
                </div>
        </div>

        <div class="ml-1 bg-website-main flex min-h-screen">
            <!-- edit business info wag to iedit kasi business name ito-->


            <div class="mt-[90px] ml-8 flex-col h-1/2">
                <div>
                    <button @click="edit('businessName')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p class="mt-[10px] text-black text-[29px] font-bold tracking-[3px]">{{textAreas.businessName.value}}</p>
                <div v-if="editButton==='businessName'">
                    <textarea v-model="textAreas.businessName.value" class="rows-2 cols-50 border boder-black"></textarea>
                    <button @click="save" class="bg-white rounded-xl p-1">Save</button>
                </div>

                </div>
                <div class="mt-5">
                    <div class="max-w-[100px]">
                        <button @click="edit('businessDescription')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    </div>
                    <div class="max-w-[550px]">
                    <p class="mt-[10px] font-bold text-xl text-black">{{ textAreas.businessDescription.value }}</p>
                    </div>
                    <div v-if="editButton==='businessDescription'">
                        <textarea v-model="textAreas.businessDescription.value" class="w-full h-[100px] border boder-black"></textarea>
                        <button @click="save" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                </div>
                <div class="mt-5" >
                    <div class="max-w-[100px]">
                        <button @click="edit('businessDetails')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    </div>
                    <div class="max-w-[520px]">
                        <p id="business-details" class="mt-[10px] text-black">{{ textAreas.businessDetails.value }} </p>
                    </div>
                    <div v-if="editButton==='businessDetails'">
                        <textarea v-model="textAreas.businessDetails.value" class="w-full h-[130px] border boder-black"></textarea>
                        <button @click="save" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                    <div class="mt-[30px] flex flex-row">
                    <button class="mr-[20px] cursor-pointer bg-white border border-white rounded-sm py-[8px] px-[50px]">Register</button>
                    <p class="text-black text-xl">|</p>
                    <a class="ml-[35px] justify-center text-black text-[18px]">See All Products</a>
                </div>
                </div>
            
            </div>
            <transition name="modal-fade" >
            <div v-if="showSuccessAddModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Business Information has been successfully Changed!.</p>
                </div>
            </div>
            </transition>

            <!-- image -->
            <div class="mt-[50px] ml-auto flex-grow-0 w-1/2 max-w-lg">
                <div class="mt-2 flex flex-col items-center">
                <button @click="edit('image')" class="bg-white border border-white rounded-xl p-1">Edit Photo</button>
                <img :src='textAreas.homePageImage.value' class ="mr-[8px] mt-8 w-full h-[340px] object-cover rounded-tl-[30px]"/>
                <div v-if="editButton==='image'" class="flex flex-col items-center">
                    <input class="p6 bg-white" type="file" @change="imageUpload"/>
                    <button @click="save" class="mt-5 bg-gray-300 rounded-xl p-1 w-25">Save</button>
                </div>
            </div>
            </div>
        </div>

        <!-- button to next section of homepage -->
        <div class="ml-auto z-50 fixed bottom-4 right-4">
                    <button @click="goToEditWebsite2" class="bg-white border border-black rounded-2xl p-8">
                        <i class="fa fa-arrow-down "></i>
                    </button>
                </div>
    
    </AuthenticatedLayout>

</template>
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

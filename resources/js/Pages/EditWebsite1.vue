<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch, reactive  } from 'vue';
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
    getImages();
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
        
        textAreas.homePageImage.value=imgUrl;
    }
    catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

function base64ToBlob(base64) {
    const [prefix, data] = base64.split(',');
    const mimeType = prefix.match(/:(.*?);/)[1];
    const binary = atob(data);
    const array = [];
    for (let i = 0; i < binary.length; i++) {
        array.push(binary.charCodeAt(i));
    }
    return new Blob([new Uint8Array(array)], { type: mimeType });
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


        if(uploadedFile.value){
            const imgFormData = new FormData();
            imgFormData.append('business_id', businessId);
            imgFormData.append('website_image', uploadedFile.value);

            await axios.post('/api/website-update', imgFormData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        }uploadedFile.value=null;

        if (uploadedFiles.value.length>0) {
            const imgFormData = new FormData();

            uploadedFiles.value.forEach((base64File, index) => {
                // Convert base64 string to Blob
                const blob = base64ToBlob(base64File);
                const fileName = `image${index + 1}.png`; // You can set a custom file name
                imgFormData.append(`image${index + 1}`, blob, fileName);
            });

             console.log('Uploaded Files:', JSON.stringify(uploadedFiles.value, null, 2))

            try {
                const response = await axios.post('/api/images-update', imgFormData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                console.log(response.data.message);
            } catch (error) {
                console.error('Error uploading images:', error);
            }

            showSuccessAddModal.value = true;

            uploadedFiles.value = [];
        }

    const saveBusinessDesc = await axios.post('/api/website-update', formData, {
        headers:{
            'Content-Type': 'multipart/form-data'
        }
    });
    console.log('Save response:', saveBusinessDesc.data);

    
}

let uploadedFile = ref(null);
let uploadedFiles = ref([]);
async function imageUpload(event){
    if(editButton.value==='image'){
        const file = event.target.files[0];
        if (file){
            uploadedFile.value=file;
            const reader = new FileReader();
            reader.onload = (e) => {
                textAreas.homePageImage.value = e.target.result;
            };
            reader.readAsDataURL(file);

        }
        }else if(editButton.value==='slideshow'){
            const files = event.target.files;
            for(let i=0;i<files.length;i++){
                const file = files[i];
                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadedFiles.value.push(e.target.result);
                    images.value.push(e.target.result); 
                };
                reader.readAsDataURL(file);
            }
        }
}

function goToEditWebsite2(){
    Inertia.visit(route('editWebsite2'));
}

const images = reactive({ value: [] });
const currentImage = ref(null);
let currentIndex = 0;
const slideShowClick = ref(null);

const getImages = async () => {
    try {
        const response = await axios.get('/api/images', {
            params: { business_id: 1 }
        });
        
        console.log("Response data: ", response.data);

        if (response.data) {
            for (let i = 1; i <= 5; i++) {
                const imageKey = `image${i}`;
                if(response.data[imageKey]){
                const imagePath = `/storage/${response.data[imageKey]}`;
                console.log("Response data imageKey: ", imagePath);

                if (imagePath && imagePath !== null) {

                    images.value.push(imagePath);
                }
            }
            }
        }

        console.log("Updated images.value: ", images.value);

        // If there are images, set the first one as the current image
        if (images.value.length > 0) {
            currentImage.value = images.value[0];  
        }
    } catch (error) {
        console.error("Error fetching images:", error);
    }
};

watch(() => images.value,  () => {
    if (images.value.length > 0) {
        currentImage.value = images.value[0];  
    }
});
const startSlideshow = () => {
    if (slideShowClick.value === null) {
  slideShowClick.value=setInterval(() => {
    currentIndex = (currentIndex + 1) % images.value.length;
    currentImage.value = images.value[currentIndex];
  }, 1000); 
}
};

const stopSlideshow = () => {
  if (slideShowClick.value) {
    clearInterval(slideShowClick.value);
  }
};

const moveSlideShow=(direction)=>{
    stopSlideshow();
    if(direction=='right'){
        currentIndex = (currentIndex + 1) % images.value.length;
    }else if(direction=='left'){
        currentIndex = (currentIndex - 1 + images.value.length) % images.value.length;
    }
    currentImage.value = images.value[currentIndex];
}
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- header of edit website to save changes -->
     
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

       
        <section>
        <div :style="{ backgroundImage: `url(${textAreas.homePageImage.value})` }" class="bg-no-repeat bg-cover min-h-screen">
            <div class="flex flex-row ">
            <div class="h-auto flex-grow-0 flex-shrink-0">
            <div class="max-w-[880px] mt-[90px] ml-[85px] flex flex-col h-auto flex-shrink-0 rounded-lg p-4">
                <div class="flex-grow-0 max-w-[260px] z-30">
                    <div class="mt-2 flex flex-col">
                    <button @click="edit('image')" class="bg-black text-white text-[19px] border border-white rounded-xl p-2">Edit Background Image</button>
                        <div v-if="editButton==='image'" class="flex flex-col items-center">
                            <input class="p6 bg-white" type="file" @change="imageUpload"/>
                            <button @click="save" class="mt-5 bg-gray-300 rounded-xl p-1 w-25">Save</button>
                        </div>
                    </div>
                </div>
                <div>
                    <h1 class="font-poppins font-bold text-white text-[70px] tracking-[5px]">{{textAreas.businessName.value}}</h1>
                </div>
                <div class="mt-[10px]">
                    <div class="max-w-[550px]">
                    <p class=" font-poppins font-extrabold text-[25px] text-white">{{ textAreas.businessDescription.value }}</p>
                    </div>
                </div>
                <div class="mt-[30px]" >
                    <div class="max-w-[600px]">
                        <p id=" font-poppins business-details" class="text-[19px] text-white">{{ textAreas.businessDetails.value }} </p>
                    </div>
                </div>

                <div class="mt-[50px] flex flex-row ">
                    <a class="text-center rounded-lg p-4 w-[380px] bg-white text-black text-[18px]" :href="route('products_page')">See All Products</a>
                </div>
            </div>
            </div>
            
            
            <div class="flex-grow-0 max-w-[260px] mt-[50px] mr-[10px]">
                    <div class="mt-2 flex flex-col">
                        <button @click="edit('slideshow')" class="bg-black text-white text-[19px] border border-white rounded-xl p-2">Edit Slideshow Image</button>
                            <div v-if="editButton==='slideshow'" class="flex flex-col items-center">
                                <input class="p6 bg-white" type="file" @change="imageUpload" multiple />
                                <button @click="save" class="mt-5 bg-gray-300 rounded-xl p-1 w-25">Save</button>
                            </div>

                            <div v-if="editButton==='slideshow'" class="mt-3 grid grid-cols-3 gap-2">
                                <img v-for="(image, index) in uploadedFiles" :key="index" :src="image" class="w-24 h-24 object-cover rounded-md" />
                            </div>
                    </div>
            </div>

            <div class="mr-[45px] mt-[75px] ml-auto relative flex-grow-0 max-w-2xl z-20">
                <a class="absolute top-1/2 right-0 mr-[10px] cursor-pointer" @click="moveSlideShow('right')"><i class="text-white text-[80px] fas fa-angle-right z-20"></i></a>
                <a class="absolute top-1/2 left-0 ml-[10px] cursor-pointer" @click="moveSlideShow('left')"><i class="text-white text-[80px] fas fa-angle-left z-20"></i></a>
                <img :src='currentImage' class ="mt-8 w-[800px] h-[605px] object-cover rounded-[5px] z-10"/>
            </div>
        </div>
        </div>

        <transition name="modal-fade" >
            <div v-if="showSuccessAddModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Slideshow is updated! Please Refresh the Page.</p>
                </div>
            </div>
            </transition>
        </section>

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

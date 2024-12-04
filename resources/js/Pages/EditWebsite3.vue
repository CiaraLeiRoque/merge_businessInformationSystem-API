<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const textAreas = {
    card1: ref(''),
    card1_img: ref(''),
    card2: ref(''),
    card2_img: ref(''),
    card3: ref(''),
    card3_img: ref(''),
    card4: ref(''),
    card4_img: ref(''),
    card5: ref(''),
    card5_img: ref(''),
    card6: ref(''),
    card6_img: ref('')
    
}
const showSuccessAddModal = ref(false);
const feature_toggle=ref('true');
const onSale_toggle=ref('true');
const package_toggle=ref('true');
onMounted(()=>{

    getWebsiteInfo();
});


async function getWebsiteInfo(){
    try{
        const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log(userId);

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        const businessId = getBusinessInfo.data.business_id;

        const getProductsInfo = await axios.get('/api/featured-products', {
            params: {business_id: businessId}
        });
        console.log('Products Info:', getProductsInfo.data);

        const getWebsiteInfo = await axios.get('/api/website', {
            params: {business_id: businessId}
        });
        feature_toggle.value=getWebsiteInfo.data.featured_section;
        package_toggle.value=getWebsiteInfo.data.package_section;
        onSale_toggle.value=getWebsiteInfo.data.onSale_section;

        const featuredProducts = getProductsInfo.data.slice(0, 6);
        console.log('featuredProducts Info:', featuredProducts);

        if(featuredProducts.length<5){
            console.error('Featured Products must be 6 in number');
        }else{
            
        featuredProducts.forEach((product, index) => {
            const imgPath = product.product_img.replace('products/', '');
        if (index === 0) {
            textAreas.card1.value = product.product_name;
            textAreas.card1_img.value = imgPath;
        } else if (index === 1) {
            textAreas.card2.value = product.product_name;
            textAreas.card2_img.value = imgPath;
        } else if (index === 2) {
            textAreas.card3.value = product.product_name;
            textAreas.card3_img.value = imgPath;
        } else if (index === 3) {
            textAreas.card4.value = product.product_name;
            textAreas.card4_img.value = imgPath;
        } else if (index === 4) {
            textAreas.card5.value = product.product_name;
            textAreas.card5_img.value = imgPath;
        }else if (index === 5) {
            textAreas.card6.value = product.product_name;
            textAreas.card6_img.value = imgPath;
        }
        });
    }

    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}
async function save(){


    const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
    const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        const businessId = getBusinessInfo.data.business_id;

        const formData = new FormData();
        formData.append('business_id', businessId);
        formData.append('featured_section', feature_toggle.value);
        formData.append('package_section', package_toggle.value)
        formData.append('onSale_section', onSale_toggle.value);

        console.log("Form Data being sent:");
    for (let pair of formData.entries()) {
        console.log(`${pair[0]}: ${pair[1]}`);
    }

    const saveBusinessDesc = await axios.post('/api/website-update', formData, {
        headers:{
            'Content-Type': 'multipart/form-data'
        }
    });

    const getWebsiteInfo = await axios.get('/api/website', {
            params: {business_id: businessId}
        });
        console.log('Website Info: ',getWebsiteInfo.data);
        showSuccessAddModal.value = true;
    setTimeout(() => {
        showSuccessAddModal.value = false;
        }, 1000) 
    
}

function goToEditWebsite4(){
    Inertia.visit(route('editWebsite4'));
}
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        
        <div class="ml-1 bg-website-main flex flex-col min-h-screen relative">

        <div class="ml-auto mt-6 mr-5 p-2 flex flex-col items-end w-[500px] h-1/2 bg-white border-white rounded-lg">
            <div class="flex items-end justify-end space-x-2 w-full">
            <p class=" text-lg text-black">Display Featured Products Section on Website</p>
            <label class="switch">
                <input type="checkbox" v-model="feature_toggle" true-value="true" false-value="false" />
                <span class="slider round"></span>
            </label>
            <span class="text-base text-black">
                {{ feature_toggle === 'true' ? 'Show' : 'Don\'t Show' }}
            </span>
            </div>

            <div class="flex items-end justify-end space-x-2 w-full">
            <p class=" text-lg text-black">Display Product Packages Section on Website</p>
            <label class="switch">
                <input type="checkbox" v-model="package_toggle" true-value="true" false-value="false" />
                <span class="slider round"></span>
            </label>
            <span class="text-base text-black">
                {{ package_toggle === 'true' ? 'Show' : 'Don\'t Show' }}
            </span>
            </div>

            <div class="flex items-end justify-end space-x-2 w-full">
            <p class=" text-lg text-black">Display On Sale Products Section on Website</p>
            <label class="switch">
                <input type="checkbox" v-model="onSale_toggle" true-value="true" false-value="false" />
                <span class="slider round"></span>
            </label>
            <span class="text-base text-black">
                {{ onSale_toggle === 'true' ? 'Show' : 'Don\'t Show' }}
            </span>
            </div>
                
                <button @click="save" class="mt-6 text-white px-6 py-1 bg-gray-600 ml-auto">Save</button>
                
            </div>

            <div class="flex flex-col items-center p-3 relative top-[10px] left-0 right-0 bottom-[300px] m-auto">
                <p class="mt-[10px] text-[40px]  text-black font-bold  text-center">Featured Products</p>
                <p class="mt-[10px] text-[20px]  text-black  text-center">
                    A list of the most popular products loved by customers. 
                    Best prices guaranteed everyday.
                </p>
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
            <div class="mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg px-4 pt-[70px] pb-[70px]">
                
                <div class="block flex flex-row gap-5">
                <!-- card 1 -->
                    <div class="flex flex-col w-[320px] h-[380px] rounded-xl shadow-lg bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 transition-transform duration-300 hover:scale-105">
                    <div class="w-full bg-gray-800 h-[35px]"></div>
                        <img :src="`/storage/products/${textAreas.card1_img.value}`" class="w-full h-5/6 object-cover"/>
                        <p class="text-white text-[20px] h-1/6 mt-[20px] text-center">{{textAreas.card1.value}}</p>
                    </div>

                 <!-- card 2 -->
                 <div class="flex flex-col w-[320px] h-[380px] rounded-xl shadow-lg bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 transition-transform duration-300 hover:scale-105">
                 <div class="w-full bg-gray-800 h-[35px]"></div>
                    <img :src="`/storage/products/${textAreas.card2_img.value}`" class="w-full h-5/6 object-cover"/>
                        <p class="text-white text-[20px] h-1/6 mt-[20px] text-center">{{textAreas.card2.value}}</p>
                    </div>
                

                <!-- card 3 -->
                <div class="flex flex-col w-[320px] h-[380px] rounded-xl shadow-lg bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 transition-transform duration-300 hover:scale-105">
                <div class="w-full bg-gray-800 h-[35px]"></div>
                    <img :src="`/storage/products/${textAreas.card3_img.value}`" class="w-full h-5/6 object-cover"/>
                        <p class="text-white text-[20px] h-1/6 mt-[20px] text-center">{{textAreas.card3.value}}</p>
                    </div>
                </div>

                <div class="block flex flex-row gap-5">
                <!-- card 4 -->
                <div class="flex flex-col w-[320px] h-[380px] rounded-xl shadow-lg bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 transition-transform duration-300 hover:scale-105">
                <div class="w-full bg-gray-800 h-[35px]"></div>
                    <img :src="`/storage/products/${textAreas.card4_img.value}`" class="w-full h-5/6 object-cover"/>
                        <p class="text-white text-[20px] h-1/6 mt-[20px] text-center">{{textAreas.card4.value}}</p>
                    </div>

                <!-- card 5 -->
                <div class="flex flex-col w-[320px] h-[380px] rounded-xl shadow-lg bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 transition-transform duration-300 hover:scale-105">
                <div class="w-full bg-gray-800 h-[35px]"></div>
                    <img :src="`/storage/products/${textAreas.card5_img.value}`" class="w-full h-5/6 object-cover"/>
                        <p class="text-white text-[20px] h-1/6 mt-[20px] text-center">{{textAreas.card5.value}}</p>
                    </div>

                <!-- card 6 -->
                <div class="flex flex-col w-[320px] h-[380px] rounded-xl shadow-lg bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 transition-transform duration-300 hover:scale-105">
                <div class="w-full bg-gray-800 h-[35px]"></div>
                    <img :src="`/storage/products/${textAreas.card6_img.value}`" class="w-full h-5/6 object-cover"/>
                        <p class="text-white text-[20px] h-1/6 mt-[20px] text-center">{{textAreas.card6.value}}</p>
                    </div> 
                </div>
            </div>
        </div>

        <section v-if="feature_toggle === 'true'">
  <div
    class="flex flex-col pb-44 items-center relative"
    style="background-color: #1A202C;"
  >
    <!-- Title Section -->
    <div class="pt-28 flex flex-col items-center p-3 mb-20">
      <p
        class="font-poppins mt-[10px] text-[65px] text-white font-bold text-center leading-tight"
      >
        Check Out Our Packages/Bundles!
      </p>
      <p class="mt-[10px] text-[24px] text-gray-300 text-center max-w-2xl">
        Take a look at our product packages. Best prices <br> guaranteed everyday.
      </p>
    </div>

    <!-- Product Packages -->
    <div class="flex flex-wrap justify-start gap-8 w-full max-w-screen px-4">
      <div class="flex flex-col w-[420px] h-auto rounded-xl shadow-2xl bg-gradient-to-br from-gray-100 via-gray-200 to-gray-100 border border-gray-300 transition-all duration-300 hover:scale-105 hover:shadow-3xl overflow-hidden group">
        <!-- Header -->
        <div class="w-full bg-gradient-to-r from-gray-200 to-gray-300 h-[50px] flex items-center justify-center sticky top-0 z-10 transition-colors duration-300 group-hover:from-gray-300 group-hover:to-gray-400">
          <p style="font-weight: 900;" class="text-gray-800 text-[30px] tracking-wide">
            Product Package Sample 1
          </p>
        </div>
        <!-- Centered List -->
        <div class="flex flex-grow flex-col items-center justify-center p-6">
          <ul class="space-y-4">
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 1<span class="text-gray-500">(x9)</span></span>
            </li>
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 2<span class="text-gray-500">(x9)</span></span>
            </li>
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 3<span class="text-gray-500">(x9)</span></span>
            </li>
          </ul>
        </div>
        <!-- Footer -->
        <p class="text-gray-800 text-[20px] h-[60px] text-center bg-gradient-to-r from-gray-200 to-gray-300 rounded-b-xl flex items-center justify-center sticky bottom-0 z-10 transition-colors duration-300 group-hover:from-gray-300 group-hover:to-gray-400"></p>
      </div>

      <!-- Repeat above card for other packages -->
      <div class="flex flex-col w-[420px] h-auto rounded-xl shadow-2xl bg-gradient-to-br from-gray-100 via-gray-200 to-gray-100 border border-gray-300 transition-all duration-300 hover:scale-105 hover:shadow-3xl overflow-hidden group">
        <!-- Header -->
        <div class="w-full bg-gradient-to-r from-gray-200 to-gray-300 h-[50px] flex items-center justify-center sticky top-0 z-10 transition-colors duration-300 group-hover:from-gray-300 group-hover:to-gray-400">
          <p style="font-weight: 900;" class="text-gray-800 text-[30px] tracking-wide">
            Product Package Sample 2
          </p>
        </div>
        <!-- Centered List -->
        <div class="flex flex-grow flex-col items-center justify-center p-6">
          <ul class="space-y-4">
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 1<span class="text-gray-500">(x9)</span></span>
            </li>
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 2<span class="text-gray-500">(x9)</span></span>
            </li>
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 3<span class="text-gray-500">(x9)</span></span>
            </li>
          </ul>
        </div>
        <!-- Footer -->
        <p class="text-gray-800 text-[20px] h-[60px] text-center bg-gradient-to-r from-gray-200 to-gray-300 rounded-b-xl flex items-center justify-center sticky bottom-0 z-10 transition-colors duration-300 group-hover:from-gray-300 group-hover:to-gray-400"></p>
      </div>

            <!-- Repeat above card for other packages -->
            <div class="flex flex-col w-[420px] h-auto rounded-xl shadow-2xl bg-gradient-to-br from-gray-100 via-gray-200 to-gray-100 border border-gray-300 transition-all duration-300 hover:scale-105 hover:shadow-3xl overflow-hidden group">
        <!-- Header -->
        <div class="w-full bg-gradient-to-r from-gray-200 to-gray-300 h-[50px] flex items-center justify-center sticky top-0 z-10 transition-colors duration-300 group-hover:from-gray-300 group-hover:to-gray-400">
          <p style="font-weight: 900;" class="text-gray-800 text-[30px] tracking-wide">
            Product Package Sample 3
          </p>
        </div>
        <!-- Centered List -->
        <div class="flex flex-grow flex-col items-center justify-center p-6">
          <ul class="space-y-4">
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 1<span class="text-gray-500">(x9)</span></span>
            </li>
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 2<span class="text-gray-500">(x9)</span></span> 
            </li>
            <li class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]">
              <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"><span class="w-3 h-3 rounded-full bg-gray-600"></span></span>
              <span>Product Name Sample 3<span class="text-gray-500">(x9)</span></span>
            </li>
          </ul>
        </div>
        <!-- Footer -->
        <p class="text-gray-800 text-[20px] h-[60px] text-center bg-gradient-to-r from-gray-200 to-gray-300 rounded-b-xl flex items-center justify-center sticky bottom-0 z-10 transition-colors duration-300 group-hover:from-gray-300 group-hover:to-gray-400"></p>
      </div>

      <!-- Repeat for other cards -->
    </div>
  </div>
</section>


        <!-- button to next section of homepage -->
        <div class="ml-auto z-50 fixed bottom-4 right-4">
                    <button @click="goToEditWebsite4()" class="bg-white border border-black rounded-2xl p-8">
                        <i class="fa fa-arrow-down "></i>
                    </button>
                </div>
    
    </AuthenticatedLayout>

</template>

<style scoped>

.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 12px;
    width: 12px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #4CAF50;
}

input:checked + .slider:before {
    transform: translateX(20px);
}
.icon-color {
    background-color: #306091; /* Replace with your desired color */
}
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

<script setup>
import { text } from '@fortawesome/fontawesome-svg-core';
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref, reactive } from 'vue';
import{usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

const businessInfo = {
    businessImage: ref(''),
    businessName: ref(''),
    business_Email: ref(''),
    business_Contact_Number: ref(''),
    business_Telephone_Number: ref(''),
    business_Address: ref(''),
    business_Facebook: ref(''),
    business_X: ref(''),
    business_Instagram: ref(''),
    business_Tiktok: ref(''),
    website_footNote: ref(''),
    business_Province: ref(''),
    business_City: ref(''),
    business_Barangay: ref('')
}
const {props} = usePage();
let profile_img = ref('');
const profilePicture = ref(null);

const formatUrl = (url) => {
    // Check if the URL starts with http:// or https://
    if (!/^https?:\/\//i.test(url)) {
        // Attach https:// if it doesn't
        return `https://${url}`;
    }
    return url;
};

const textAreas = reactive({
    products: [],
    website_footNote: ''
});

let currentPage = ref(0);  

const PRODUCTS_PER_PAGE = 9;

const onSale_toggle=ref('');
let next = ref(false);
const lengthArray = ref(null);
let isLoading = ref(true);
let buttonVisible = ref(true);
let productToDisplay = ref([]);
let userLogIn = ref(false);

function logout(button){
    Inertia.post(route('logout'), {button});
}
function account(){
    Inertia.visit(route('account_settings'));
}
onMounted(()=>{
    getWebsiteInfo();
})

function chunkArray(array, chunkSize) {
            const result = [];
            for (let i = 0; i < array.length; i += chunkSize) {
                result.push(array.slice(i, i + chunkSize));
            }
            return result;
        }

function showMore(){
    currentPage.value+=1;
    if(lengthArray.value<=9){
        alert(`There are only ${lengthArray.value} products to be displayed`);
        buttonVisible.value=false;
    }else{
        fetchProducts();
    }
}

async function getWebsiteInfo(){
    try{

        const response = await axios.get('/showUser');
        if (response.data) {
            profilePicture.value = response.data.profile_img 
        ? `/storage/user_profile/${response.data.profile_img}` 
        : '/storage/user_profile/default-profile.png';
            isLoading.value=false;
        }
        if(props.auth.user){
            userLogIn.value=true;
        }

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });

        const getWebsiteInfo1 = await axios.get('/api/website', {
            params: {business_id: 1}
        });
        console.log('Business data: ',getBusinessInfo.data);
        
        console.log('Website data: ',getWebsiteInfo1.data);

        businessInfo.businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
        businessInfo.businessName.value = getBusinessInfo.data.business_Name;
        businessInfo.business_Email.value = getBusinessInfo.data.business_Email;
        businessInfo.business_Contact_Number.value = getBusinessInfo.data.business_Contact_Number;
        businessInfo.business_Telephone_Number.value = getBusinessInfo.data.business_Telephone_Number;
        businessInfo.business_Address.value = getBusinessInfo.data.business_Address;

        businessInfo.business_Facebook.value = getBusinessInfo.data.business_Facebook;
        businessInfo.business_X.value = getBusinessInfo.data.business_X;
        businessInfo.business_Instagram.value = getBusinessInfo.data.business_Instagram;
        businessInfo.business_Tiktok.value = getBusinessInfo.data.business_Tiktok;

        businessInfo.business_Province.value = getBusinessInfo.data.business_Province;
        businessInfo.business_City.value = getBusinessInfo.data.business_City;
        businessInfo.business_Barangay.value = getBusinessInfo.data.business_Barangay;

        onSale_toggle.value = getWebsiteInfo1.data.onSale_section;
        businessInfo.website_footNote.value = getWebsiteInfo1.data.website_footNote;

        await fetchProducts();

    
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

async function fetchProducts() {
        const getProductsInfo = await axios.get('/api/listed-products', {
            params: {business_id: 1}
        });
        console.log('Fetched Products: ', getProductsInfo.data); 
        const listedProducts = getProductsInfo.data
        lengthArray.value = listedProducts.length;
        console.log("listedProducts: ", listedProducts);

        if(lengthArray.value > 0){
            const start = currentPage.value * PRODUCTS_PER_PAGE;
            const end = start + PRODUCTS_PER_PAGE;

            productToDisplay.value = listedProducts.slice(start, end);
            console.log("productToDisplay products: ",productToDisplay.value);

            if (productToDisplay.value && productToDisplay.value.length > 0) {
            productToDisplay.value.forEach(element => {
                textAreas.products.push({
                    name: element.product_name,
                    img: element.product_img.replace('products/', ''),
                    desc: element.product_desc,
                    price: element.product_price
                })
                
            });
        } else {
                console.error('No products to display, productToDisplay is:', productToDisplay.value);
            }
            if(textAreas.products.length >= lengthArray.value){
                buttonVisible.value=false;
            }
            
        }
}

const formatDescription = (description) => {
  return description.replace(/\n/g, '<br>');
}
</script>

<template>
    <Head title="Products" />
        <!-- header -->
        <div class="bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('homepage')">Home</a>
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('chat_with_us')">Chat with Us</a>
                    <a class="text-black rounded-3xl bg-white px-4 py-2 text-[18px] cursor-pointer" :href="route('products_page')">Products</a>
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('aboutUs_page')">About Us</a>
                    <p>|</p>
                    <div v-if="userLogIn===true" class="flex flex-col">
                        <a @click="logout('logout')" class=" cursor-pointer text-white text-[14px] underline">Log Out</a>
                        <a @click="account" class=" cursor-pointer text-white text-[14px] underline">Account</a>
                    </div>
                    <div v-else-if="userLogIn===false" class="space-x-[40px] mr-[40px]">
                        <a class="text-white text-[18px] cursor-pointer" :href="route('login')">Log In</a>
                        <a class="text-white text-[18px] cursor-pointer" :href="route('register')">Register</a>
                    </div>
                    <div class="w-[50px] h-[50px]">
                        <img v-if="isLoading" src='/storage/user_profile/default-profile.png'/>
                        <img v-else-if="profilePicture" :src='profilePicture' alt="Logo" class="h-full object-cover rounded-full" />
                        <img v-else src='/storage/user_profile/default-profile.png'/>
                    </div>
                    
                </div>
        </div>

       


    <!-- products -->
    <section>
        <div class=" bg-website-main flex min-h-screen relative" style="min-height: calc(100vh + 800px);">

<div class="flex flex-col items-center p-3 absolute top-[10px] left-0 right-0 bottom-[500px] m-auto">
    <p class="mt-[30px] text-[60px]  text-black font-bold  text-center">Products</p>
    <p class="mt-[10px] text-[20px]  text-black  text-center">
        A list of all the quality products provided. 
        Best prices and quality guaranteed.
    </p>
    <div v-if="onSale_toggle==='true'">
    <a  class="mt-[10px] text-[20px] text-black  text-center" :href="route('sale')"><u>Check out our on SALE products</u></a>
</div>
</div>

<div class="mt-[30px] mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg mt px-4 pt-[200px]">
    <div v-for="(row, rowIndex) in chunkArray(textAreas.products, 3)" :key="rowIndex" class="flex justify-between gap-6 items-start justify-center ">
        <div v-for="(product, index) in row" :key="index" class="flex flex-col w-[340px] h-auto min-h-[400px] flex-shrink-0 rounded-xl flex-shrink-0 rounded-xl shadow-lg overflow-hidden bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 transition-transform duration-300 hover:scale-105">
            <div class="relative">
                <img :src="`/storage/products/${product.img}`" class="w-full h-[320px] object-cover" alt="Product image" />
                <div class="absolute top-0 left-0 right-0 p-4 flex justify-between items-start">
                    <h3 class="text-white text-lg font-semibold bg-black bg-opacity-50 px-2 py-1 rounded">{{ product.name }}</h3>
                    <span class="text-white text-lg font-bold bg-black bg-opacity-50 px-2 py-1 rounded">{{ product.price }}</span>
            </div>
                <div class="p-4 text-center flex-grow">
                    <p class="text-center text-gray-300 text-sm" v-html="formatDescription(product.desc)"></p>
            </div>
        </div>


    </div>

    <div>
        <button v-if="buttonVisible===true" @click="showMore" class="cursor-pointer bg-white border border-white rounded-sm py-6 px-9">Show More</button>
    </div>
</div>


        </div>
        </div>
    </section>

    <section>
        <div class="bg-website-main1 flex flex-col min-h-screen" style="min-height: calc(70vh);">

            <!-- Footnote -->
            <div class="mt-[50px] w-full">
                <hr class="border-white mx-auto w-11/12">
            </div>
        <div class="flex flex-row justify-between ml-8 mr-8 w-full">
        <!-- FootNote -->
        <div class="mr-auto mt-[100px] ml-8 flex flex-col h-1/2 w-1/2 max-w-md">
            <div class="max-w-[50px]">
        <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
    </div>

            <div class="mt-5">
                <p class=" text-xl text-white">{{website_footNote }}</p>
                <div class="mt-[20px]">
                <a :href="formatUrl(businessFacebook)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-facebook-f"></i></a>
                <a :href="formatUrl(businessX)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa fa-twitter"></i></a>
                <a :href="formatUrl(businessInstagram)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-instagram"></i></a>
                <a :href="formatUrl(businessTiktok)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

        </div>

        <!-- Contact Us -->
        <div class="mt-[50px] ml-auto flex flex-grow-0 w-1/2 max-w-md w-1/2 max-w-md">
            <div class="mt-2 flex flex-col ">
        <p class="text-white font-bold text-[50px]">Contact Us</p>
        <p class="text-white mt-[10px]">Email: {{ businessInfo.business_Email }} </p>
        <p class="text-white">Contact No.: {{ businessInfo.business_Contact_Number }} </p>
        <p class="text-white">Address: {{ businessInfo.business_Address }} </p>
        <p class="text-white">{{ businessInfo.business_Province }}, 
            {{ businessInfo.business_City }}, {{ businessInfo.business_Barangay }}  </p>
        <p class="text-white">Telephone No.: {{ businessInfo.business_Telephone_Number }} </p>
    </div>
        </div>
        </div>

        <div class="mt-[60px] w-full">
            <hr class="border-white mx-auto w-11/12">
        </div>

        <div class="ml-[60px] mr-auto w-full">
            <p class="text-[17px] text-white mt-2"><i class="fa fa-copyright"></i> {{ businessName }} All rights reserved</p>
        </div>
        </div>

</section>
</template>
<style scoped>

body, html {
  overflow-x: hidden;
  width: 100%;
  margin: 0;
  padding: 0;
}

.bg-website-main,
.bg-website-main1 {
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}

/* Ensure all sections have full width */
section {
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}
.icon-color {
    background-color: #306091;
}
.fa.fa-twitter{
	font-family:sans-serif;
}
.fa.fa-twitter::before{
	content:"𝕏";
	font-size:1.2em;
}
</style>
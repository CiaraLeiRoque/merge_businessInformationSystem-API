<script setup>
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref } from 'vue';
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
    businessDescription: ref(''),
    businessDetails: ref(''), 
    business_Province: ref(''),
    business_City: ref(''),
    business_Barangay: ref('')
}
const {props} = usePage();
let profile_img = ref('');
const profilePicture = ref(null);
let isLoading = ref(true);
let userLogIn = ref(false);

const formatUrl = (url) => {
    // Check if the URL starts with http:// or https://
    if (!/^https?:\/\//i.test(url)) {
        // Prepend https:// if it doesn't
        return `https://${url}`;
    }
    return url;
};

function account(){
    Inertia.visit(route('account_settings'));
}
const textAreas = {
    about_us1: ref(''),
    about_us2: ref(''),
    about_us3: ref(''),
    website_footNote: ref('')
}

function logout(button){
    Inertia.post(route('logout'), {button});
}

onMounted(()=>{
    loadMap();
    getWebsiteInfo();
})

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

        businessInfo.business_Province.value = getBusinessInfo.data.business_Province;
        businessInfo.business_City.value = getBusinessInfo.data.business_City;
        businessInfo.business_Barangay.value = getBusinessInfo.data.business_Barangay;

        businessInfo.business_Facebook.value = getBusinessInfo.data.business_Facebook;
        businessInfo.business_X.value = getBusinessInfo.data.business_X;
        businessInfo.business_Instagram.value = getBusinessInfo.data.business_Instagram;
        businessInfo.business_Tiktok.value = getBusinessInfo.data.business_Tiktok;

        businessInfo.businessDescription.value = getWebsiteInfo1.data.website_description;
        businessInfo.businessDetails.value = getWebsiteInfo1.data.website_details;


        
       
        textAreas.about_us1.value = getWebsiteInfo1.data.about_us1;
        textAreas.about_us2.value = getWebsiteInfo1.data.about_us2;
        textAreas.about_us3.value = getWebsiteInfo1.data.about_us3;
        textAreas.website_footNote.value = getWebsiteInfo1.data.website_footNote;


    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

function loadMap() {
  const mapOptions = {
    center: { lat: 14.862140, lng: 120.817826}, 
    zoom: 15,
  };

  const map = new google.maps.Map(document.getElementById("map"), mapOptions);

  // Optional: Add a marker
  new google.maps.Marker({
    position: { lat: 14.862140, lng: 120.817826 }, 
    map: map,
    title: "Limesen Network Solutions Inc.",
  });
}
</script>

<template>
    <Head title="About Us" />
        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('homepage')">Home</a>
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('chat_with_us')">Chat with Us</a>
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('products_page')">Products & Services</a>
                    <a class="text-black rounded-3xl bg-white px-4 py-2 text-[18px] cursor-pointer" :href="route('aboutUs_page')">About Us</a>
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

        <!-- section 2/EditWebsite2 -->
<section>
        
<div class=" bg-website-main flex min-h-screen relative">
        <div class="flex items-center p-3 absolute top-[5px] left-0 right-0 bottom-[500px] m-auto">
            <p class="mt-[10px] text-[70px] tracking-[3px] text-black font-bold flex-grow text-center">About Us</p>
        </div>

    <!-- edit business info wag to iedit kasi business name ito-->
    <div class=" mx-auto flex flex-row items-center justify-between w-full max-w-screen-lg mt-[250px]">
        
        <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
            <div class="flex justify-center w-full">
                <a class=" border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
                    <i class="fa fa-check-circle text-black text-[50px]"></i></a>
            </div>
            <div class="max-w-[330px] min-h-[170px] mt-[100px]">
                <p class="text-black text-[19px] text-center break-words">{{textAreas.about_us1}}</p>
            </div>
        </div>

        <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3 mx-[100px]">
            <div class="flex justify-center w-full">
                <a class=" border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
                <i class="fa fa-tag text-black text-[50px]"></i></a>
            </div>
            <div class="max-w-[330px] min-h-[170px] mt-[100px]">
                <p class="text-black text-[19px] text-center break-words">{{textAreas.about_us2}}</p>
            </div>
        </div>

        <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
            <div class="flex justify-center w-full">
                <a class=" border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
                <i class="fa fa-phone text-black text-[40px]"></i></a>
            </div>
            <div class="max-w-[330px] min-h-[170px] mt-[100px]">
                <p class="text-black text-[19px] text-center break-words">{{textAreas.about_us3}}</p>
            </div>
        </div>
    </div>
</div>

        <!-- Map Section -->
        <div class="map-section h-[500px] w-full mb-10">
            <div id="map" class="h-full w-full"></div>
        </div>
</section>

<section>
        <div class="bg-website-main1 flex flex-col min-h-screen" style="min-height: calc(70vh);">
            <div class="w-full">
                <hr class="border-white mx-auto w-11/12">
            </div>
<div class="flex flex-row justify-between mt-[5px] ml-8 mr-8 w-full">
<!-- FootNote -->
<div class="mr-auto mt-[100px] ml-8 flex flex-col h-1/2 w-1/2 max-w-md">
    <div class="max-w-[50px]">
        <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
    </div>

    <div class="mt-5">
        <p class=" text-xl text-white">{{ textAreas.website_footNote }}</p>
        <div class="mt-[20px]">
        <a :href="formatUrl(businessInfo.business_Facebook.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-facebook-f"></i></a>
        <a :href="formatUrl(businessInfo.business_X.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa fa-twitter"></i></a>
        <a :href="formatUrl(businessInfo.business_Instagram.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-instagram"></i></a>
        <a :href="formatUrl(businessInfo.business_Tiktok.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-tiktok"></i></a>
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
    <p class="text-[17px] text-white mt-2"><i class="fa fa-copyright"></i> {{ textAreas.businessName }} All rights reserved</p>
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
    background-color: ghostwhite; 
}
.fa.fa-twitter{
	font-family:sans-serif;
}
.fa.fa-twitter::before{
	content:"𝕏";
	font-size:1.2em;
}

#map {
  margin: 0 auto;
  padding: 0;
  width: 80%;
  height: 500px;
  border: 1px solid #ccc;
}
</style>
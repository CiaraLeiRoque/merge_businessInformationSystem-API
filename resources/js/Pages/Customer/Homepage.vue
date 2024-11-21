<script setup>
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref } from 'vue';
import Chatbot from '@/Components/Chatbot.vue';
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
    homePageImage: ref(''),
    business_Province: ref(''),
    business_City: ref(''),
    business_Barangay: ref('')
}

let isLoading = ref(true);


const textAreas = {
    about_us1: ref(''),
    about_us2: ref(''),
    about_us3: ref(''),
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
    card6_img: ref(''),
    website_footNote: ref('')
}

const feature_toggle=ref('');
const onSale_toggle=ref('');
let profile_img = ref('');
const profilePicture = ref(null);
let userLogIn = ref(false);

function logout(button){
    Inertia.post(route('logout'), {button});
}

function account(){
    Inertia.visit(route('account_settings'));
}

function goTochatPage(){
    Inertia.visit(route('chat_with_us'));
}


onMounted(()=>{
    loadMap();
    getWebsiteInfo();
  window.addEventListener('scroll', handleScroll);
})

async function getWebsiteInfo(){
    try{
  
        const response = await axios.get('/showUser');
        if (response.data) {
            profilePicture.value = response.data.profile_img 
        ? `/storage/user_profile/${response.data.profile_img}` 
        : '/storage/user_profile/default-profile.png';
            isLoading.value=false;
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
        const imgUrl = `/storage/${getWebsiteInfo1.data.website_image}`;
        businessInfo.homePageImage.value=imgUrl;

        
        feature_toggle.value = getWebsiteInfo1.data.featured_section;
        onSale_toggle.value = getWebsiteInfo1.data.onSale_section;
        textAreas.about_us1.value = getWebsiteInfo1.data.about_us1;
        textAreas.about_us2.value = getWebsiteInfo1.data.about_us2;
        textAreas.about_us3.value = getWebsiteInfo1.data.about_us3;
        textAreas.website_footNote.value = getWebsiteInfo1.data.website_footNote;

        const getProductsInfo = await axios.get('/api/featured-products', {
            params: {business_id: 1}
        });

        const featuredProducts = getProductsInfo.data.slice(0, 6);

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

const formatUrl = (url) => {
    // Check if the URL starts with http:// or https://
    if (!/^https?:\/\//i.test(url)) {
        // Prepend https:// if it doesn't
        return `https://${url}`;
    }
    return url;
};


const isVisible = ref(false);
const aboutSection = ref(null);

const handleScroll = () => {
  if (aboutSection.value) {
    const rect = aboutSection.value.getBoundingClientRect();
    if (rect.top <= window.innerHeight - 100) {
      isVisible.value = true;
      window.removeEventListener('scroll', handleScroll);
    }
  }
};



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
    <Head title="Home" />
        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-black rounded-3xl bg-white px-4 py-2 text-[18px] cursor-pointer" :href="route('homepage')">Home</a>
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('chat_with_us')">Chat with Us</a>
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('products_page')">Products</a>
                    <a class="text-white rounded-3xl px-4 py-2 transition ease-in-out duration-150 hover:bg-white hover:text-black text-[18px] cursor-pointer" :href="route('aboutUs_page')">About Us</a>
                    <p class="text-white">|</p>
                    <div v-if="userLogIn" class="flex flex-col">
                        <a @click="logout('logout')" class=" cursor-pointer text-white text-[14px] underline">Log Out</a>
                        <a @click="account" class=" cursor-pointer text-white text-[14px] underline">Account</a>
                    </div>
                    <div v-else>
                        <a class="text-white text-[18px] cursor-pointer" :href="route('login')">Log In &nbsp; &nbsp;</a>
                        <a class="text-white text-[18px] cursor-pointer" :href="route('register')">Register</a>
                    </div>
                    <div class="w-[50px] h-[50px]">
                        <img v-if="isLoading" src='/storage/user_profile/default-profile.png'/>
                        <img v-else-if="profilePicture" :src='profilePicture' alt="Logo" class="h-full object-cover rounded-full" />
                        <img v-else src='/storage/user_profile/default-profile.png'/>
                    </div>
                    
                </div>
        </div>

        <!-- section 1/EditWebsite1 -->
        <section>
        <div style="background-color: ghostwhite" class=" flex min-h-screen">

            <div class="mt-[230px] ml-[80px] flex-col h-1/2">
                <div>
                    <h1 class="font-black text-black text-[60px] tracking-[5px]">{{businessInfo.businessName.value}}</h1>
                </div>
                <div class="mt-[10px]">
                    <div class="max-w-[550px]">
                    <p class="font-extrabold text-[25px] text-black">{{ businessInfo.businessDescription.value }}</p>
                    </div>
                </div>
                <div class="mt-[30px]" >
                    <div class="max-w-[550px]">
                        <p id="business-details" class="text-[19px] text-black">{{ businessInfo.businessDetails.value }} </p>
                    </div>
                </div>

                <div class="mt-[90px] flex flex-row">
                    <button @click="logout('register')"  class="transition ease-in-out duration-150 hover:text-black bg-gray-800 hover:bg-white text-white border border-black mt-[-5px] mr-[20px] cursor-pointer shadow-m rounded-lg py-[8px] px-[70px]">Register</button>
                    <p class="text-black text-xl">|</p>
                    <a class="ml-[35px] justify-center text-black text-[18px]" :href="route('products_page')">See All Products</a>
                </div>
            </div>


            <!-- image -->
            <div class=" mt-[50px] ml-auto flex-grow-0 w-1/2 max-w-4xl">
                
                <img :src='businessInfo.homePageImage.value' class ="mt-8 w-full h-[690px] object-cover rounded-tl-[125px]"/>
            </div>
        </div>
        </section>

        <!-- section 2/EditWebsite2 -->
        <section 
        ref="aboutSection" 
        class="-mt-[100px] bg-website-main1 border-t border-b border-gray-200 flex flex-col h-auto relative transition-transform duration-500"
        :class="{'translate-active': isVisible, 'translate-custom': !isVisible}"
        >
        <div class="mt-[50px] text-center">
            <p class="text-[70px] tracking-[3px] text-white font-bold flex-grow text-center">About Us</p>
        </div>

        <div class="mx-auto flex flex-row items-center justify-between w-full max-w-screen-lg mt-[100px]">
            <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
            <div class="flex justify-center w-full">
                <a class="icon-color border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
                <i class="fa fa-check-circle text-gray-800 text-[50px]"></i>
                </a>
            </div>
            <div class="max-w-[330px] min-h-[170px] mt-[100px]">
                <p class="text-white text-[19px] text-center break-words">{{ textAreas.about_us1 }}</p>
            </div>
            </div>

            <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3 mx-[100px]">
            <div class="flex justify-center w-full">
                <a class="icon-color border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
                <i class="fa fa-tag text-gray-800 text-[50px]"></i>
                </a>
            </div>
            <div class="max-w-[330px] min-h-[170px] mt-[100px]">
                <p class="text-white text-[19px] text-center break-words">{{ textAreas.about_us2 }}</p>
            </div>
            </div>

            <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
            <div class="flex justify-center w-full">
                <a class="icon-color border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
                <i class="fa fa-phone text-gray-800 text-[40px]"></i>
                </a>
            </div>
            <div class="max-w-[330px] min-h-[170px] mt-[100px]">
                <p class="text-white text-[19px] text-center break-words">{{ textAreas.about_us3 }}</p>
            </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section h-[500px] w-full mb-10">
            <div id="map" class="h-full w-full"></div>
        </div>
        </section>



    <!-- section 3/EditWebsite3 -->
    
    <section v-if="feature_toggle==='true'">
        <div class=" bg-website-main mb-20 flex min-h-screen relative" style="min-height: calc(100vh + 100px);">

<div class="flex flex-col items-center p-3 absolute top-[10px] left-0 right-0 bottom-[500px] m-auto">
    <p class="mt-[30px] text-[55px]  text-black font-bold  text-center">Featured Products</p>
    <p class="mt-[10px] text-[20px]  text-black  text-center">
        A list of the most popular products loved by customers. 
        Best prices guaranteed everyday.
    </p>
</div>

<!-- edit business info wag to iedit kasi business name ito-->
<div class=" mt-8 mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg mt-[10px] px-4 pt-[200px]">
    
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
    </section>

    <!-- section 4/Chat Section -->
<section class="border-t border-gray-200 shadow-sm">
        <div style="background-color: ghostwhite" class="flex flex-col min-h-screen">

        
    <div class="flex w-full justify-center items-center p-3">
        <p class="mt-[30px] text-[60px] tracking-[3px] text-black font-bold flex-grow text-center">Connect with Us!</p>
    </div>

<div class="flex flex-row items-center">
            <!-- image -->
    <div class="ml-[150px] mt-[15px] mr-auto w-1/2 max-w-xl">
        <img src="/storage/images/chat_img_homepage.jpg" class ="mt-6 h-[600px] w-[800px] object-cover rounded-[20px]"/>
    </div>  

    <div class="-mt-[30px] flex items-center justify-center mr-[70px] w-1/2 ml-auto flex-col h-1/2">
            <p class="text-black text-[30px] text-center mr-[50px]">
            Using our chatbot, you can inquire about our business information,             
            and to further reach us please refer to our contact information.
            </p>
            <button @click="goTochatPage"  class="mt-8 transition ease-in-out duration-150 hover:text-black bg-gray-800 hover:bg-white text-white border border-black mr-[20px] cursor-pointer shadow-m rounded-lg py-[8px] px-[70px]">See Chatbot!</button>
            <!-- <img src="/storage/images/chat_icon.png" 
            class =" mx-auto mt-[90px] w-[88px] h-[120px] object-cover cursor-pointer"
            @click="goTochatPage"/> -->
            
    </div>
</div>
</div>

    </section>

    <section>
        <div class="bg-website-main1 flex flex-col min-h-screen" style="min-height: calc(70vh);">

            <div class="w-full">
                <hr class="border-white mx-auto w-11/12">
            </div>
<div class="flex flex-row justify-between mt-[5px] ml-8 mr-8 w-full">
<!-- FootNote -->
<div class="mr-auto mt-40 ml-8 flex flex-col h-1/2 w-1/2 max-w-md">
    <div class="max-w-[50px]">
        <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
    </div>

    <div class="mt-5">
        <p class=" text-xl text-white">{{ textAreas.website_footNote }}</p>
        <div class="mt-[20px]">
        <a :href="formatUrl(businessInfo.business_Facebook.value)" class="w-[30px] h-[40px] bg-white rounded-full mr-[5px] px-3 py-2 cursor-pointer"><i class="fa-brands fa-facebook-f"></i></a>
        <a :href="formatUrl(businessInfo.business_X.value)" class="w-[30px] h-[40px] bg-white rounded-full mr-[5px] px-3 py-2 cursor-pointer"><i class="fa fa-twitter"></i></a>
        <a :href="formatUrl(businessInfo.business_Instagram.value)" class="w-[30px] h-[40px] bg-white rounded-full mr-[5px] px-3 py-2 cursor-pointer"><i class="fa-brands fa-instagram"></i></a>
        <a :href="formatUrl(businessInfo.business_Tiktok.value)" class="w-[30px] h-[40px] bg-white rounded-full mr-[5px] px-3 py-2 cursor-pointer"><i class="fa-brands fa-tiktok"></i></a>
        </div>
    </div>

</div>

<!-- Contact Us -->
<div class="mt-[100px] ml-auto flex flex-grow-0 w-1/2 max-w-md w-1/2 max-w-md">
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

<Chatbot />

</section>
</template>
<style scoped>
.translate-custom {
  transform: translateY(50px);
  opacity: 0;
}
.translate-active {
  transform: translateY(0);
  opacity: 1;
}
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
    background-color: ghostwhite; /* Replace with your desired color */
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
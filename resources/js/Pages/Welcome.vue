<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { onMounted, onBeforeUnmount, ref, watch, reactive } from 'vue';
import Chatbot from '@/Components/Chatbot.vue';
import { Swiper, SwiperSlide } from 'swiper/vue';  // Correctly import Swiper and SwiperSlide as named imports
import 'swiper/css';  // Import Swiper styles
import 'swiper/css/navigation';  // Navigation styles
import 'swiper/css/pagination'; 
import ErrorToast from '@/Components/ErrorToast.vue';
import SuccessToast from '@/Components/SuccessToast.vue';

const{props} = usePage();
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

function goTochatPage(){
    Inertia.visit(route('chat_with_us'));
}

//subscribers   
const email = ref('');

const subscribe = async () => {
    if (!email.value) {
        alert('Please enter a valid email address.');
        return;
    }

    try {
        const response = await axios.post('/api/subscribe', { email: email.value });
        showToast("Email verification link sent", "success");
        email.value = ''; // Reset the input field
    } catch (error) {
        alert(error.response?.data?.message || 'Subscription failed. Please try again.');
    }
};

const showErrorToast = ref(false);
const showSuccessToast = ref(false);
const toastMessage = ref('');

const showToast = (message, type) => {
  toastMessage.value = message;
  if (type === 'error') showErrorToast.value = true;
  if (type === 'success') showSuccessToast.value = true;
  setTimeout(() => {
    showErrorToast.value = false;
    showSuccessToast.value = false;
  }, 3000);
};

const businessInfo = {
    businessImage: ref(''),
    businessName: ref(''),
    business_Email: ref(''),
    business_Contact_Number: ref(''),
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

let user_type=ref('');
const feature_toggle=ref('');
const onSale_toggle=ref('');
let profile_img = ref('');
const profilePicture = ref(null);

function logout(button){
    Inertia.post(route('logout'), {button});
}

function account(){
    Inertia.visit(route('account_settings'));
}

onMounted(()=>{
    loadMap();
    getWebsiteInfo();
    startSlideshow();
    getImages();
    fetchPackageInfo();
    startAutoLoop();
  window.addEventListener('scroll', handleScroll);
})
onBeforeUnmount(() => {
  stopAutoLoop();
});



async function getWebsiteInfo(){
    try{

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });

        if(props.auth.user){
        const getUserInfo = await axios.get('/auth_user', {
            
        });
         user_type.value=getUserInfo.data.user_type;
         console.log("user type", user_type.value);
    }

        
        const getWebsiteInfo1 = await axios.get('/api/website', {
            params: {business_id: 1}
        });
        console.log('Business data: ',getBusinessInfo.data);
        
        console.log('Website data: ',getWebsiteInfo1.data);

        businessInfo.businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
       

        businessInfo.businessName.value = getBusinessInfo.data.business_Name;
        businessInfo.business_Email.value = getBusinessInfo.data.business_Email;
        businessInfo.business_Contact_Number.value = getBusinessInfo.data.business_Phone_Number;
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

        console.log("Updated images.value: ", JSON.stringify(images.value));

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
const packageData = ref([]);



const textAreasPackage = {
  card1Package: ref(''),
  card1PackageName: ref(''),
  card2Package: ref(''),
  card2PackageName: ref(''),
  card3Package: ref(''),
  card3PackageName: ref(''),
  card4Package: ref(''),
  card4PackageName: ref(''),
  card5Package: ref(''),
  card5PackageName: ref(''),
  card6Package: ref(''),
  card6PackageName: ref(''),
};

const fetchPackageInfo = async () => {
  try {
    const response = await axios.get('/api/productPackageNamesWithProducts');
    console.log('getPackageInfo data', response.data);
    packageData.value = response.data;
  } catch (error) {
    console.error('There was an error fetching the data:', error);
  }
};










const packageWidth = 420; // Card width
const gap = 32; // Gap between cards
const currentIndexCarousel = ref(0);
const autoLoopInterval = ref(null);

const nextSlide = () => {
  currentIndexCarousel.value =
    (currentIndexCarousel.value + 1) % packageData.value.length; // Loop back to the first slide
};

const prevSlide = () => {
  currentIndexCarousel.value =
    (currentIndexCarousel.value - 1 + packageData.value.length) %
    packageData.value.length; // Loop back to the last slide
};

const startAutoLoop = () => {
  autoLoopInterval.value = setInterval(() => {
    nextSlide();
  }, 3000); // Change slide every 3 seconds
};

const stopAutoLoop = () => {
  clearInterval(autoLoopInterval.value);
};


</script>

<template>
    <Head title="Home" />
            <ErrorToast
                v-if="showErrorToast"
                :visible="showErrorToast"
                :message="toastMessage"
                @close="showErrorToast = false"
            />
            <SuccessToast
                v-if="showSuccessToast"
                :visible="showSuccessToast"
                :message="toastMessage"
                @close="showSuccessToast = false"
            />
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
                    <div>
                        <a class="text-white text-[18px] cursor-pointer" :href="route('login')">Log In</a>
                    </div>
                    <div class="w-[50px] h-[50px]">
                        <img v-if="isLoading" src='/storage/user_profile/default-profile.png'/>
                        <img v-else-if="profilePicture" :src='profilePicture' alt="Logo" class="h-full object-cover rounded-full" />
                        <img v-else src='/storage/user_profile/default-profile.png'/>
                    </div>
                    
                </div>
        </div>

        <!-- section 1 -->
        <section>
        <div :style="{ backgroundImage: `url(${businessInfo.homePageImage.value})` }" class="bg-no-repeat bg-cover min-h-screen">
            <div class="flex flex-row ">
            <div class="h-auto flex-grow-0 flex-shrink-0">
            <div class="max-w-[980px] mt-[100px] ml-[105px] flex flex-col h-auto flex-shrink-0 rounded-lg p-4">
                <div>
                    <h1 class="font-poppins font-bold text-white text-[80px] tracking-[5px]">{{businessInfo.businessName.value}}</h1>
                </div>
                <div class="mt-[20px]">
                    <div class="max-w-[550px]">
                    <p class=" font-poppins font-extrabold text-[35px] text-white">{{ businessInfo.businessDescription.value }}</p>
                    </div>
                </div>
                <div class="mt-[30px]" >
                    <div class="max-w-[690px]">
                        <p id=" font-poppins business-details" class="text-[29px] text-white">{{ businessInfo.businessDetails.value }} </p>
                    </div>
                </div>

                <div class="mt-[50px] flex flex-row ">
                    <a class="font-poppins  text-center rounded-lg p-4 w-[380px] bg-white text-black text-[21px]" :href="route('products_page')">See All Products</a>
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

        </section>


        <!-- section 2-->
        <!-- FEATURED PRODUCTS -->
        <section v-if="feature_toggle==='true'">
            <div class=" flex h-[1150px] relative" style="background-color: ghostwhite ;min-height: calc(100vh + 100px);">

    <div class="flex flex-col items-center pt-[80px] p-3 absolute top-[10px] left-0 right-0 bottom-[500px] m-auto">
        <p class="font-poppins mt-[10px] text-[55px]  text-black font-bold  text-center">Featured Products</p>
        <p class="mt-[10px] text-[20px]  text-black  text-center">
            A list of the most popular products loved by customers. 
            Best prices guaranteed everyday.
        </p>
    </div>

    <!-- edit business info wag to iedit kasi business name ito-->
    <div class=" mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg mt-[80px] px-4 pt-[200px]">
        
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


        <!-- section 3-->
        <!-- PRODUCT PACKAGES -->
        <section v-if="feature_toggle === 'true'">
          <div class="flex flex-col pb-44 items-center relative" style="background-color: #1A202C;">
            <!-- Title Section -->
            <div class="pt-28 flex flex-col items-center p-3 mb-20">
              <p class="font-poppins mt-[10px] text-[65px] text-white font-bold text-center leading-tight">
                Check Out Our Packages/Bundles!
              </p>
              <p class="mt-[10px] text-[24px] text-gray-300 text-center max-w-2xl">
                Take a look at our product packages. Best prices <br> guaranteed everyday.
              </p>
            </div>

            <!-- Product Packages Carousel -->
            <div class="carousel-container relative w-full max-w-7xl overflow-hidden">
              <div
                class="carousel-track flex gap-8 transition-transform duration-500 ease-in-out"
                :style="{ transform: `translateX(calc(-${currentIndexCarousel} * (${packageWidth}px + ${gap}px) + 50% - ${packageWidth / 2}px))` }"
              >
                <div
                  v-for="(packageItem, index) in packageData"
                  :key="packageItem.id"
                  class="carousel-item flex flex-col h-[400px] w-[420px] rounded-xl shadow-2xl bg-gradient-to-br from-gray-100 via-gray-200 to-gray-100 border border-gray-300 transition-all duration-300 hover:scale-105 hover:shadow-3xl group"
                >
                  <!-- Header -->
                  <div
                    class="w-full bg-gradient-to-r from-gray-200 to-gray-300 h-[50px] flex items-center justify-center sticky top-0 z-10 transition-colors duration-300 group-hover:from-gray-300 group-hover:to-gray-400"
                  >
                    <p style="font-weight: 900;" class="text-gray-800 text-[30px] tracking-wide">
                      {{ packageItem.product_package_name }}
                    </p>
                  </div>

                  <!-- Centered List -->
                  <div class="flex flex-grow flex-col items-center justify-center p-6">
                    <ul class="space-y-4">
                      <li
                        v-for="product in packageItem.packages"
                        :key="product.id"
                        class="flex items-center justify-start text-left space-x-3 text-gray-800 text-[18px]"
                      >
                        <span
                          class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center"
                        >
                          <span class="w-3 h-3 rounded-full bg-gray-600"></span>
                        </span>
                        <span>
                          {{ product.product_name }}
                          <span class="text-gray-500">(x{{ product.product_quantity }})</span>
                        </span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Carousel Controls -->
              <button
                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-700 text-white rounded-full p-3"
                @click="prevSlide"
              >
                ◀
              </button>
              <button
                class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-700 text-white rounded-full p-3"
                @click="nextSlide"
              >
                ▶
              </button>
            </div>
          </div>
        </section>










        <!-- section 4 -->
        <section 
        ref="aboutSection" 
        class="mb-30 bg-website-main border-t border-b border-gray-200 flex flex-col h-auto relative transition-transform duration-500"
        :class="{'translate-active': isVisible, 'translate-custom': !isVisible}"
        >
        <div class="mt-[50px] text-center">
            <p class="font-poppins text-[70px] tracking-[3px] text-black font-bold flex-grow text-center">About Us</p>
        </div>

    <div class="mx-auto flex flex-row items-center justify-between w-full max-w-screen-lg mt-[180px] pb-[110px]">
      <div class="flex -mt-[20px] max-h-[350px] flex-col items-center space-y-4 w-1/3">
        <div class="flex justify-center w-full">
          <a class="icon-color border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
            <i class="fa fa-check-circle text-gray-800 text-[70px]"></i>
          </a>
        </div>
        <div class="max-w-[330px] min-h-[170px] mt-[100px]" :class="{'translate-active': isVisible, 'translate-custom': !isVisible}">
          <p class="text-black text-[22px] text-center break-words">{{ textAreas.about_us1 }}</p>
        </div>
      </div>

      <div class="flex -mt-[20px] max-h-[350px] flex-col items-center space-y-4 w-1/3 mx-[100px]">
        <div class="flex justify-center w-full">
          <a class="icon-color border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
            <i class="fa fa-tag text-gray-800 text-[70px]"></i>
          </a>
        </div>
        <div class="max-w-[330px] min-h-[170px] mt-[100px]" :class="{'translate-active': isVisible, 'translate-custom': !isVisible}">
          <p class="text-black text-[22px] text-center break-words">{{ textAreas.about_us2 }}</p>
        </div>
      </div>

            <div class="flex -mt-[20px] max-h-[350px] flex-col items-center space-y-4 w-1/3">
            <div class="flex justify-center w-full">
                <a class="icon-color border border-gray-400 rounded-[30px] p-12 flex inline-flex items-center justify-center">
                <i class="fa fa-phone text-gray-800 text-[70px]"></i>
                </a>
            </div>
            <div class="max-w-[330px] min-h-[170px] mt-[100px]" :class="{'translate-active': isVisible, 'translate-custom': !isVisible}">
                <p class="text-black text-[22px] text-center break-words">{{ textAreas.about_us3 }}</p>
            </div>
            </div>
        </div>
        </section>



    <section>
        <div class="bg-website-main1 pb-20" >
            <!-- Map Section -->
        <div class="mt-[65px] text-center mb-20 mx-auto flex flex-col">
            <p class="font-poppins text-[70px] tracking-[3px] text-white font-bold flex-grow text-center">Visit Us</p>
            <p class="text-[20px] text-white flex-grow text-center">{{ businessInfo.business_Address }}</p>
            <p class="text-[20px] text-white">{{ businessInfo.business_Province }}, 
                {{ businessInfo.business_City }}, {{ businessInfo.business_Barangay }}  </p>
        </div>
        <div class="-mt-[60px] map-section h-[700px] w-full mb-[100px]">
            <div id="map" class="h-full w-full"></div>
        </div>
        </div>
    </section>


    <!-- section /Chat Section -->
<section class="border-t border-gray-200 shadow-sm">
        <div style="background-color: ghostwhite" class="flex flex-col min-h-screen">

        
    <div class="flex w-full justify-center items-center p-3">
        <p class="font-poppins mt-[30px] text-[60px] tracking-[3px] text-black font-bold flex-grow text-center">Connect with Us!</p>
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

<div class="mr-auto mt-40 ml-8 flex flex-col max-w-md">
    <p class="text-[17px] text-white mt-2"> Subscribe to Us!</p>

    <div class="flex items-center space-x-4 mt-4">
        <input 
            v-model="email" 
            type="email" 
            placeholder="Enter your email" 
            class="px-3 py-2 rounded-lg text-black focus:outline-none"
        />
        <button 
            @click="subscribe" 
            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600"
        >
            Subscribe
        </button>
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
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';
@layer utilities {
  .scrollbar-thin {
    scrollbar-width: thin;
  }
  .scrollbar-thumb-gray-600::-webkit-scrollbar-thumb {
    background-color: #4B5563;
  }
  .scrollbar-track-gray-900::-webkit-scrollbar-track {
    background-color: #111827;
  }
}

.carousel-container {
  position: relative;
  overflow: hidden;
}

.carousel-track {
  display: flex;
  transition: transform 0.5s ease-in-out;
}

.carousel-item {
  flex-shrink: 0;
  width: 420px; /* Ensure consistent card width */
  scroll-snap-align: center;
}

.swiper {
  width: 100%;
  padding: 0 20px;
}

.packages-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Poppins', sans-serif;
}

.packages-title {
  font-size: 2.5rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 2rem;
  color: #333;
}

.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.package-card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  transition: transform 0.3s ease;
}

.package-card:hover {
  transform: translateY(-5px);
}

.package-name {
  font-size: 1.5rem;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 1rem;
}

.package-items {
  list-style-type: none;
  padding: 0;
}

.package-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #eee;
}

.package-item:last-child {
  border-bottom: none;
}

.item-name {
  font-weight: 500;
  color: #34495e;
}

.item-quantity {
  color: #7f8c8d;
}

.no-packages {
  text-align: center;
  font-size: 1.2rem;
  color: #7f8c8d;
}

@media (max-width: 768px) {
  .packages-grid {
    grid-template-columns: 1fr;
  }
}

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
.bg-cover {
  transition: background-image 1s ease-in-out;
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
  height: 750px;
  border: 1px solid #ccc;
  border-radius: 10px;
}
</style>

<template>
    <AuthenticatedLayout>
        <div class="flex flex-row">
            <div class="w-full sm:px-6 lg:px-8 py-6 flex flex-col" style="width: 60vw;">
                <div class="bg-whiteoverflow-hidden shadow-sm sm:rounded-lg" style="background-color: #0F2C4A;">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col items-center">
                        <div class="flex flex-row justify-center"><div class="flex flex-col text-center mr-20"><div style="font-size: 18px;"><b>Total Income (30 Days)</b></div><div style="font-size: 25px">₱ {{ totalIncome }}</div></div>
                        <div class="flex flex-col text-center"><div style="font-size: 18px;"><b>Total Expenses (30 Days)</b></div><div style="font-size: 25px">₱ {{ totalExpenses }}</div></div></div>
                        <div class="flex flex-row justify-center">
                            <button style="background-color: #FFFFFF; border-radius: 14px;">
                                <ResponsiveNavLink :href="route('finance')" :active="route().current('finance')" style="color: #0F2C4A; font-size: 12px;">View Finance</ResponsiveNavLink>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="flex flex-row justify-center w-full">
                    <div class="inventory_table m-4 table_container max-w-4xl">
                        <div class="flex md:flex-row justify-between px-4 py-2">
                            <div style="font-size: 23px;"><b>Inventory</b></div>
                                <button>
                                    <ResponsiveNavLink :href="route('inventory')" :active="route().current('inventory')" style="color: white; background-color: #0F2C4A; border-radius: 14px; font-size: 12px;">View all products</ResponsiveNavLink>
                                </button>
                        </div>
                        <div class="overflow-x-auto w-full">
                        <table>
                            <tr style="font-size: 1rem; background-color: white">
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Sold</th>
                                <th>Stock</th>
                            </tr>
                            <tr v-for="product in filteredProducts.slice(0, 3)" :key="product.id">
                                <td >{{ product.name }}</td>
                                <td>{{ product.category }}</td>
                                <td>{{ product.price }}</td>
                                <td>{{ product.sold }}</td>
                                <td>{{ product.stock }}</td>
                            </tr>
                        </table>
                    </div>
                    </div>
                </div>                
                    <!-- Visitors & Views and Retention Rate Charts side by side -->
                    <div class="flex flex-row justify-center px-8 space-x-6 mt-2"> 
                        <!-- Visitors & Views Chart -->
                        <div class="custom-chart-width p-4 border border-black rounded-lg w-full" style="height: 275px;"> 

                            <canvas id="visitorsViewsChart" class="w-full" style="height: 150px;"></canvas> 
                        </div>

                        <!-- Retention Rate Chart -->
                        <div class="custom-chart-width p-4 border border-black rounded-lg w-full" style="height: 275px;"> 
                            <canvas id="engagementRateChart" class="w-full" style="height: 150px;"></canvas> 
                        </div>
                    </div>
                </div>

            <!-- Right-side Content -->
            <div class="flex flex-col">
                <vue-cal hide-view-selector :time="false" active-view="month" xsmall class="p-6 max-h-[400px]" style="background-color: #0F2C4A; margin-right: 30px; margin-top: 25px; color: white; font-weight: bold; border-radius: 1rem;">
                    <template #arrow-prev>
                        <i class="fa-solid fa-arrow-left"></i>
                    </template>
                    <template #arrow-next>
                        <i class="fa-solid fa-arrow-right"></i>
                    </template>
                </vue-cal>
                <div class="flex flex-col items-center mt-4 text-white">
                    <h3 style="color: black"><b>Social Media</b></h3>
                    <div class="flex flex-row">
                            <a v-if="business_Facebook" :href="business_Facebook" target="_blank" class="p-3 mx-3 h-20 min-w-36 bg-blue-800 mt-2 flex flex-col justify-center items-center rounded-2xl">
                                <i class="fa-brands fa-facebook-f"></i><br>Facebook
                            </a>
                            <a v-else class="p-3 mx-3 h-20 min-w-36 bg-blue-800 mt-2 flex flex-col justify-center items-center rounded-2xl" :href="route('BusinessInfo')" :active="route().current('BusinessInfo')">
                                <i class="fa-brands fa-facebook-f"></i><br>Facebook
                            </a>
                            <a v-if="business_X" :href="business_X" target="_blank" class="p-3 mx-3 h-20 min-w-36 bg-blue-400 mt-2 flex flex-col justify-center items-center rounded-2xl">
                                <i class="fa-brands fa-twitter"></i><br>Twitter
                            </a>
                            <a v-else class="p-3 mx-3 h-20 min-w-36 bg-blue-400 mt-2 flex flex-col justify-center items-center rounded-2xl" :href="route('BusinessInfo')" :active="route().current('BusinessInfo')">
                                <i class="fa-brands fa-twitter"></i><br>Twitter
                            </a>
                    </div>
                    <div class="flex flex-row">
                            <a v-if="business_Instagram" :href="business_Instagram" target="_blank" class="p-3 mx-3 h-20 min-w-36 bg-pink-700 mt-2 flex flex-col justify-center items-center rounded-2xl">
                                <i class="fa-brands fa-instagram"></i><br>Instagram
                            </a>
                            <a v-else class="p-3 mx-3 h-20 min-w-36 bg-pink-700 mt-2 flex flex-col justify-center items-center rounded-2xl" :href="route('BusinessInfo')" :active="route().current('BusinessInfo')">
                                <i class="fa-brands fa-instagram"></i><br>Instagram
                            </a>
                            <a v-if="business_Tiktok" :href="business_Tiktok" target="_blank" class="p-3 mx-3 h-20 min-w-36 bg-black mt-2 flex flex-col justify-center items-center rounded-2xl">
                                <i class="fa-brands fa-tiktok"></i><br>Tiktok
                            </a> 
                            <a v-else class="p-3 mx-3 h-20 min-w-36 bg-black mt-2 flex flex-col justify-center items-center rounded-2xl" :href="route('BusinessInfo')" :active="route().current('BusinessInfo')"><i class="fa-brands fa-tiktok"></i><br>Tiktok</a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import DoughnutChart from '@/Components/DoughnutChart.vue';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

// Product Data and Modal Management
const products = ref([]);
const showAddProductModal = ref(false);
const showEditProductModal = ref(false);
const listedCategories = ref([]);
const searchQuery = ref('');
const newProduct = ref({ /* your product structure */ });
const editProduct = ref({ /* your edit structure */ });
const imagePreviewUrl = ref(null);
const editImagePreviewUrl = ref(null);

// Analytics Data
let visitorsViewsChart = null;
let engagementRateChart = null;

const selectedPeriod = ref(7);
const views = ref(0);
const visits = ref(0);
const visitors = ref(0);
const bounceRate = ref(0);
const avgVisitTime = ref('');
const engagementRate = ref(0);

const dailyData = ref([]);

const fetchAnalyticsData = async () => {
    try {
        const { data } = await axios.get(`/api/analytics`, {
            params: { period: selectedPeriod.value },
        });

        views.value = data.metricsData.views;
        visits.value = data.metricsData.visits;
        visitors.value = data.metricsData.visitors;
        bounceRate.value = data.metricsData.bounceRate;
        avgVisitTime.value = data.metricsData.avgVisitTime;
        engagementRate.value = data.metricsData.engagementRate;

        dailyData.value = data.dailyData;

        renderVisitorsViewsChart();
        renderEngagementRateChart();
    } catch (error) {
        console.error("Error fetching analytics data", error);
    }
};

const renderVisitorsViewsChart = () => {
    // If chart already exists, destroy it before creating a new one
    if (visitorsViewsChart) {
        visitorsViewsChart.destroy();
    }

    const labels = dailyData.value.map(data => data.date);
    const viewsData = dailyData.value.map(data => data.views);
    const visitorsData = dailyData.value.map(data => data.visitors);

    const ctx = document.getElementById("visitorsViewsChart").getContext("2d");
    visitorsViewsChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Visitors",
                    data: visitorsData,
                    backgroundColor: "#5E9FF2",
                    borderColor: "#5E9FF2",
                    borderWidth: 1,
                    stack: 'combined',
                },
                {
                    label: "Views",
                    data: viewsData,
                    backgroundColor: "#3F6CA6",
                    borderColor: "#3F6CA6",
                    borderWidth: 1,
                    stack: 'combined',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    beginAtZero: true,
                    stacked: true,
                },
            },
        plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true, // Use circles instead of rectangles for legend icons
                        pointStyle: 'circle',
                    },
                },
            },
        },
    });
};

const renderEngagementRateChart = () => {
    if (engagementRateChart) {
        engagementRateChart.destroy();
    }

    const labels = dailyData.value.map(data => data.date);
    const engagementRateData = dailyData.value.map(data => data.engagementRate);

    const ctx = document.getElementById("engagementRateChart").getContext("2d");
    engagementRateChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Engagement Rate",
                    data: engagementRateData,
                    backgroundColor: "#E0DFFD",
                    borderColor: "#27378F",
                    fill: true,
                    tension: 0.1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        maxRotation: 15, // Set max rotation
                        minRotation: 15, // Set min rotation to make them diagonal
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => `${value}%`,
                    },
                },
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
            },
        },
    });
};

watch(selectedPeriod, async () => {
    await fetchAnalyticsData();  // Fetch new data based on selected period
    renderVisitorsViewsChart();  // Re-render the chart with new data
    renderEngagementRateChart();
});


// Fetch data initially and refetch on period change
onMounted(fetchAnalyticsData);

// Fetch Products and Categories
const fetchProducts = async () => {
    try {
        const response = await axios.get('/api/products');
        products.value = response.data;
    } catch (error) {
        console.error("Error fetching products:", error);
    }
};

const fetchListedCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        listedCategories.value = response.data.filter(category => category.status === 'listed');
    } catch (error) {
        console.error("Error fetching categories:", error);
    }
};

// Filtering and Sorting Products
const filteredProducts = computed(() => {
    if (!searchQuery.value) return products.value;
    return products.value.filter(product =>
        product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Function to calculate total products sold each month
const soldProductsByMonth = computed(() => {
    const monthlySales = {};

    products.value.forEach(product => {
        const soldDate = new Date(product.soldDate);  // assuming product has soldDate property
        const month = soldDate.toLocaleString('default', { month: 'long', year: 'numeric' });

        if (!monthlySales[month]) {
            monthlySales[month] = 0;
        }
        monthlySales[month] += product.sold;
    });

    return monthlySales;
});

const isDateFiltered = ref(false);
const startDate = ref('');
const endDate = ref('');
const financesByDate = ref([]);
const totalExpenses = ref(0);
const totalIncome = ref(0);
const roundToTwoDecimals = (num) => Math.round(num * 100) / 100;

const formatDate = (date) => {
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
    const day = String(date.getDate()).padStart(2, '0');
    const year = date.getFullYear();
    return `${month}/${day}/${year}`;
};

const setDateRange = () => {
    const currentDate = new Date();
    const pastDate = new Date();
    pastDate.setDate(currentDate.getDate() - 30);

    startDate.value = formatDate(pastDate);
    endDate.value = formatDate(currentDate);
};

const fetchFinancesByDate = async () => {
    setDateRange(); // Set the start and end dates before fetching

    try {
        const response = await axios.get('/api/finance_by_date', {
            params: {
                start_date: startDate.value,
                end_date: endDate.value
            }
        });
        financesByDate.value = response.data;
        isDateFiltered.value = true; // Set flag to true after fetching by date

        for (const finance of financesByDate.value) {
            if (finance.category === 'income') {
                totalIncome.value += roundToTwoDecimals(finance.amount);
            }
            if (finance.category === 'expense') {
                totalExpenses.value += roundToTwoDecimals(finance.amount);
            }
        }

        console.log("Finances by date:", financesByDate);
        console.log("Total Income:", totalIncome.value);
        console.log("Total Expenses:", totalExpenses.value);

    } catch (error) {
        console.error("Error fetching finances by date:", error);
    }
};
        const business_Facebook = ref('');
        const business_X = ref('');
        const business_Instagram = ref('');
        const business_Tiktok = ref('');

const fetchsocialmediaLinks = async() =>{
    try {const response_userId = await axios.get('/user-id');
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
        business_Facebook.value = validateAndFormatUrl(getBusinessInfo.data.business_Facebook, "facebook.com");
        business_X.value = validateAndFormatUrl(getBusinessInfo.data.business_X, "X.com");
        business_Instagram.value = validateAndFormatUrl(getBusinessInfo.data.business_Instagram, "Instagram.com");
        business_Tiktok.value = validateAndFormatUrl(getBusinessInfo.data.business_Tiktok, "Tiktok.com");

        console.log("Social Media Links:", business_Facebook.value, business_X.value, business_Instagram.value, business_Tiktok.value);
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}
function validateAndFormatUrl(url, baseUrl) {
    // Check if url already starts with "https://www."
    const isValidLink = url.startsWith(`https://www.${baseUrl}`);
    
    // If valid, return the URL as is
    if (isValidLink) {
        console.log("This link is valid");
        return url;
    } else {
        // Else, construct the full URL with the base URL + string
        console.log("This link is not valid");
        return `https://www.${baseUrl}/${url}`;
    }
}

fetchsocialmediaLinks();
fetchFinancesByDate();
fetchProducts();
fetchListedCategories();
</script>
<style>
/* Button and Table Styling */
h3 {
    font-size: 25px;
}
td, th {
    border-top: 0.5px solid #0F2C4A;
    border-bottom: 0.5px solid #0F2C4A;
    text-align: center;
}

.table_container {
    border: 5px solid #0F2C4A;
    border-radius: 14px;
    border-collapse: separate;
}
table{
    width: 1000px;
    height: 26vh;
}

/* Responsive design */
@media (max-width: 1024px) {
    .flex {
        flex-direction: column;
    }

    .inventory_table {
        width: 100%;
        margin: 1rem 0;
    }
    
    table {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .max-w-7xl {
        width: 100vw;
    }
    
    .py-6 {
        padding: 0.5rem;
    }
    
    td, th {
        font-size: 0.625rem;
        padding: 0.25rem;
    }
}
</style>

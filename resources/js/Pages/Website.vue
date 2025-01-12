<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';
import WebsiteModal from '@/Pages/WebsiteModal.vue';

const showModal = ref(false);

const handleFilterApplied = ({ fromDate, toDate }) => {
    console.log('Filter applied with dates:', fromDate, toDate);
    fetchAnalyticsData({ fromDate, toDate });
};

let visitorsViewsChart = null;
let engagementRateChart = null;
let bounceRateChart = null;
let userTypesChart = null;

function goToEditWebsite1(){
    Inertia.visit(route('editWebsite1'));
}

function goToPreviewHomePage(){
    Inertia.visit(route('preview_homepage'));
}

Chart.register(...registerables);

const selectedPeriod = ref('today');
const selectedYear = ref(2025);
const availableYears = [2024, 2025,];
const views = ref(0);
const visits = ref(0);
const visitors = ref(0);
const bounceRate = ref(0);
const avgVisitTime = ref('');
const engagementRate = ref(0);

const dailyData = ref([]);
const userTypes = ref({ new: 0, returning: 0 });

const granularity = ref('hour');

const updateGranularity = () => {
  switch (selectedPeriod.value) {
    case 'today':
      granularity.value = 'hour';
      break;
    case 'weekly':
      granularity.value = 'day';
      break;
    case 'monthly':
      granularity.value = 'day';
      break;
    case 'yearly':
      granularity.value = 'month';
      break;
    default:
      granularity.value = 'day';
  }
};

const fetchAnalyticsData = async ({ fromDate = '', toDate = '' } = {}) => {
    try {
        updateGranularity();
        const { data } = await axios.get(`/api/analytics`, {
            params: { 
                period: selectedPeriod.value, 
                granularity: granularity.value,
                fromDate,
                toDate,
                year: selectedPeriod.value === 'yearly' ? selectedYear.value : undefined
            },
        });

        console.log('Received analytics data:', data);

        views.value = data.metricsData.views;
        visits.value = data.metricsData.visits;
        visitors.value = data.metricsData.visitors;
        bounceRate.value = data.metricsData.bounceRate;
        avgVisitTime.value = data.metricsData.avgVisitTime;
        engagementRate.value = data.metricsData.engagementRate;

        // Ensure dailyData is properly set
        dailyData.value = data.dailyData.map(item => ({
            ...item,
            views: parseInt(item.views),
            visitors: parseInt(item.visitors),
            engagementRate: parseFloat(item.engagementRate),
            bounceRate: parseFloat(item.bounceRate)
        }));

        userTypes.value = {
            new: parseInt(data.userTypes.new),
            returning: parseInt(data.userTypes.returning)
        };

        renderVisitorsViewsChart();
        renderEngagementRateChart();
        renderBounceRateChart();
        renderUserTypesChart();

    } catch (error) {
        console.error("Error fetching analytics data", error);
    }
};

const renderVisitorsViewsChart = () => {
    if (visitorsViewsChart) {
        visitorsViewsChart.destroy();
    }

    const labels = dailyData.value.map(data => {
        if (selectedPeriod.value === 'yearly' && granularity.value === 'month') {
            return new Date(`${data.dimension}-01`).toLocaleString('default', { month: 'short' });
        }
        if (selectedPeriod.value === 'weekly' && granularity.value === 'day') {
            return new Date(data.dimension).toLocaleDateString('en-US', { weekday: 'short' });
        }
        return granularity.value === 'hour'
            ? `${data.dimension.toString().padStart(2, '0')}:00`
            : data.dimension;
    });

    const viewsData = dailyData.value.map(data => data.views);
    const visitorsData = dailyData.value.map(data => data.visitors);

    const ctx = document.getElementById("visitorsViewsChart").getContext("2d");
    visitorsViewsChart = new Chart(ctx, {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: "Visitors",
                    data: visitorsData,
                    backgroundColor: "#8392AC",
                    borderColor: "#8392AC",
                    borderWidth: 1,
                    fill: false,
                },
                {
                    label: "Views",
                    data: viewsData,
                    backgroundColor: "#0F2C4A",
                    borderColor: "#0F2C4A",
                    borderWidth: 1,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        maxRotation: 15,
                        minRotation: 15,
                        callback: function(val, index) {
                            if (selectedPeriod.value === 'monthly') {
                                return index % 3 === 0 ? this.getLabelForValue(val) : '';
                            }
                            return this.getLabelForValue(val);
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "#E5E7EB",
                    },
                },
            },
            plugins: {
                legend: {
                    display: true,
                    position: "bottom",
                    labels: {
                        usePointStyle: true,
                        pointStyle: "circle",
                    },
                },
            },
        },
    });
};




const renderBounceRateChart = () => {
    if (bounceRateChart) {
        bounceRateChart.destroy();
    }

    const labels = dailyData.value.map(data => {
        if (selectedPeriod.value === 'yearly' && granularity.value === 'month') {
            return new Date(data.dimension + '-01').toLocaleString('default', { month: 'short' });
        } else if (selectedPeriod.value === 'weekly' && granularity.value === 'day') {
            return new Date(data.dimension).toLocaleDateString('en-US', { weekday: 'short' });
        }
        return granularity.value === 'hour'
            ? `${data.dimension.toString().padStart(2, '0')}:00`
            : data.dimension;
    });
    const bounceRateData = dailyData.value.map((data) => data.bounceRate);

    const ctx = document.getElementById('bounceRateChart').getContext('2d');
    bounceRateChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Retention Rate',
                    data: bounceRateData,
                    backgroundColor: '#E5ECF2',
                    borderColor: '#0F2C4A',
                    fill: true,
                    tension: 0.1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { maxRotation: 15, minRotation: 15 } },
                y: { 
                    beginAtZero: true, 
                    ticks: { callback: (value) => `${value}%` },
                    max: 100,
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

const renderEngagementRateChart = () => {
    if (engagementRateChart) {
        engagementRateChart.destroy();
    }

    const labels = dailyData.value.map(data => {
        if (selectedPeriod.value === 'yearly' && granularity.value === 'month') {
            return new Date(data.dimension + '-01').toLocaleString('default', { month: 'short' });
        } else if (selectedPeriod.value === 'weekly' && granularity.value === 'day') {
            return new Date(data.dimension).toLocaleDateString('en-US', { weekday: 'short' });
        }
        return granularity.value === 'hour'
            ? `${data.dimension.toString().padStart(2, '0')}:00`
            : data.dimension;
    });
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
                    backgroundColor: "#E5ECF2",
                    borderColor: "#0F2C4A",
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
                        maxRotation: 15,
                        minRotation: 15,
                    },
                },
                y: {
                    beginAtZero: true,
                    max: 100,
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

const renderUserTypesChart = () => {
    if (userTypesChart) {
        userTypesChart.destroy();
    }

    const totalUsers = userTypes.value.new + userTypes.value.returning;
    const newUsersPercentage = totalUsers ? ((userTypes.value.new / totalUsers) * 100).toFixed(2) : 0;
    const returningUsersPercentage = totalUsers ? ((userTypes.value.returning / totalUsers) * 100).toFixed(2) : 0;

    const ctx = document.getElementById('userTypesChart').getContext('2d');
    userTypesChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['New Users', 'Returning Users'],
            datasets: [
                {
                    label: 'User Types',
                    data: [userTypes.value.new, userTypes.value.returning],
                    backgroundColor: ['#0F2C4A', '#9BA7B0'],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = totalUsers ? ((value / totalUsers) * 100).toFixed(2) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });
};

watch(selectedPeriod, async () => {
    await fetchAnalyticsData();
});

watch(selectedYear, async () => {
    if (selectedPeriod.value === 'yearly') {
        await fetchAnalyticsData();
    }
});

onMounted(fetchAnalyticsData);
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <div class="px-8 border-b border-black">
            <div class="grid grid-cols-7 gap-4 text-center">
                <div>
                    <h2 class="text-3xl font-extrabold mb-2">{{ views }}</h2>
                    <p class="font-bold">Visits</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold mb-2">{{ visits }}</h2>
                    <p class="font-bold">Views</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold mb-2">{{ visitors }}</h2>
                    <p class="font-bold">Visitors</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold mb-2">{{ bounceRate }}</h2>
                    <p class="font-bold">Retention Rate</p>
                </div>
                <div>
                    <h2 class="text-3xl font-bold mb-2">{{ avgVisitTime }}</h2>
                    <p class="font-bold">Avg Visit Time</p>
                </div>
                <div>
                    <select v-model="selectedPeriod" class="ml-4 p-2 pr-8 border rounded">
                        <option value="today">Today</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div v-if="selectedPeriod === 'yearly'">
                    <select v-model="selectedYear" class="ml-4 p-2 pr-8 border rounded">
                        <option v-for="year in availableYears" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
                </div>
                <div v-else>
                    <button 
                        @click="showModal = true"
                        class="ml-4 p-2 pr-8 border-black #0E2940 rounded text-white mr-10"
                        style="background-color: #0E2940;"
                    >
                        Custom Date
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap justify-center px-8 gap-5">
            <h2 v-if="selectedPeriod === 'yearly'" class="w-full text-2xl font-bold text-center mt-4">
                Generated Analytics for Year {{ selectedYear }}
            </h2>
            <div class="custom-chart-width p-4 border border-black rounded-lg mt-6">
                <h3 class="text-l font-semibold text-center">Visitors and Views</h3>
                <canvas id="visitorsViewsChart" class="w-full" style="height: 280px;"></canvas>
            </div>
            <div class="custom-chart-width p-4 border border-black rounded-lg mt-6">
                <h3 class="text-l font-semibold text-center">Retention Rate</h3>
                <canvas id="bounceRateChart" class="w-full" style="height: 280px;"></canvas>
            </div>
            <div class="custom-chart-width p-4 border border-black rounded-lg mt-10">
                <h3 class="text-l font-semibold text-center">Engagement Rate</h3>
                <canvas id="engagementRateChart" class="w-full" style="height: 280px;"></canvas>
            </div>
            <div class="custom-chart-width p-4 border border-black rounded-lg mt-10">
                <h3 class="text-l font-semibold text-center">User Types</h3>
                <canvas id="userTypesChart" class="w-full" style="height: 280px;"></canvas>
            </div>
        </div>

        <div class="ml-48 fixed bottom-0 left-0 right-0 flex justify-center mb-3 space-x-2">
            <button class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 mr-1 bg-blue-500 text-white px-6 py-4 rounded-md" @click="goToPreviewHomePage">Preview Website</button>
            <button class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 mr-1 bg-blue-500 text-white px-6 py-4 rounded-md" @click="goToEditWebsite1">Edit Website</button>
        </div>

        <WebsiteModal 
            :showModal="showModal" 
            @filter-applied="handleFilterApplied" 
            @close="showModal = false" 
        />
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-chart-width {
    width: 750px;
    max-height: 300px;
    height: 300px;
}
</style>


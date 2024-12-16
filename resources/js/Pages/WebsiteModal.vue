<template>
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-xl font-bold mb-4">Filter by Date</h3>
            <div class="mb-4">
                <label for="fromDate" class="block text-sm font-semibold">From</label>
                <input 
                    id="fromDate" 
                    type="date" 
                    v-model="fromDate" 
                    class="w-full p-2 border rounded mt-2" 
                />
            </div>
            <div class="mb-4">
                <label for="toDate" class="block text-sm font-semibold">To</label>
                <input 
                    id="toDate" 
                    type="date" 
                    v-model="toDate" 
                    class="w-full p-2 border rounded mt-2" 
                />
            </div>
            <div class="flex justify-end">
                <button 
                    @click="applyFilter"
                    class="bg-blue-500 text-white px-4 py-2 rounded mr-2"
                >
                    Apply Filter
                </button>
                <button 
                    @click="closeModal"
                    class="bg-gray-500 text-white px-4 py-2 rounded"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';

// Define props
const props = defineProps({
  showModal: Boolean,
});

// Define emits
const emit = defineEmits(['filter-applied', 'close']);

const fromDate = ref('');
const toDate = ref('');

const closeModal = () => {
    emit('close'); // Emit close event to parent
    fromDate.value = '';
    toDate.value = '';
};

const applyFilter = async () => {
    const filterData = { fromDate: fromDate.value, toDate: toDate.value };
    
    try {
        // Replace with the actual API URL
        const response = await axios.get('/api/analytics', { params: filterData });
        // Handle the response here (e.g., update your component's data with the analytics)
        emit('filter-applied', filterData);
        console.log(response.data);
    } catch (error) {
        console.error("Error fetching data:", error);
    }

    closeModal(); // Close the modal after applying the filter
};
</script>

<style scoped>
/* Add some custom styles for the modal */
</style>

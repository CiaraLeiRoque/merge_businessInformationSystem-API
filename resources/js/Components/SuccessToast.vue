<template>
  <div v-if="visible" class="toast-container success">
    <span class="toast-message">{{ message }}</span>
    <button class="close-btn" @click="closeToast">×</button>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  visible: Boolean,
  message: String,
});
const emit = defineEmits(['close']);
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 10%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: green;
  padding: 15px 25px;
  border: 2px solid green; /* Green border */
  background-color: white; /* White background */
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  opacity: 0;
  animation: fadeInOut 3s ease forwards;
  text-align: center;
}

.toast-container.success {
  color: green;
}

.close-btn {
  background: none;
  border: none;
  color: green;
  font-size: 16px;
  font-weight: bold;
  margin-left: 15px;
  cursor: pointer;
  outline: none;
}

.close-btn:hover {
  color: darkgreen;
}

@keyframes fadeInOut {
  0% {
    opacity: 0;
    transform: translate(-50%, -60%);
  }
  10%, 90% {
    opacity: 1;
    transform: translate(-50%, -50%);
  }
  100% {
    opacity: 0;
    transform: translate(-50%, -60%);
  }
}
</style>

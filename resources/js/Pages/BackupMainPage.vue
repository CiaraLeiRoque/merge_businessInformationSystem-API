

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import BackupButton from '@/Components/BackupButton.vue';
import ImportButton from '@/Components/ImportButton.vue';

</script>

<template>
  <Head title="Backup and Restore" />
  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">Welcome to the Backup Page!</div>
        </div>
      </div>
    </div>
    <div class="flex flex-col items-center">
      <div class="flex flex-row items-start justify-center w-full">
        <div class="pt-3 flex-1 flex justify-end pr-8">
          <BackupButton />
        </div>
          <div class="w-px h-96 bg-gray-300 dark:bg-gray-600"></div>
        <div class="pt-3 flex-1 flex justify-start pl-8">
          <ImportButton />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Tambah Jadwal Makan
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="mb-4">
                <label for="food_name" class="block text-sm font-medium text-gray-700">Nama Makanan</label>
                <input
                  id="food_name"
                  v-model="form.food_name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                />
                <div v-if="form.errors.food_name" class="text-red-500 text-sm mt-1">
                  {{ form.errors.food_name }}
                </div>
              </div>
  
              <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                  {{ form.errors.description }}
                </div>
              </div>
  
              <div class="mb-4">
                <label for="schedule_time" class="block text-sm font-medium text-gray-700">Waktu Jadwal</label>
                <input
                  id="schedule_time"
                  v-model="form.schedule_time"
                  type="datetime-local"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                />
                <div v-if="form.errors.schedule_time" class="text-red-500 text-sm mt-1">
                  {{ form.errors.schedule_time }}
                </div>
              </div>
  
              <div class="flex items-center justify-end mt-6">
                <inertia-link
                  :href="route('eat-schedule.index')"
                  class="px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 mr-2"
                >
                  Batal
                </inertia-link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </app-layout>
  </template>
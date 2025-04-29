<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Detail Jadwal Makan
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">{{ schedule.food_name }}</h3>
            <div class="flex space-x-2">
              <inertia-link
                v-if="can.edit"
                :href="route('eat-schedule.edit', schedule.id)"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
              >
                Edit
              </inertia-link>
              <button
                v-if="can.delete"
                @click="confirmDelete"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
              >
                Hapus
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500">Status</h4>
              <div class="mt-1">
                <span :class="[
                  'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                  schedule.is_completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                ]">
                  {{ schedule.is_completed ? 'Selesai' : 'Belum Selesai' }}
                </span>
              </div>
            </div>
            
            <div>
              <h4 class="text-sm font-medium text-gray-500">Waktu Jadwal</h4>
              <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(schedule.schedule_time) }}</p>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500">Deskripsi</h4>
              <p class="mt-1 text-sm text-gray-900">{{ schedule.description || 'Tidak ada deskripsi' }}</p>
            </div>

            <div v-if="can.edit && !schedule.is_completed">
              <button
                @click="markAsCompleted"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
              >
                Tandai Selesai
              </button>
            </div>
          </div>

          <div class="mt-8">
            <inertia-link
              :href="route('eat-schedule.index')"
              class="text-indigo-600 hover:text-indigo-900"
            >
              &larr; Kembali ke Daftar
            </inertia-link>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';

export default {
  components: {
    AppLayout,
  },
  props: {
    schedule: Object,
    can: Object,
  },
  methods: {
    formatDateTime(datetime) {
      return new Date(datetime).toLocaleString();
    },
    confirmDelete() {
      if (confirm(`Apakah Anda yakin ingin menghapus jadwal: ${this.schedule.food_name}?`)) {
        Inertia.delete(route('eat-schedule.destroy', this.schedule.id));
      }
    },
    markAsCompleted() {
      Inertia.put(route('eat-schedule.mark-completed', this.schedule.id));
    },
  },
};
</script>
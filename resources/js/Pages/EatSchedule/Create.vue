<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';

export default {
  components: {
    AppLayout,
  },
  props: {
    schedules: Array,
    can: Object,
  },
  methods: {
    formatDateTime(datetime) {
      return new Date(datetime).toLocaleString();
    },
    confirmDelete(schedule) {
      if (confirm(`Apakah Anda yakin ingin menghapus jadwal: ${schedule.food_name}?`)) {
        Inertia.delete(route('eat-schedule.destroy', schedule.id));
      }
    },
  },
};
</script>
<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Jadwal Makan
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">Daftar Jadwal Makan</h3>
            <inertia-link
              v-if="can.create"
              :href="route('eat-schedule.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
            >
              Tambah Jadwal
            </inertia-link>
          </div>

          <div v-if="schedules.length === 0" class="text-center py-8 text-gray-500">
            Belum ada jadwal makan yang tersedia.
          </div>

          <table v-else class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Makanan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(schedule, index) in schedules" :key="schedule.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ schedule.food_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatDateTime(schedule.schedule_time) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    schedule.is_completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  ]">
                    {{ schedule.is_completed ? 'Selesai' : 'Belum Selesai' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <inertia-link
                      v-if="can.view"
                      :href="route('eat-schedule.show', schedule.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Detail
                    </inertia-link>
                    <inertia-link
                      v-if="can.edit"
                      :href="route('eat-schedule.edit', schedule.id)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Edit
                    </inertia-link>
                    <button
                      v-if="can.delete"
                      @click="confirmDelete(schedule)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </app-layout>
</template>



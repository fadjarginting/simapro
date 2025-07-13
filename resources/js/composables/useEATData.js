import { ref } from 'vue';
import axios from 'axios';

/**
 * Composable untuk mengambil dan mengelola data yang dibutuhkan
 * oleh Engineering Assignment Task (EAT).
 *
 * @returns {Object} - Objek reaktif berisi data, status loading, dan fungsi fetch.
 */
export function useEATData() {
    // State reaktif untuk menampung data
    const disciplines = ref([]);
    const users = ref([]);
    const isLoading = ref(false);

    /**
     * Mengambil data disciplines dan users dari API secara bersamaan.
     */
    const fetchData = async () => {
        isLoading.value = true;
        try {
            // Menggunakan Promise.all untuk menjalankan kedua request secara paralel
            const [disciplinesResponse, usersResponse] = await Promise.all([
                axios.get(route('disciplines.api.all')),
                axios.get(route('users.api.all')) // Pastikan route ini ada
            ]);

            disciplines.value = disciplinesResponse.data;
            users.value = usersResponse.data;

        } catch (error) {
            console.error('Gagal mengambil data EAT:', error);
            // Anda bisa menambahkan notifikasi error di sini
            alert('Terjadi kesalahan saat memuat data penting. Silakan coba lagi.');
        } finally {
            isLoading.value = false;
        }
    };

    // Mengembalikan state dan fungsi agar bisa digunakan di komponen
    return {
        disciplines,
        users,
        isLoading,
        fetchData
    };
}
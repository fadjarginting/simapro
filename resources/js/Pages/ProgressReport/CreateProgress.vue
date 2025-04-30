<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

defineOptions({
    layout: AuthenticatedLayout,
});

// Setup form inertia
const form = useForm({
    request_category: "",
    status_verifikasi: "",
    pic_mekanikal: "",
    progress_mekanikal: "",
    pic_sipil: "",
    progress_sipil: "",
    pic_elinst: "",
    progress_elinst: "",
    pic_proses: "",
    progress_proses: "",
    detail_progress: "",
    note: "",
});

// Submit function
function submit() {
    form.post(route("progress.store"), {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: "Progress berhasil ditambahkan!",
                timer: 2000,
                showConfirmButton: false,
            }).then(() => {
                window.location.href = route("progress.index");
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Terdapat kesalahan input, periksa kembali form kamu!",
            });
        },
    });
}
</script>

<template>
    <Head title="Add Progress" />

    <!-- form add document -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="p-2 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-2">
                            Add New Progress
                        </h2>
                    </div>
                </div>
                <!-- FORM -->
                <div class="max-w-full mt-0 p-6 bg-white rounded-lg shadow-md">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="request-category"
                                    value="Request Category"
                                />
                                <div class="relative">
                                    <select
                                        id="request-category"
                                        v-model="form.request_category"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Request Category (CAPEX/OPEX)
                                        </option>
                                        <option value="CAPEX">CAPEX</option>
                                        <option value="OPEX">OPEX</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="status-verifikasi"
                                    value="Status Verifikasi"
                                />
                                <TextInput
                                    id="status-verifikasi"
                                    v-model="form.status_verifikasi"
                                    placeholder="Enter Status Verifikasi ERF"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError
                                    :message="form.errors.status_verifikasi"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="pic-mekanikal"
                                    value="PIC Mekanikal"
                                />
                                <div class="relative">
                                    <select
                                        id="pic-mekanikal"
                                        v-model="form.pic_mekanikal"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">PIC Mekanikal</option>
                                        <option value="NOV">NOV</option>
                                        <option value="Type 2">Type 2</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="progress-mekanikal"
                                    value="Progress Mekanikal"
                                />
                                <div class="relative">
                                    <TextInput
                                        id="progress-mekanikal"
                                        v-model="form.progress_mekanikal"
                                        placeholder="Contoh: 75 atau 75,5"
                                        type="text"
                                        class="mt-1 block w-full pr-10"
                                        @input="
                                            (e) => {
                                                // Ganti koma ke titik dan validasi agar hanya angka & titik
                                                const cleaned = e.target.value
                                                    .replace(',', '.')
                                                    .replace(/[^0-9.]/g, '');
                                                form.progress_mekanikal =
                                                    cleaned;
                                            }
                                        "
                                        required
                                    />
                                    <span
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none"
                                    >
                                        %
                                    </span>
                                </div>
                                <InputError
                                    :message="form.errors.progress_mekanikal"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel for="pic-sipil" value="PIC Sipil" />
                                <div class="relative">
                                    <select
                                        id="pic-sipil"
                                        v-model="form.pic_sipil"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">PIC Sipil</option>
                                        <option value="AEF">AEF</option>
                                        <option value="Type 2">Type 2</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="progress-sipil"
                                    value="Progress Sipil"
                                />
                                <div class="relative">
                                    <TextInput
                                        id="progress-sipil"
                                        v-model="form.progress_sipil"
                                        placeholder="Contoh: 75 atau 75,5"
                                        type="text"
                                        class="mt-1 block w-full pr-10"
                                        @input="
                                            (e) => {
                                                // Ganti koma ke titik dan validasi agar hanya angka & titik
                                                const cleaned = e.target.value
                                                    .replace(',', '.')
                                                    .replace(/[^0-9.]/g, '');
                                                form.progress_sipil = cleaned;
                                            }
                                        "
                                        required
                                    />
                                    <span
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none"
                                    >
                                        %
                                    </span>
                                </div>
                                <InputError
                                    :message="form.errors.progress_sipil"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="pic-elinst"
                                    value="PIC Elinst"
                                />
                                <div class="relative">
                                    <select
                                        id="pic-elinst"
                                        v-model="form.pic_elinst"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">PIC Elinst</option>
                                        <option value="MCA">MCA</option>
                                        <option value="Type 2">Type 2</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="progress-elinst"
                                    value="Progress Elinst"
                                />
                                <div class="relative">
                                    <TextInput
                                        id="progress-elinst"
                                        v-model="form.progress_elinst"
                                        placeholder="Contoh: 75 atau 75,5"
                                        type="text"
                                        class="mt-1 block w-full pr-10"
                                        @input="
                                            (e) => {
                                                // Ganti koma ke titik dan validasi agar hanya angka & titik
                                                const cleaned = e.target.value
                                                    .replace(',', '.')
                                                    .replace(/[^0-9.]/g, '');
                                                form.progress_elinst = cleaned;
                                            }
                                        "
                                        required
                                    />
                                    <span
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none"
                                    >
                                        %
                                    </span>
                                </div>
                                <InputError
                                    :message="form.errors.progress_elinst"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="pic-proses"
                                    value="PIC Proses"
                                />
                                <div class="relative">
                                    <select
                                        id="pic-proses"
                                        v-model="form.pic_proses"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">PIC Proses</option>
                                        <option value="MRJ">MRJ</option>
                                        <option value="Type 2">Type 2</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="progress-proses"
                                    value="Progress Proses"
                                />
                                <div class="relative">
                                    <TextInput
                                        id="progress-proses"
                                        v-model="form.progress_proses"
                                        placeholder="Contoh: 75 atau 75,5"
                                        type="text"
                                        class="mt-1 block w-full pr-10"
                                        @input="
                                            (e) => {
                                                // Ganti koma ke titik dan validasi agar hanya angka & titik
                                                const cleaned = e.target.value
                                                    .replace(',', '.')
                                                    .replace(/[^0-9.]/g, '');
                                                form.progress_proses = cleaned;
                                            }
                                        "
                                        required
                                    />
                                    <span
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none"
                                    >
                                        %
                                    </span>
                                </div>
                                <InputError
                                    :message="form.errors.progress_proses"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <InputLabel
                                    for="detail-progress"
                                    value="Detail Progress"
                                />
                                <TextInput
                                    id="detail-progress"
                                    v-model="form.detail_progress"
                                    placeholder="Enter Detail Progress"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError
                                    :message="form.errors.detail_progress"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="note" value="Note" />
                                <TextInput
                                    id="note"
                                    v-model="form.note"
                                    placeholder="Enter Note"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.note" />
                            </div>
                        </div>

                        <div class="flex mt-6 space-x-4 justify-end">
                            <button
                                type="button"
                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
                                @click="form.reset"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end form add document -->
</template>

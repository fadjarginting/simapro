<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <Head title="Add Progress" />

    <!-- form add document -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="p-2 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-2">Add New Progress</h2>
                    </div>
                </div>
                <!-- FORM -->
                <div class="max-w-full mt-0 p-6 bg-white rounded-lg shadow-md">
                    <form>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel for="request-category" value="Request Category" />
                                <div class="relative">
                                    <select
                                        id="request-category"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>Request Category (CAPEX/OPEX)</option>
                                        <option>CAPEX</option>
                                        <option>OPEX</option>
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
                                    placeholder="Enter Status Verifikasi ERF"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel for="pic-mekanikal" value="PIC Mekanikal" />
                                <div class="relative">
                                    <select
                                        id="pic-mekanikal"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>PIC Mekanikal</option>
                                        <option>NOV</option>
                                        <option>Type 2</option>
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
                                <TextInput
                                    id="progress-mekanikal"
                                    placeholder="Enter Progress %"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
                            </div>

                            
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="pic-sipil"
                                    value="PIC Sipil"
                                />
                                <div class="relative">
                                    <select
                                        id="pic-sipil"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>PIC Sipil</option>
                                        <option>AEF</option>
                                        <option>Type 2</option>
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
                                <TextInput
                                    id="progress-sipil"
                                    placeholder="Enter Progress %"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
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
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>PIC Elinst</option>
                                        <option>MCA</option>
                                        <option>Type 2</option>
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
                                <TextInput
                                    id="progress-elinst"
                                    placeholder="Enter Progress %"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
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
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>
                                            PIC Proses
                                        </option>
                                        <option>MRJ</option>
                                        <option>Type 2</option>
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
                                <TextInput
                                    id="progress-proses"
                                    placeholder="Enter Progress %"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <InputLabel for="detail-progress" value="Detail Progress" />
                                <TextInput
                                    id="detail-progress"
                                    placeholder="Enter Detail Progress"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError />
                            </div>
                            
                            <div class="mb-4">
                                <InputLabel for="note" value="Note" />
                                <TextInput
                                    id="note"
                                    placeholder="Enter Note"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
                            </div>
                        </div>

                        <div class="flex mt-6 space-x-4 justify-end">
                            <button
                                type="button"
                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
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

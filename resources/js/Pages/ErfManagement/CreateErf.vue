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
    <Head title="Add ERF" />

    <!-- form add document -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="p-2 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-2">Add New ERF</h2>
                    </div>
                </div>
                <!-- FORM -->
                <div class="max-w-full mt-0 p-6 bg-white rounded-lg shadow-md">
                    <form>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <InputLabel for="job-title" value="Job Title" />
                                <TextInput
                                    id="job-title"
                                    placeholder="Enter Job Title"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="no-erf" value="No ERF" />
                                <TextInput
                                    id="no-erf"
                                    placeholder="Enter No ERF"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="requesting-unit"
                                    value="Requesting Unit"
                                />
                                <TextInput
                                    id="requesting-unit"
                                    placeholder="Enter Requesting Unit"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel for="plant" value="Plant" />
                                <div class="relative">
                                    <select
                                        id="plant"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>Select Plant</option>
                                        <option>APLP</option>
                                        <option>BINS</option>
                                        <option>GENERAL</option>
                                        <option>GP DUMAI</option>
                                        <option>INDARUNG II/III</option>
                                        <option>INDARUNG IV</option>
                                        <option>INDARUNG V</option>
                                        <option>INDARUNG VI</option>
                                        <option>PP BENGKULU</option>
                                        <option>PPI</option>
                                        <option>PPTB</option>
                                        <option>TAMBANG</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel for="job-type" value="Job Type" />
                                <div class="relative">
                                    <select
                                        id="job-type"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>Select Job Type</option>
                                        <option>FEED/DED</option>
                                        <option>Kajian Engineering</option>
                                        <option>Technical Assist</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel
                                    for="work-priority"
                                    value="Work Priority"
                                />
                                <div class="relative">
                                    <select
                                        id="work-priority"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>Select Work Piority</option>
                                        <option>-</option>
                                        <option>P1</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="request-category"
                                    value="Request Category"
                                />
                                <div class="relative">
                                    <select
                                        id="request-category"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>Select Request Category</option>
                                        <option>OPEX</option>
                                        <option>CAPEX</option>
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
                                    for="erf-disetujui"
                                    value="Tanggal ERF Disetujui"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="erf-disetujui"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fuchsia-300 dark:bg-gray-950 dark:text-white/80 text-sm leading-5.6 transition-all"
                                        required
                                    />
                                    <!-- Ikon Kalender -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        📅
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="klarifikasi-erf"
                                    value="Tanggal Klarifikasi ERF"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="klarifikasi-erf"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fuchsia-300 dark:bg-gray-950 dark:text-white/80 text-sm leading-5.6 transition-all"
                                        required
                                    />
                                    <!-- Ikon Kalender -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        📅
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <InputLabel
                                    for="erf-disahkan"
                                    value="Tanggal ERF Disahkan"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="erf-disahkan"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fuchsia-300 dark:bg-gray-950 dark:text-white/80 text-sm leading-5.6 transition-all"
                                        required
                                    />
                                    <!-- Ikon Kalender -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        📅
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel
                                    for="assigned-lead"
                                    value="Assigned Lead Engineering"
                                />
                                <div class="relative">
                                    <select
                                        id="assigned-lead"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>
                                            Select Assigned Lead Engineering
                                        </option>
                                        <option>ANDRA NOVENDRI</option>
                                        <option>AULIA EKADANA FAUTHRISNO</option>
                                        <option>MARJUKI</option>
                                        <option>MOCH CHOIRIL ANAM</option>
                                        <option>NOVRIADI M</option>
                                        <option>TBD</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel
                                    for="status-verifikasi"
                                    value="Status Verifikasi ERF"
                                />
                                <div class="relative">
                                    <select
                                        id="status-verifikasi"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>
                                            Select Status Verifikasi ERF
                                        </option>
                                        <option>Belum Verifikasi</option>
                                        <option>Finish Verifikasi</option>
                                        <option>In Progress Verifikasi</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="keterangan-progres"
                                    value="Keterangan Progress"
                                />
                                <div class="relative">
                                    <select
                                        id="keterangan-progres"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option>
                                            Select Keterangan Progress
                                        </option>
                                        <option>Approval dokumen di e-DEMS</option>
                                        <option>Approval dokumen di e-DEMS oleh Ka. Dept.</option>
                                        <option>Approval dokumen di e-DEMS oleh Ka. Unit</option>
                                        <option>Approval EAT di e-DEMS oleh Ka. Unit</option>
                                        <option>Approval pengesahan ERF oleh User di e-DEMS</option>
                                        <option>Belum ada ERF</option>
                                        <option>Dokumen terkirim ke User melalui e-DEMS</option>
                                        <option>ERF belum disetujui oleh User di e-DEMS</option>
                                        <option>Hold</option>
                                        <option>Penyusunan dokumen DED</option>
                                        <option>Penyusunan dokumen Kajian</option>
                                        <option>Proses EAT</option>
                                        <option>Proses verifikasi ERF</option>
                                        <option>Reject</option>
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
                                    for="initiating-phase"
                                    value="Deadline Initiating Phase"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="initiating-phase"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fuchsia-300 dark:bg-gray-950 dark:text-white/80 text-sm leading-5.6 transition-all"
                                        required
                                    />
                                    <!-- Ikon Kalender -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        📅
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="executing-phase"
                                    value="Deadline Executing Phase"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="executing-phase"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fuchsia-300 dark:bg-gray-950 dark:text-white/80 text-sm leading-5.6 transition-all"
                                        required
                                    />
                                    <!-- Ikon Kalender -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        📅
                                    </div>
                                </div>
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

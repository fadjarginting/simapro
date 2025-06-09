<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    progress: Object,
});

defineOptions({
    layout: AuthenticatedLayout,
});

//tanggal
function formatDate(dateStr) {
    if (!dateStr) return "N/A";
    const date = new Date(dateStr);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
}
</script>

<template>
    <Head title="Detail Progress" />

    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">
                    Detail Progress
                </h2>

                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <p class="font-semibold">Job Title:</p>
                        <p>{{ progress.title }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Plant:</p>
                        <p>{{ progress.plant }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Work Priority:</p>
                        <p>{{ progress.work_priority }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Job Type:</p>
                        <p>{{ progress.job_type }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Request Category:</p>
                        <p>{{ progress.request_category }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">No ERF:</p>
                        <p>{{ progress.no_erf }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">ERF Approved Date:</p>
                        <p>{{ formatDate(progress.erf_approved_date) }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">ERF Clarification Date:</p>
                        <p>{{ formatDate(progress.erf_clarification_date) }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">ERF Validated Date:</p>
                        <p>{{ formatDate(progress.erf_validated_date) }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Lead Engineering:</p>
                        <p>{{ progress.lead_engineering }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">PIC Mekanikal:</p>
                        <p>{{ progress.pic_mekanikal }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Progress Mekanikal:</p>
                        <p>{{ progress.progress_mekanikal }}%</p>
                    </div>

                    <div>
                        <p class="font-semibold">PIC Sipil:</p>
                        <p>{{ progress.pic_sipil }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Progress Sipil:</p>
                        <p>{{ progress.progress_sipil }}%</p>
                    </div>

                    <div>
                        <p class="font-semibold">PIC Elinst:</p>
                        <p>{{ progress.pic_elinst }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Progress Elinst:</p>
                        <p>{{ progress.progress_elinst }}%</p>
                    </div>

                    <div>
                        <p class="font-semibold">PIC Proses:</p>
                        <p>{{ progress.pic_proses }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Progress Proses:</p>
                        <p>{{ progress.progress_proses }}%</p>
                    </div>
                    <div>
                        <p class="font-semibold">Requesting Unit:</p>
                        <p>{{ progress.requesting_unit }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Status Verifikasi:</p>
                        <p>{{ progress.status_verifikasi }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Deadline Initiating:</p>
                        <p>{{ formatDate(progress.deadline_initiating) }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Deadline Executing:</p>
                        <p>{{ formatDate(progress.deadline_executing) }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Status:</p>
                        <p>{{ progress.status }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Fase:</p>
                        <p>{{ progress.fase }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Progress Description:</p>
                        <p>{{ progress.progress_description }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="font-semibold">Note:</p>
                        <p>{{ progress.note }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Entry ERF By User:</p>
                        <p>{{ formatDate(progress.entry_date) }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                        @click="$inertia.visit(route('progress.index'))"
                    >
                        Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

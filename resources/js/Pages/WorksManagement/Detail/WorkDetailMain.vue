<script setup>
import { useForm, Head, router, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import WorkHeader from "./WorkHeader.vue";
import WorkActionButtons from "./WorkActionButtons.vue";
import WorkBasicInfo from "./WorkBasicInfo.vue";
import WorkPICSection from "./WorkDocs.vue";
import WorkProgressSection from "./WorkProgressSection.vue";
import WorkStatusSummary from "./WorkStatusSummary.vue";
import WorkTimeline from "./WorkTimeline.vue";
import WorkMetadata from "./WorkMetadata.vue";
import WorkEAT  from "./WorkEAT.vue";
import WorkDocs from "./WorkDocs.vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    work: Object,
    notes: Array,
    documents: Array,
});
</script>

<template>

    <Head>
        <title>Detail Pekerjaan - {{ work.slug }}</title>
    </Head>

    <div class="mx-auto px-5 sm:px-6 lg:px-8">
        <!-- Header -->
        <WorkHeader :work="work" />

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-4 lg:gap-6 xl:grid-cols-4">
            <!-- Left Column -->
            <div class="xl:col-span-3 space-y-4 lg:space-y-6">
                <WorkBasicInfo :work="work" :notes="notes" />
                <WorkEAT :work="work" />
                <WorkProgressSection :work="work"  />

            </div>

            <!-- Right Column -->
            <div class="xl:col-span-1 space-y-4 lg:space-y-6 ">
                <WorkStatusSummary :work="work" />
                <WorkTimeline :work="work" />
                <WorkMetadata :work="work" />
                <WorkDocs :work="work" :documents="documents" />
            </div>
        </div>
    </div>
</template>
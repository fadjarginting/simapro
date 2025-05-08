<script setup>
import { useForm, Head, router } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import { ref, watch, computed } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    plants: Array,
    noteds: Array,
});

// Setup form inertia - modified to use arrays for PICs and Lead Engineering
const form = useForm({
    title: "",
    plant_id: null,
    work_priority: "",
    job_type: "",
    request_category: "",
    no_erf: "",
    erf_approved_date: "",
    erf_clarification_date: "",
    erf_validated_date: "",
    lead_engineering: [], // Changed to array
    pic_mekanikal: [], // Changed to array
    progress_mekanikal: "",
    pic_sipil: [], // Changed to array
    progress_sipil: "",
    pic_elinst: [], // Changed to array
    progress_elinst: "",
    pic_proses: [], // Changed to array
    progress_proses: "",
    uk_peminta: "",
    status_verifikasi: "",
    deadline_initiating: "",
    deadline_executing: "",
    status: "",
    fase: "",
    noted_id: null,
    note: "",
    entry_date: "",
});

// Options for multi-select dropdowns
const leadEngineeringOptions = [
    { value: "AULIA EKADANA FAUTHRISNO", label: "AULIA EKADANA FAUTHRISNO" },
    { value: "MARJUKI", label: "MARJUKI" },
    { value: "MOCH CHOIRIL ANAM", label: "MOCH CHOIRIL ANAM" },
    { value: "NOVRIADI M", label: "NOVRIADI M" },
    { value: "YOKE GISKARD", label: "YOKE GISKARD" },
];

const picMekanikalOptions = [
    { value: "NOV", label: "NOV" },
    { value: "Type 2", label: "Type 2" },
    { value: "Type 3", label: "Type 3" },
    { value: "Type 4", label: "Type 4" },
];

const picSipilOptions = [
    { value: "AEF", label: "AEF" },
    { value: "Type 2", label: "Type 2" },
    { value: "Type 3", label: "Type 3" },
];

const picElinstOptions = [
    { value: "MCA", label: "MCA" },
    { value: "Type 2", label: "Type 2" },
    { value: "Type 3", label: "Type 3" },
];

const picProsesOptions = [
    { value: "MRJ", label: "MRJ" },
    { value: "Type 2", label: "Type 2" },
    { value: "Type 3", label: "Type 3" },
];

// Date tracking for potential duration calculation
const tglMulai = ref("");
const tglSelesai = ref("");
const durasi = ref(0);

// Computed total progress
const totalProgress = computed(() => {
    const mekanikal = parseFloat(form.progress_mekanikal) || 0;
    const sipil = parseFloat(form.progress_sipil) || 0;
    const elinst = parseFloat(form.progress_elinst) || 0;
    const proses = parseFloat(form.progress_proses) || 0;

    // Calculate average of provided progress values that aren't empty
    let total = 0;
    let count = 0;

    if (form.progress_mekanikal) {
        total += mekanikal;
        count++;
    }
    if (form.progress_sipil) {
        total += sipil;
        count++;
    }
    if (form.progress_elinst) {
        total += elinst;
        count++;
    }
    if (form.progress_proses) {
        total += proses;
        count++;
    }

    return count > 0 ? (total / count).toFixed(2) : 0;
});

// Format percentage inputs
const formatPercentage = (e, field) => {
    // Replace comma with dot and remove non-numeric characters
    let value = e.target.value.replace(",", ".").replace(/[^0-9.]/g, "");

    // Ensure only one decimal point
    const decimalCount = (value.match(/\./g) || []).length;
    if (decimalCount > 1) {
        value = value.replace(/\.(?=.*\.)/g, "");
    }

    // Ensure value doesn't exceed 100
    if (parseFloat(value) > 100) {
        value = "100";
    }

    form[field] = value;
};

// Toggle selection for multi-select dropdowns
const toggleSelection = (option, fieldName) => {
    const index = form[fieldName].indexOf(option.value);
    if (index === -1) {
        form[fieldName].push(option.value);
    } else {
        form[fieldName].splice(index, 1);
    }
};

// Check if option is selected
const isSelected = (value, fieldName) => {
    return form[fieldName].includes(value);
};

// Calculate duration when dates change
watch([tglMulai, tglSelesai], () => {
    if (tglMulai.value && tglSelesai.value) {
        const mulai = new Date(tglMulai.value);
        const selesai = new Date(tglSelesai.value);

        // Calculate days difference
        const diffTime = selesai - mulai;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        durasi.value = diffDays >= 0 ? diffDays : 0;
    } else {
        durasi.value = 0;
    }
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
                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <InputLabel
                                    for="entry-erf-by-user"
                                    value="Entry ERF By User"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="entry-erf-by-user"
                                        v-model="form.entry_date"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fuchsia-300 dark:bg-gray-950 dark:text-white/80 text-sm leading-5.6 transition-all"
                                        required
                                    />
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        📅
                                    </div>
                                </div>
                                <InputError :message="form.errors.entry_date" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="title" value="Job Title" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    placeholder="Enter Title"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel for="plant_id" value="Plant" />
                                <div class="relative">
                                    <select
                                        id="plant_id"
                                        v-model="form.plant_id"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">Select Plant</option>
                                        <option
                                            v-for="plant in plants"
                                            :key="plant.id"
                                            :value="plant.id"
                                        >
                                            {{ plant.name }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError :message="form.errors.plant_id" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4 relative">
                                <InputLabel
                                    for="work-priority"
                                    value="Work Priority"
                                />
                                <div class="relative">
                                    <select
                                        id="work-priority"
                                        v-model="form.work_priority"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Select Work Priority
                                        </option>
                                        <option value="-">-</option>
                                        <option value="P1">P1</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.work_priority"
                                />
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel for="job-type" value="Job Type" />
                                <div class="relative">
                                    <select
                                        id="job-type"
                                        v-model="form.job_type"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Select Job Type
                                        </option>
                                        <option value="FEED/DED">
                                            FEED/DED
                                        </option>
                                        <option value="Kajian Engineering">
                                            Kajian Engineering
                                        </option>
                                        <option value="Technical Assist">
                                            Technical Assist
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError :message="form.errors.job_type" />
                            </div>

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
                                            Select Request Category
                                        </option>
                                        <option value="OPEX">OPEX</option>
                                        <option value="CAPEX">CAPEX</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.request_category"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <InputLabel for="no-erf" value="No ERF" />
                                <TextInput
                                    id="no-erf"
                                    v-model="form.no_erf"
                                    placeholder="Enter No ERF"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.no_erf" />
                            </div>

                            <div class="mb-4">
                                <InputLabel
                                    for="erf-disetujui"
                                    value="ERF Approved Date"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="erf-disetujui"
                                        v-model="form.erf_approved_date"
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
                                <InputError
                                    :message="form.errors.erf_approved_date"
                                />
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
                                        v-model="form.erf_clarification_date"
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
                                <InputError
                                    :message="
                                        form.errors.erf_clarification_date
                                    "
                                />
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
                                        v-model="form.erf_validated_date"
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
                                <InputError
                                    :message="form.errors.erf_validated_date"
                                />
                            </div>

                            <!-- Multi-select Lead Engineering -->
                            <div class="mb-4">
                                <InputLabel
                                    for="lead"
                                    value="Lead Engineering (Select multiple if needed)"
                                />
                                <div
                                    class="mt-1 w-full border border-gray-300 rounded-md"
                                >
                                    <div class="p-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="option in leadEngineeringOptions"
                                            :key="option.value"
                                            @click="
                                                toggleSelection(
                                                    option,
                                                    'lead_engineering'
                                                )
                                            "
                                            class="px-3 py-1 rounded-full cursor-pointer text-sm"
                                            :class="
                                                isSelected(
                                                    option.value,
                                                    'lead_engineering'
                                                )
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                            "
                                        >
                                            {{ option.label }}
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Selected:
                                    {{
                                        form.lead_engineering.join(", ") ||
                                        "None"
                                    }}
                                </p>
                                <InputError
                                    :message="form.errors.lead_engineering"
                                />
                            </div>

                            <!-- Multi-select PIC Mekanikal -->
                            <div class="mb-4">
                                <InputLabel
                                    for="pic-mekanikal"
                                    value="PIC Mekanikal (Select multiple if needed)"
                                />
                                <div
                                    class="mt-1 w-full border border-gray-300 rounded-md"
                                >
                                    <div class="p-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="option in picMekanikalOptions"
                                            :key="option.value"
                                            @click="
                                                toggleSelection(
                                                    option,
                                                    'pic_mekanikal'
                                                )
                                            "
                                            class="px-3 py-1 rounded-full cursor-pointer text-sm"
                                            :class="
                                                isSelected(
                                                    option.value,
                                                    'pic_mekanikal'
                                                )
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                            "
                                        >
                                            {{ option.label }}
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Selected:
                                    {{
                                        form.pic_mekanikal.join(", ") || "None"
                                    }}
                                </p>
                                <InputError
                                    :message="form.errors.pic_mekanikal"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <!-- Multi-select PIC Sipil -->
                            <div class="mb-4">
                                <InputLabel
                                    for="pic-sipil"
                                    value="PIC Sipil (Select multiple if needed)"
                                />
                                <div
                                    class="mt-1 w-full border border-gray-300 rounded-md"
                                >
                                    <div class="p-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="option in picSipilOptions"
                                            :key="option.value"
                                            @click="
                                                toggleSelection(
                                                    option,
                                                    'pic_sipil'
                                                )
                                            "
                                            class="px-3 py-1 rounded-full cursor-pointer text-sm"
                                            :class="
                                                isSelected(
                                                    option.value,
                                                    'pic_sipil'
                                                )
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                            "
                                        >
                                            {{ option.label }}
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Selected:
                                    {{ form.pic_sipil.join(", ") || "None" }}
                                </p>
                                <InputError :message="form.errors.pic_sipil" />
                            </div>

                            <!-- Multi-select PIC Elinst -->
                            <div class="mb-4">
                                <InputLabel
                                    for="pic-elinst"
                                    value="PIC Elinst (Select multiple if needed)"
                                />
                                <div
                                    class="mt-1 w-full border border-gray-300 rounded-md"
                                >
                                    <div class="p-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="option in picElinstOptions"
                                            :key="option.value"
                                            @click="
                                                toggleSelection(
                                                    option,
                                                    'pic_elinst'
                                                )
                                            "
                                            class="px-3 py-1 rounded-full cursor-pointer text-sm"
                                            :class="
                                                isSelected(
                                                    option.value,
                                                    'pic_elinst'
                                                )
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                            "
                                        >
                                            {{ option.label }}
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Selected:
                                    {{ form.pic_elinst.join(", ") || "None" }}
                                </p>
                                <InputError :message="form.errors.pic_elinst" />
                            </div>

                            <!-- Multi-select PIC Proses -->
                            <div class="mb-4">
                                <InputLabel
                                    for="pic-proses"
                                    value="PIC Proses (Select multiple if needed)"
                                />
                                <div
                                    class="mt-1 w-full border border-gray-300 rounded-md"
                                >
                                    <div class="p-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="option in picProsesOptions"
                                            :key="option.value"
                                            @click="
                                                toggleSelection(
                                                    option,
                                                    'pic_proses'
                                                )
                                            "
                                            class="px-3 py-1 rounded-full cursor-pointer text-sm"
                                            :class="
                                                isSelected(
                                                    option.value,
                                                    'pic_proses'
                                                )
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                            "
                                        >
                                            {{ option.label }}
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Selected:
                                    {{ form.pic_proses.join(", ") || "None" }}
                                </p>
                                <InputError :message="form.errors.pic_proses" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <InputLabel
                                    for="uk-peminta"
                                    value="UK Peminta"
                                />
                                <div class="relative">
                                    <select
                                        id="uk-peminta"
                                        v-model="form.uk_peminta"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Select UK Peminta
                                        </option>
                                        <option value="AREA GUDANG">
                                            AREA GUDANG
                                        </option>
                                        <option value="AREA IV TAMBANG">
                                            AREA IV TAMBANG
                                        </option>
                                        <option value="AREA KILN IND-V">
                                            AREA KILN IND-V
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError :message="form.errors.uk_peminta" />
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel
                                    for="status-verifikasi"
                                    value="Status Verifikasi ERF"
                                />
                                <div class="relative">
                                    <select
                                        id="status-verifikasi"
                                        v-model="form.status_verifikasi"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Select Status Verifikasi ERF
                                        </option>
                                        <option value="Belum Verifikasi">
                                            Belum Verifikasi
                                        </option>
                                        <option value="Finish Verifikasi">
                                            Finish Verifikasi
                                        </option>
                                        <option value="In Progress Verifikasi">
                                            In Progress Verifikasi
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.status_verifikasi"
                                />
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
                                        v-model="form.deadline_initiating"
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
                                <InputError
                                    :message="form.errors.deadline_initiating"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <InputLabel
                                    for="executing-phase"
                                    value="Deadline Executing Phase"
                                />
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="executing-phase"
                                        v-model="form.deadline_executing"
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
                                <InputError
                                    :message="form.errors.deadline_executing"
                                />
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel for="status" value="Status" />
                                <div class="relative">
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">Select Status</option>
                                        <option value="0-Not started">
                                            0-Not started
                                        </option>
                                        <option value="1-In Progress">
                                            1-In Progress
                                        </option>
                                        <option value="2-Finish">
                                            2-Finish
                                        </option>
                                        <option value="3-Hold">3-Hold</option>
                                        <option value="4-Cancel">
                                            4-Cancel
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError :message="form.errors.status" />
                            </div>

                            <div class="mb-4 relative">
                                <InputLabel for="fase" value="Fase" />
                                <div class="relative">
                                    <select
                                        id="fase"
                                        v-model="form.fase"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">Select Fase</option>
                                        <option value="0-Not started">
                                            0-Not started
                                        </option>
                                        <option value="1-Initiating">
                                            1-Initiating
                                        </option>
                                        <option value="3-Executing">
                                            3-Executing
                                        </option>
                                        <option value="4-Closing">
                                            4-Closing
                                        </option>
                                        <option value="5-Hold">5-Hold</option>
                                        <option value="6-Reject">
                                            6-Reject
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError :message="form.errors.fase" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
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
                                            (e) =>
                                                formatPercentage(
                                                    e,
                                                    'progress_mekanikal'
                                                )
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
                                            (e) =>
                                                formatPercentage(
                                                    e,
                                                    'progress_sipil'
                                                )
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
                                            (e) =>
                                                formatPercentage(
                                                    e,
                                                    'progress_elinst'
                                                )
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

                        <div class="grid grid-cols-3 gap-4">
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
                                            (e) =>
                                                formatPercentage(
                                                    e,
                                                    'progress_proses'
                                                )
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

                            <div class="mb-4 relative">
                                <InputLabel
                                    for="progress-description"
                                    value="Keterangan Progress"
                                />
                                <div class="relative">
                                    <select
                                        id="noted_id"
                                        v-model="form.noted_id"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Select Keterangan Progress
                                        </option>
                                        <option
                                            v-for="noted in noteds"
                                            :key="noted.id"
                                            :value="noted.id"
                                        >
                                            {{ noted.name }}
                                        </option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError :message="form.errors.noted_id" />
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
                                @click="
                                    () => router.visit(route('progress.index'))
                                "
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

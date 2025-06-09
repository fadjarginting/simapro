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
    priorities: Array,
    categories: Array,
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

    <Head title="Add In-Progress Document" />

    <div class="py-2">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div
                    class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                    <h6 class="text-2xl font-semibold mb-2">
                        Create In-Progress Document
                    </h6>
                    <div class="flex items-center justify-end">
                        <Link 
                            class="bg-gray-200 px-3 py-2 text-xs rounded inline-block whitespace-nowrap text-center font-bold leading-none text-gray-700 transition duration-300 hover:bg-gray-300">
                        <i class="fas fa-arrow-left mr-2 text-xs leading-none"></i>
                        <span>Back to List</span>
                        </Link>
                    </div>
                </div>

                <!-- Form -->
                <div class="max-w-full mt-0 p-6 bg-white rounded-lg shadow-md">
                   
                </div>
            </div>
        </div>
    </div>
</template>
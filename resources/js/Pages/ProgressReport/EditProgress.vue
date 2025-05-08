<script setup>
import { useForm, Head, router } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import { ref, watch, computed, onMounted } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

// Get props from controller
const props = defineProps({
    progress: Object,
});

// Setup form inertia with existing data
const form = useForm({
    title: props.progress.title || "",
    plant: props.progress.plant || "",
    work_priority: props.progress.work_priority || "",
    job_type: props.progress.job_type || "",
    request_category: props.progress.request_category || "",
    no_erf: props.progress.no_erf || "",
    erf_approved_date: toInputDateFormat(props.progress.erf_approved_date),
    erf_clarification_date: toInputDateFormat(
        props.progress.erf_clarification_date
    ),
    erf_validated_date: toInputDateFormat(props.progress.erf_validated_date),
    lead_engineering: props.progress.lead_engineering || "",
    pic_mekanikal: props.progress.pic_mekanikal || "",
    progress_mekanikal: props.progress.progress_mekanikal || "",
    pic_sipil: props.progress.pic_sipil || "",
    progress_sipil: props.progress.progress_sipil || "",
    pic_elinst: props.progress.pic_elinst || "",
    progress_elinst: props.progress.progress_elinst || "",
    pic_proses: props.progress.pic_proses || "",
    progress_proses: props.progress.progress_proses || "",
    uk_peminta: props.progress.uk_peminta || "",
    status_verifikasi: props.progress.status_verifikasi || "",
    deadline_initiating: toInputDateFormat(props.progress.deadline_initiating),
    deadline_executing: toInputDateFormat(props.progress.deadline_executing),
    status: props.progress.status || "",
    fase: props.progress.fase || "",
    progress_description: props.progress.progress_description || "",
    note: props.progress.note || "",
    entry_date: toInputDateFormat(props.progress.entry_date),
});

//tanggal
function toInputDateFormat(dateStr) {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    return date.toISOString().slice(0, 10); // Format: YYYY-MM-DD
}

// Date tracking for potential duration calculation
const tglMulai = ref(props.progress.erf_approved_date || "");
const tglSelesai = ref(props.progress.deadline_executing || "");
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

// Calculate initial duration on mount
onMounted(() => {
    if (tglMulai.value && tglSelesai.value) {
        const mulai = new Date(tglMulai.value);
        const selesai = new Date(tglSelesai.value);

        // Calculate days difference
        const diffTime = selesai - mulai;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        durasi.value = diffDays >= 0 ? diffDays : 0;
    }
});

// Submit function for update
function submit() {
    form.put(route("progress.update", props.progress.id), {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: "Progress berhasil diperbarui!",
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
    <Head title="Edit Progress" />

    <!-- form edit progress -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="p-2 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-2">
                            Edit Progress
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
                                <InputLabel for="plant" value="Plant" />
                                <div class="relative">
                                    <select
                                        id="plant"
                                        v-model="form.plant"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">Select Plant</option>
                                        <option value="APLP">APLP</option>
                                        <option value="BINS">BINS</option>
                                        <option value="GENERAL">GENERAL</option>
                                        <option value="GP DUMAI">
                                            GP DUMAI
                                        </option>
                                        <option value="INDARUNG II/III">
                                            INDARUNG II/III
                                        </option>
                                        <option value="INDARUNG IV">
                                            INDARUNG IV
                                        </option>
                                        <option value="INDARUNG V">
                                            INDARUNG V
                                        </option>
                                        <option value="INDARUNG VI">
                                            INDARUNG VI
                                        </option>
                                        <option value="PP BENGKULU">
                                            PP BENGKULU
                                        </option>
                                        <option value="PPI">PPI</option>
                                        <option value="PPTB">PPTB</option>
                                        <option value="TAMBANG">TAMBANG</option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError :message="form.errors.plant" />
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

                            <div class="mb-4 relative">
                                <InputLabel
                                    for="lead"
                                    value="Lead Engineering"
                                />
                                <div class="relative">
                                    <select
                                        id="lead"
                                        v-model="form.lead_engineering"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Select Lead Engineering
                                        </option>
                                        <option
                                            value="AULIA EKADANA FAUTHRISNO"
                                        >
                                            AULIA EKADANA FAUTHRISNO
                                        </option>
                                        <option value="MARJUKI">MARJUKI</option>
                                        <option value="MOCH CHOIRIL ANAM">
                                            MOCH CHOIRIL ANAM
                                        </option>
                                        <option value="NOVRIADI M">
                                            NOVRIADI M
                                        </option>
                                        <option value="YOKE GISKARD">
                                            YOKE GISKARD
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
                                    :message="form.errors.lead_engineering"
                                />
                            </div>

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
                                <InputError
                                    :message="form.errors.pic_mekanikal"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
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
                                <InputError :message="form.errors.pic_sipil" />
                            </div>

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
                                <InputError :message="form.errors.pic_elinst" />
                            </div>

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
                                <InputError :message="form.errors.pic_proses" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <InputLabel
                                    for="uk-peminta"
                                    value="UK Peminta"
                                />
                                <TextInput
                                    id="uk-peminta"
                                    v-model="form.uk_peminta"
                                    placeholder="Select UK Peminta"
                                    class="mt-1 block w-full"
                                    required
                                />
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
                                        id="progress-description"
                                        v-model="form.progress_description"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10"
                                    >
                                        <option value="">
                                            Select Keterangan Progress
                                        </option>
                                        <option
                                            value="Approval dokumen di e-DEMS"
                                        >
                                            Approval dokumen di e-DEMS
                                        </option>
                                        <option
                                            value="Approval dokumen di e-DEMS oleh Ka. Dept."
                                        >
                                            Approval dokumen di e-DEMS oleh Ka.
                                            Dept.
                                        </option>
                                        <option
                                            value="Approval dokumen di e-DEMS oleh Ka. Unit"
                                        >
                                            Approval dokumen di e-DEMS oleh Ka.
                                            Unit
                                        </option>
                                        <option
                                            value="Approval EAT di e-DEMS oleh Ka. Unit"
                                        >
                                            Approval EAT di e-DEMS oleh Ka. Unit
                                        </option>
                                        <option
                                            value="Approval pengesahan ERF oleh User di e-DEMS"
                                        >
                                            Approval pengesahan ERF oleh User di
                                            e-DEMS
                                        </option>
                                        <option value="Belum ada ERF">
                                            Belum ada ERF
                                        </option>
                                        <option
                                            value="Dokumen terkirim ke User melalui e-DEMS"
                                        >
                                            Dokumen terkirim ke User melalui
                                            e-DEMS
                                        </option>
                                        <option
                                            value="ERF belum disetujui oleh User di e-DEMS"
                                        >
                                            ERF belum disetujui oleh User di
                                            e-DEMS
                                        </option>
                                        <option value="Hold">Hold</option>
                                        <option value="Penyusunan dokumen DED">
                                            Penyusunan dokumen DED
                                        </option>
                                        <option
                                            value="Penyusunan dokumen Kajian"
                                        >
                                            Penyusunan dokumen Kajian
                                        </option>
                                        <option value="Proses EAT">
                                            Proses EAT
                                        </option>
                                        <option value="Proses verifikasi ERF">
                                            Proses verifikasi ERF
                                        </option>
                                        <option value="Reject">Reject</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                    >
                                        🔽
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.progress_description"
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
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end form edit progress -->
</template>

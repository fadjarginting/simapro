<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { defineProps } from "vue";
import Swal from "sweetalert2";

defineOptions({
    layout: AuthenticatedLayout,
});

// Deklarasi props
const props = defineProps({
    errors: Object,
    roles: Array,
    user: Object,
    disciplines: Array
});

// Form data menggunakan inertia
const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    role: props.user.role || '',
    discipline_id: props.user.discipline_id || ''
});


// submit with sweetalert2 confirmation, succes, and error handling
const submit = () => {
    Swal.fire({
        title: 'Update User',
        text: "Are you sure you want to update this user?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mr-2',
            cancelButton: 'bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            form.put(route('users.update', props.user.id), {
                onFinish: () => form.reset('password', 'password_confirmation'),
                preserveScroll: true,
                onSuccess: () => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'User has been updated successfully.'
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to update user. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }
    });
};

// Cancel button to go back to the previous page
// with sweetalert2 confirmation
const cancel = () => {
    Swal.fire({
        title: 'Cancel',
        text: "Are you sure you want to cancel?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, go back',
        customClass: {
            confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 mr-2',
            cancelButton: 'bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.history.back();
        }
    });
};
</script>

<template>

    <Head title="Edit User" />

    <div class="py-2">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-pencil-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900 tracking-tight">
                                    Update User Information
                                </h1>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    Please update the user information in the form below.
                                </p>
                            </div>
                        </div>
                        <Link :href="route('users.index')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Back</span>
                        </Link>
                    </div>
                </div>

                <!-- Form -->
                <div class="p-4">
                    <form @submit.prevent="submit" autocomplete="off">
                        <!-- Legenda Keterangan -->
                        <div class="mb-3">
                            <div class="inline-flex items-center space-x-1.5">
                                <span class="text-red-500 text-sm font-bold">*</span>
                                <span class="text-xs text-gray-500">Required field</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Full Name -->
                            <div class="mb-1">
                                <InputLabel for="name" class="text-xs">
                                    <span>Full Name <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <TextInput id="name" v-model="form.name" placeholder="Enter Full Name"
                                    class="mt-1 block w-full text-sm" :class="{ 'border-red-500': errors.name }"
                                    required autofocus autocomplete="off" />
                                <InputError :message="errors.name" class="mt-1" />
                            </div>

                            <!-- Email -->
                            <div class="mb-1">
                                <InputLabel for="email" class="text-xs">
                                    <span>Email <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <TextInput id="email" v-model="form.email" type="email" placeholder="Enter Email"
                                    class="mt-1 block w-full text-sm" :class="{ 'border-red-500': errors.email }"
                                    required aria-autocomplete="false" />
                                <InputError :message="errors.email" class="mt-1" />
                            </div>

                            <!-- Password -->
                            <div class="mb-1">
                                <InputLabel for="password" value="New Password (optional)" class="text-xs" />
                                <TextInput id="password" v-model="form.password" type="password"
                                    placeholder="Enter New Password" class="mt-1 block w-full text-sm"
                                    :class="{ 'border-red-500': errors.password }" autocomplete="new-password" />
                                <InputError :message="errors.password" class="mt-1" />
                            </div>

                            <!-- Password Confirmation -->
                            <div class="mb-1">
                                <InputLabel for="password_confirmation" value="Confirm New Password" class="text-xs" />
                                <TextInput id="password_confirmation" v-model="form.password_confirmation"
                                    type="password" placeholder="Confirm New Password" class="mt-1 block w-full text-sm"
                                    :class="{ 'border-red-500': errors.password_confirmation }"
                                    autocomplete="new-password" />
                                <InputError :message="errors.password_confirmation" class="mt-1" />
                            </div>

                            <!-- Discipline Selection -->
                            <div class="mb-1">
                                <InputLabel for="discipline" class="text-xs">
                                    <span>Discipline <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="discipline" v-model="form.discipline_id" required
                                        class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                                        :class="{ 'border-red-500': errors.discipline_id }">
                                        <option value="">Select Discipline</option>
                                        <option v-for="discipline in disciplines" :key="discipline.id"
                                            :value="discipline.id">
                                            {{ discipline.name }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="errors.discipline_id" class="mt-1" />
                            </div>

                            <!-- Role Selection -->
                            <div class="mb-1">
                                <InputLabel for="role" class="text-xs">
                                    <span>Role <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="role" v-model="form.role" required
                                        class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                                        :class="{ 'border-red-500': errors.role }">
                                        <option value="">Select Role</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="errors.role" class="mt-1" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="cancel"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-50 shadow-sm transition">
                                <i class="fas fa-times text-xs"></i>
                                <span>Cancel</span>
                            </button>
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save"></i>
                                <span>Update User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { defineProps } from "vue";
import Swal from "sweetalert2";

defineOptions({
    layout: AuthenticatedLayout,
});

const page = usePage();

// Deklarasi props
const props = defineProps({
    errors: Object,
    roles: Array,
    disciplines: Array
});



// Form data menggunakan inertia
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    discipline_id:'',
    role: ''
});

// submit with sweetalert2 confirmation, succes, and error handling
const submit = () => {
    Swal.fire({
        title: 'Create User',
        text: "Are you sure you want to create this user?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Create',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mr-2',
            cancelButton: 'bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route('users.store'), {
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
                        title: 'User has been created successfully'
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error',
                        text: page.props.errors.email || 'Failed to create the user. Please try again.',
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

    <Head title="Add User" />

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                            <i class="fas fa-user-plus text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                Add New User
                            </h1>
                            <p class="text-sm text-gray-600">
                                Create a new user account and assign roles.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <h2 class="text-lg font-semibold mb-6 text-gray-800 border-b pb-3">
                            Please fill in user information
                        </h2>
                        <form @submit.prevent="submit" autocomplete="off">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Full Name -->
                                <div class="mb-4">
                                    <InputLabel for="name" value="Full Name" />
                                    <TextInput id="name" v-model="form.name" placeholder="Enter Full Name"
                                        class="mt-1 block w-full" :class="{ 'border-red-500': errors.name }" required
                                        autofocus autocomplete="off" />
                                    <InputError :message="errors.name" />
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <InputLabel for="email" value="Email" />
                                    <TextInput id="email" v-model="form.email" type="email" placeholder="Enter Email"
                                        class="mt-1 block w-full" :class="{ 'border-red-500': errors.email }" required
                                        aria-autocomplete="false" />
                                    <InputError :message="errors.email" />
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <InputLabel for="password" value="Password" />
                                    <TextInput id="password" v-model="form.password" type="password"
                                        placeholder="Enter Password" class="mt-1 block w-full"
                                        :class="{ 'border-red-500': errors.password }" required
                                        autocomplete="new-password" />
                                    <InputError :message="errors.password" />
                                </div>

                                <!-- Password Confirmation -->
                                <div class="mb-4">
                                    <InputLabel for="password_confirmation" value="Confirm Password" />
                                    <TextInput id="password_confirmation" v-model="form.password_confirmation"
                                        type="password" placeholder="Confirm Password" class="mt-1 block w-full"
                                        :class="{ 'border-red-500': errors.password_confirmation }" required
                                        autocomplete="new-password" />
                                    <InputError :message="errors.password_confirmation" />
                                </div>

                                <!-- Discipline Selection -->
                                <div class="mb-4">
                                    <InputLabel for="discipline_id" value="Discipline" />
                                    <select id="discipline_id" v-model="form.discipline_id" required
                                        class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                                        :class="{ 'border-red-500': errors.discipline_id }">
                                        <option value="" disabled>Select Discipline</option>
                                        <option v-for="discipline in disciplines" :key="discipline.id"
                                            :value="discipline.id">
                                            {{ discipline.name }}
                                        </option>
                                    </select>
                                    <InputError :message="errors.discipline_id" />
                                </div>

                                <!-- Role Selection -->
                                <div class="mb-4">
                                    <InputLabel for="role" value="Role" />
                                    <select id="role" v-model="form.role" required
                                        class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                                        :class="{ 'border-red-500': errors.role }">
                                        <option value="" disabled>Select Role</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <InputError :message="errors.role" />
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex mt-8 pt-6 border-t justify-end space-x-4">
                                <button type="button" @click="cancel" :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-times mr-2"></i> Cancel
                                </button>
                                <button type="submit" :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-save mr-2"></i> Save User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
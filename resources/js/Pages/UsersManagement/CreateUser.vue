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
    roles: Array
});



// Form data menggunakan inertia
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
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

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-2">
                        Please fill in user information
                    </h2>
                </div>

                <!-- Form -->
                <div class="max-w-full mt-0 p-6 bg-white rounded-lg shadow-md">
                    <form @submit.prevent="submit" autocomplete="off">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                    class="mt-1 block w-full" :class="{ 'border-red-500': errors.email }" required aria-autocomplete="false"
                                    />
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

                            <!-- Role Selection -->
                            <div class="mb-4">
                                <InputLabel for="role" value="Role" />
                                <select id="role" v-model="form.role" required
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.role }">
                                    <option value="">Select Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <InputError :message="errors.role" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex mt-6 space-x-2 justify-end">
                            <button type="submit" class="bg-transparent px-4 rounded-lg text-blue-500 whitespace-nowrap text-center
                                transition duration-300 hover:bg-blue-500 hover:text-white py-1"
                                :disabled="form.processing">
                                <i class="fas fa-save"></i> Save
                            </button>
                            <button type="button" @click="cancel" class="bg-transparent px-4 rounded-lg text-red-500 whitespace-nowrap text-center
                                transition duration-300 hover:bg-red-500 hover:text-white py-1">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
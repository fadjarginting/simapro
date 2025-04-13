<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { defineProps } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

// Deklarasi props
const props = defineProps({
    errors: Object,
    roles: Array,
    user: Object
});

// Form data menggunakan inertia
const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    role: props.user.role || ''
});

const submit = () => {
    form.put(route('users.update', props.user.id), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const cancel = () => {
    history.back();
};
</script>

<template>

    <Head title="Edit User" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-2">
                        Please update user information
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
                                    class="mt-1 block w-full" :class="{ 'border-red-500': errors.email }" required
                                    autocomplete="email" />
                                <InputError :message="errors.email" />
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <InputLabel for="password" value="Password" />
                                <TextInput id="password" v-model="form.password" type="password"
                                    placeholder="Enter Password" class="mt-1 block w-full"
                                    :class="{ 'border-red-500': errors.password }" autocomplete="new-password" />
                                <InputError :message="errors.password" />
                            </div>

                            <!-- Password Confirmation -->
                            <div class="mb-4">
                                <InputLabel for="password_confirmation" value="Confirm Password" />
                                <TextInput id="password_confirmation" v-model="form.password_confirmation"
                                    type="password" placeholder="Confirm Password" class="mt-1 block w-full"
                                    :class="{ 'border-red-500': errors.password_confirmation }" autocomplete="new-password" />
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

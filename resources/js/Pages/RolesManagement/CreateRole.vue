<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, useForm, Link } from "@inertiajs/vue3";
import { defineOptions, computed } from "vue";
import Swal from "sweetalert2";

defineOptions({
    layout: AuthenticatedLayout,
});

// Ambil data dari controller
const { props } = usePage();
const page = usePage();
// Form data
const form = useForm({
    name: '',
    permissions: [],
});

// permission list grouped by menu
const groupedPermissions = computed(() => props.groupedPermissions || {});
// Helper function to extract group
const extractGroup = (permission) => {
    return permission.replace(/_/g, ' ');
};

// Helper function to extract action from permission string
const extractAction = (permission) => {
    const parts = permission.split('.');
    const action = parts.length > 1 ? parts[1] : permission;
    return action.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
};

// submit with sweetalert2 confirmation, succes, and error handling
const submit = () => {
    Swal.fire({
        title: 'Create Role',
        text: "Are you sure you want to create this role?",
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
            form.post(route('roles.store'), {
                onFinish: () => form.reset(),
                preserveScroll: true,
                onSuccess: () => {
                    if (page.props.errors.name) {
                        Swal.fire({
                            title: 'Error',
                            text: page.props.errors.name,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600'
                            },
                            buttonsStyling: false
                        });
                    } else {
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
                            title: 'Role has been created successfully'
                        });
                    }
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error',
                        text: page.props.errors.name || 'Failed to create the role. Please try again.',
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

const handleCheckboxChange = (event, permissionName) => {
    if (event.target.checked) {
        // Tambahkan permission jika belum ada
        if (!form.permissions.includes(permissionName)) {
            form.permissions.push(permissionName);
        }
    } else {
        // Hapus permission dari array
        form.permissions = form.permissions.filter(p => p !== permissionName);
    }
};

// cancel button with sweetalert2 confirmation
const cancel = () => {
    Swal.fire({
        title: 'Cancel',
        text: "Are you sure you want to cancel?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep editing',
        customClass: {
            confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 mr-2',
            cancelButton: 'bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            history.back();
        }
    });
};
</script>

<template>

    <Head>
        <title>Create New Roles</title>
    </Head>

    <div class="container mx-auto px-4 py-12">
        <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-user-shield text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                            Create New Role
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Please fill in role and permission information.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form add roles -->
            <div class="p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Input role name -->
                    <div>
                        <InputLabel for="role-name" value="Role Name" class="font-semibold text-gray-700" />
                        <TextInput id="role-name" v-model="form.name" type="text" class="mt-1 block w-full" autofocus />
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900">Assign Permissions</h3>

                        <!-- Select all permissions checkbox -->
                        <div class="border rounded-lg p-3 bg-white shadow-sm">
                            <label class="pl-12 flex items-center cursor-pointer">
                                <Checkbox :value="'select-all'"
                                    :checked="form.permissions.length === Object.values(groupedPermissions).flat().length"
                                    @change="(e) => {
                                        if (e.target.checked) {
                                            Object.values(groupedPermissions).flat().forEach(permission => {
                                                if (!form.permissions.includes(permission.name)) {
                                                    form.permissions.push(permission.name);
                                                }
                                            });
                                        } else {
                                            form.permissions = [];
                                        }
                                    }" />
                                <span class="ml-2 text-sm font-semibold text-gray-700">Select All
                                    Permissions</span>
                            </label>
                        </div>

                        <!-- Input role permission -->
                        <div v-for="(perms, groupName) in groupedPermissions" :key="groupName"
                            class="pl-4 border rounded-lg overflow-hidden shadow-sm bg-white">
                            <!-- Group Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 border-b px-4 py-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-blue-900 capitalize">{{ extractGroup(groupName) }}</h4>
                                    <label class="flex items-center cursor-pointer">
                                        <Checkbox :value="`select-all-${groupName}`"
                                            :checked="perms.every(permission => form.permissions.includes(permission.name))"
                                            @change="(e) => {
                                                if (e.target.checked) {
                                                    perms.forEach(permission => {
                                                        if (!form.permissions.includes(permission.name)) {
                                                            form.permissions.push(permission.name);
                                                        }
                                                    });
                                                } else {
                                                    form.permissions = form.permissions.filter(permissionName =>
                                                        !perms.some(permission => permission.name === permissionName)
                                                    );
                                                }
                                            }" />
                                        <span class="ml-2 text-xs text-gray-600">Select All</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Permissions List -->
                            <div class="pl-12 p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="permission in perms" :key="permission.id" class="flex items-center">
                                    <label class="flex items-center cursor-pointer">
                                        <Checkbox name="permissions" :value="permission.name"
                                            :checked="form.permissions.includes(permission.name)"
                                            @change="(e) => handleCheckboxChange(e, permission.name)" />
                                        <span class="ml-2 text-sm text-gray-600 whitespace-normal break-words">
                                            {{ extractAction(permission.name) }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol action -->
                    <div class="flex mt-6 space-x-3 justify-end border-t pt-6">
                        <button type="button" @click="cancel"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-red-500 to-orange-500 rounded-lg hover:from-red-600 hover:to-orange-600 shadow transition">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-save"></i> Save Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
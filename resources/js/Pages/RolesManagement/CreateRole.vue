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
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-3 text-gray-900">
                    <div class="p-2 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-2">Manage Roles</h2>
                        <h6 class="text-lg font-semibold">
                            Please fill in role and permission information
                        </h6>
                    </div>
                </div>

                <!-- Form add roles -->
                <div class="max-w-full mt-0 pt-0 p-6 bg-white rounded-lg shadow-md">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Input role name -->
                        <div class="mb-4">
                            <InputLabel for="role-name" value="Role Name" />
                            <TextInput id="role-name" v-model="form.name" type="text" class="mt-1 block w-full"
                                autofocus />
                        </div>

                        <h6 class="text-lg font-semibold">
                            Input Permission for this Role
                        </h6>

                        <!-- Select all permissions checkbox -->
                        <div class="pl-12">
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
                            <span class="ml-2 text-sm text-gray-600 whitespace-normal break-words">Select All
                                Permissions</span>
                        </div>

                        <!-- Input role permission -->
                        <div v-for="(perms, groupName) in groupedPermissions" :key="groupName"
                            class="mb-4 border-b pb-4">

                            <!-- Nama group (menu) -->
                            <h6 class="text-sm font-semibold mb-2 capitalize">
                                {{ extractGroup(groupName) }}
                            </h6>

                            <!-- Select all checkbox for group -->
                            <div class="pl-12 mb-2">
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
                                <span class="ml-2 text-sm text-gray-600 whitespace-normal break-words">Select All in {{
                                    extractGroup(groupName) }}</span>
                            </div>

                            <!-- Daftar permission di group ini -->
                            <div class="pl-12 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                <div v-for="permission in perms" :key="permission.id" class="flex items-center">
                                    <!-- Checkbox komponen dengan binding update array -->
                                    <Checkbox name="permissions" :value="permission.name"
                                        :checked="form.permissions.includes(permission.name)"
                                        @change="(e) => handleCheckboxChange(e, permission.name)" />
                                    <!-- Teks permission (hanya bagian action) -->
                                    <span class="ml-2 text-sm text-gray-600 whitespace-normal break-words">
                                        {{ extractAction(permission.name) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol action -->
                        <div class="flex mt-6 space-x-4 justify-end">
                            <button type="submit"
                                class="bg-transparent px-4 rounded-lg text-green-500 whitespace-nowrap text-center transition duration-300 hover:bg-green-500 hover:text-white py-1">
                                <i class="fas fa-save"></i> Save
                            </button>
                            <button type="button" @click="cancel"
                                class="bg-transparent px-4 rounded-lg text-red-500 whitespace-nowrap text-center transition duration-300 hover:bg-red-500 hover:text-white py-1">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
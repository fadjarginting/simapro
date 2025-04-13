<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, useForm, Link } from "@inertiajs/vue3";
import { defineOptions, computed } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

// Ambil data dari controller
const { props } = usePage();


// Form data
const form = useForm({
    name: props.role.name || '',
    permissions: props.role.permissions || [],
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

const submit = () => {
    form.patch(route('roles.update', props.role.id));
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

// cancel button
const cancel = () => {
    // Redirect ke halaman sebelumnya
    history.back();
};

</script>

<template>

    <Head>
        <title>Edit Role</title>
    </Head>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-3 text-gray-900">
                    <div class="p-2 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-2">Edit Role</h2>
                        <h6 class="text-lg font-semibold">
                            Please update role and permission information
                        </h6>
                    </div>
                </div>

                <!-- Form edit roles -->
                <div class="max-w-full mt-0 pt-0 p-6 bg-white rounded-lg shadow-md">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Input role name -->
                        <div class=" mb-4">
                            <InputLabel for="role-name" value="Role Name" />
                            <TextInput id="role-name" v-model="form.name" type="text" class="mt-1 block w-full"
                                autofocus />
                        </div>

                        <h6 class="text-lg font-semibold">
                            Update Permissions for this Role
                        </h6>

                        <!-- Input role permission -->
                        <div v-for="(perms, groupName) in groupedPermissions" :key="groupName"
                            class="mb-4 border-b pb-4">

                            <!-- Nama group (menu) -->
                            <h6 class="text-sm font-semibold mb-2 capitalize">
                                {{ extractGroup(groupName) }}
                            </h6>
                            <!-- Daftar permission di group ini -->
                            <div class="pl-12 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                <div v-for="permission in perms" :key="permission.id" class="flex items-center">
                                    <!-- Checkbox komponen dengan binding update array -->
                                    <Checkbox name="permissions" :value="permission.name" v-model="form.permissions"
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

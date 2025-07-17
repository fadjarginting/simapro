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

// Get data from controller
const { props } = usePage();

// Form data - remove non-form fields (flash and errors)
const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions,
});

// Permission list grouped by menu
const groupedPermissions = computed(() => props.groupedPermissions || {});

// Helper function to extract group
const extractGroup = (permission) => {
    return permission.replace(/_/g, " ");
};

// Helper function to extract action from permission string
const extractAction = (permission) => {
    const parts = permission.split(".");
    const action = parts.length > 1 ? parts[1] : permission;
    return action
        .replace(/_/g, " ")
        .replace(/\b\w/g, (char) => char.toUpperCase());
};

// Handle form submission with sweetalert2
import Swal from "sweetalert2";
const submit = () => {
    Swal.fire({
        title: "Edit Role",
        text: "Are you sure you want to update this role?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        customClass: {
            confirmButton:
                "bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 mr-2",
            cancelButton:
                "bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2",
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            form.patch(route("roles.update", props.role.id), {
                onSuccess: () => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener(
                                "mouseenter",
                                Swal.stopTimer
                            );
                            toast.addEventListener(
                                "mouseleave",
                                Swal.resumeTimer
                            );
                        },
                    });

                    Toast.fire({
                        icon: "success",
                        title: "Role has been updated successfully",
                    });
                },
                onError: (errors) => {
                    Swal.fire({
                        title: "Error",
                        text: "Failed to update the role. Please check the form for errors.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton:
                                "bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600",
                        },
                        buttonsStyling: false,
                    });
                },
            });
        }
    });
};

// Cancel button with sweetalert2
const cancel = () => {
    Swal.fire({
        title: "Cancel",
        text: "Are you sure you want to cancel?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, cancel it!",
        cancelButtonText: "No, keep editing",
        customClass: {
            confirmButton:
                "bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 mr-2",
            cancelButton:
                "bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2",
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            history.back();
        }
    });
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
                        <div class="mb-4">
                            <InputLabel for="role-name" value="Role Name" />
                            <TextInput id="role-name" v-model="form.name" type="text" class="mt-1 block w-full"
                                autofocus />
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <h6 class="text-lg font-semibold">
                            Update Permissions for this Role
                        </h6>

                        <!-- Input role permission -->
                        <div v-for="(perms, groupName) in groupedPermissions" :key="groupName"
                            class="mb-4 border-b pb-4">
                            <!-- Group name (menu) -->
                            <h6 class="text-sm font-semibold mb-2 capitalize">
                                {{ extractGroup(groupName) }}
                            </h6>
                            <!-- Permission list in this group -->
                            <div class="pl-12 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                <div v-for="permission in perms" :key="permission.id" class="flex items-center">
                                    <!-- Checkbox component with array binding -->
                                    <Checkbox :id="permission.name" :value="permission.name"
                                        v-model:checked="form.permissions" :checked="form.permissions.includes(
                                            permission.name
                                        )
                                            " />
                                    <!-- Permission text (action part only) -->
                                    <label :for="permission.name"
                                        class="ml-2 text-sm text-gray-600 whitespace-normal break-words">
                                        {{ extractAction(permission.name) }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
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
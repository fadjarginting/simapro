<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <Head title="Add ERF" />

    <!-- form add document -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="p-2 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-2">
                            Please fill in document information
                        </h2>
                    </div>
                </div>
                <!-- FORM -->
                <div class="max-w-full mt-0 p-6 bg-white rounded-lg shadow-md">
                    <form>
                        <div class="mb-4">
                            <InputLabel
                                for="document-code"
                                value="Document Code"
                            />
                            <TextInput
                                id="document-code"
                                placeholder="Enter Document Code"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError />
                        </div>

                        <div class="mb-4">
                            <InputLabel
                                for="document-type"
                                value="Document Type"
                            />
                            <select
                                id="document-type"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-noneocus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none ni-bold-down"
                            >
                                <option>Select Document Type</option>
                                <option>Type 1</option>
                                <option>Type 2</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="title" value="Document Title" />
                            <TextInput
                                id="title"
                                placeholder="Enter Document Title"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Description" />
                            <TextInput
                                id="description"
                                placeholder="Enter Description"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError />
                        </div>

                        <!-- input document -->
                        <div class="mb-4">
                            <InputLabel for="file" value="Document File" />
                            <!-- select docs -->
                            <input
                                type="file"
                                class="block border border-gray-300 p-1 rounded-md w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:disabled:opacity-50 file:disabled:pointer-events-none dark:text-neutral-500 dark:file:bg-blue-500 dark:hover:file:bg-blue-400"
                            />
                        </div>

                        <div class="flex mt-6 space-x-4 justify-end">
                            <button
                                type="button"
                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end form add document -->
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { usePermissions } from "@/composables/permissions";
import { useCategories } from "@/composables/useCategories";

defineProps({
    show: Boolean,
    document: Object
});
const categories = computed(() => props.categories);
const {  formatCategoryPath } = useCategories(categories);
const { hasPermission } = usePermissions();
defineEmits(['close', 'upload-new-version', 'delete', 'show-version-history', 'download', 'ask-to-download']);
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="md">
        <div v-if="document" class="bg-white p-4 rounded shadow dark:bg-slate-850 transition-all duration-300">
            <!-- Header -->
            <header class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Document Details</h3>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </header>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-2 mb-4">
                <button v-if="hasPermission('released_documents.download')" @click="$emit('download', document.id)"
                    class="items-center justify-center px-1 py-1 border border-gray-200 rounded text-sm transition-colors text-blue-500 hover:bg-blue-50">
                    <i class="fas fa-download mr-1"></i> Download
                </button>
                <button v-else @click="$emit('ask-to-download', document)"
                    class="items-center justify-center px-1 py-1 border border-gray-200 rounded text-sm transition-colors col-span-2 text-orange-500 hover:bg-orange-50">
                    <i class="fas fa-question-circle mr-1"></i> Ask to Download
                </button>
                <Link v-if="hasPermission('released_documents.edit')" :href="route('documents.edit', document.id)"
                    class="text-center justify-center px-1 py-1 border border-gray-200 rounded text-sm transition-colors text-yellow-500 hover:bg-yellow-50">
                <i class="fas fa-edit mr-1"></i> Edit
                </Link>
                <button v-if="hasPermission('released_documents.add_new_revision')"
                    @click="$emit('upload-new-version', document)"
                    class="items-center justify-center px-1 py-1 border border-gray-200 rounded text-sm transition-colors text-green-500 hover:bg-green-50">
                    <i class="fas fa-plus mr-1"></i> New Revision
                </button>
                <button v-if="hasPermission('released_documents.delete')" @click="$emit('delete', document.id)"
                    class="items-center justify-center px-1 py-1 border border-gray-200 rounded text-sm transition-colors text-red-500 hover:bg-red-50">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
                <button @click="$emit('show-version-history', document)"
                    class="items-center justify-center px-1 py-1 border border-gray-200 rounded text-sm transition-colors col-span-2 text-blue-500 hover:bg-blue-50">
                    <i class="fas fa-history mr-1"></i> Version History
                </button>
            </div>

            <!-- Document Information -->
            <div class="space-y-4">
                <section>
                    <h4 class="text-sm font-semibold text-blue-500 mb-2">General Information</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-gray-500">Code</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.document_code }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Version</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.version_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">File Size</p>
                            <p class="font-medium text-gray-800 dark:text-white">
                                {{ (document.file_size / (1024 * 1024)).toFixed(2) }} MB
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Publish Date</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.published_at }}</p>
                        </div>
                        <!-- document path -->
                        <div class="col-span-2">
                            <p class="text-gray-500">Path</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ formatCategoryPath(document) }}</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h4 class="text-sm font-semibold text-blue-500 mb-2">Details</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Title</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.title }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Type</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.category }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500">Description</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.description }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500">Uploaded At</p>
                            <p class="font-medium text-gray-800 dark:text-white">
                                {{ document.created_at.split('T')[0] }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Uploaded By</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.created_by }}</p>
                        </div>
                        <div v-if="document.updated_at">
                            <p class="text-gray-500">Last Modified</p>
                            <p class="font-medium text-gray-800 dark:text-white">
                                {{ document.updated_at.split('T')[0] }}
                            </p>
                        </div>
                        <div v-if="document.updated_by">
                            <p class="text-gray-500">Modified By</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ document.updated_by }}</p>
                        </div>

                    </div>
                </section>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { computed } from "vue";
import { usePermissions } from "@/composables/permissions";
import { useCategories } from "@/composables/useCategories";

defineProps({
    show: Boolean,
    document: Object
});
const { hasPermission } = usePermissions();
</script>

<template>
  <Modal :show="show" @close="$emit('close')" maxWidth="4xl">
    <div v-if="document" class="bg-white p-6 rounded shadow dark:bg-slate-850">
      <!-- Header -->
      <header class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
          {{ document.title }}
        </h2>
      </header>

      <!-- Content -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Information -->
        <div class="space-y-4 md:col-span-1">
          <div>
            <p class="text-gray-500 text-sm">Created Time</p>
            <p class="font-medium">{{ document.created_at }}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">ERF Disetujui</p>
            <p class="font-medium">{{ document.erf_approved_at }}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Priority</p>
            <p class="font-medium capitalize">{{ document.priority }}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Status</p>
            <span class="inline-block px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
              {{ document.status }}
            </span>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Fase</p>
            <span class="inline-block px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
              {{ document.phase }}
            </span>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Due Date</p>
            <p class="font-medium">{{ document.due_date ?? 'TBD' }}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Lead Engineering</p>
            <p class="font-medium">{{ document.lead_engineering }}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Assignees</p>
            <ul class="list-disc list-inside text-sm">
              <li v-for="(assignee, index) in document.assignees" :key="index">{{ assignee }}</li>
            </ul>
          </div>
        </div>

        <!-- Right Progress -->
        <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-4">
          <div v-for="progress in document.progresses" :key="progress.label" class="flex flex-col items-center">
            <div class="relative w-24 h-24">
              <!-- Progress Ring -->
              <svg class="w-full h-full" viewBox="0 0 36 36">
                <path
                  class="text-gray-200"
                  stroke-width="3"
                  fill="none"
                  d="M18 2.0845
                  a 15.9155 15.9155 0 0 1 0 31.831
                  a 15.9155 15.9155 0 0 1 0 -31.831"
                />
                <path
                  class="text-blue-500"
                  stroke-width="3"
                  stroke-dasharray="100, 100"
                  :stroke-dasharray="`${progress.percentage}, 100`"
                  stroke-linecap="round"
                  fill="none"
                  d="M18 2.0845
                  a 15.9155 15.9155 0 0 1 0 31.831
                  a 15.9155 15.9155 0 0 1 0 -31.831"
                />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-sm font-bold">{{ progress.percentage }}%</span>
              </div>
            </div>
            <p class="text-sm mt-2 text-gray-700">{{ progress.label }}</p>
          </div>
        </div>
      </div>

      <!-- Table Update -->
      <div class="mt-8">
        <h3 class="text-lg font-semibold mb-4">Update Log</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100 text-gray-700 text-left text-sm">
                <th class="px-4 py-2 border">Date Updated</th>
                <th class="px-4 py-2 border">Fase</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Kumulatif Progress</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="update in document.update_logs" :key="update.id" class="text-sm text-gray-600">
                <td class="px-4 py-2 border">{{ update.date_updated }}</td>
                <td class="px-4 py-2 border">{{ update.phase }}</td>
                <td class="px-4 py-2 border">{{ update.status }}</td>
                <td class="px-4 py-2 border">{{ update.kumulatif_progress }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </Modal>
</template>

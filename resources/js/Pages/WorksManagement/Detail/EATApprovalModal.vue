<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    approval: { type: Object, required: true },
    action: { 
        type: String, 
        required: true,
        validator: (value) => ['approved', 'rejected'].includes(value) 
    }
});

const emit = defineEmits(['close', 'success', 'error']);

const notes = ref('');
const submitting = ref(false);
const showValidation = ref(false);

const isValid = computed(() => 
    props.action !== 'rejected' || notes.value.trim().length > 0
);

// Perbaiki data yang dikirim
const submitApproval = async () => {
    showValidation.value = true;
    if (!isValid.value) return;

    try {
        submitting.value = true;

        // Debug URL yang dihasilkan
        const url = route('eat.approve', props.approval.eat_id);
        console.log('Submitting to:', url);

        const response = await axios.post(url, {
            status: props.action,
            notes: notes.value || null
        });

        if (response.data.success) {
            emit('success', {
                approval: props.approval,
                action: props.action,
                notes: notes.value,
                data: response.data
            });
        } else {
            throw new Error(response.data.message || 'Gagal memproses persetujuan');
        }
    } catch (error) {
        console.error('Error submitting approval:', error);
        emit('error', error.response?.data?.message || 'Gagal memproses persetujuan');
    } finally {
        submitting.value = false;
    }
};

const resetForm = () => {
    notes.value = '';
    showValidation.value = false;
};

defineExpose({ resetForm });
</script>

<template>
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-auto overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div :class="[action === 'approved' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600']" class="w-10 h-10 rounded-full flex items-center justify-center">
                        <i :class="[action === 'approved' ? 'fas fa-check' : 'fas fa-times']" class="text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ action === 'approved' ? 'Setujui' : 'Tolak' }} Persetujuan
                    </h3>
                </div>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-5">
                <div class="space-y-4">
                    <div class="text-sm bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-blue-800">
                            <span class="font-semibold">Approver:</span> {{ approval?.approver || '-' }}
                        </p>
                        <p class="text-blue-800">
                            <span class="font-semibold">Disiplin:</span> {{ approval?.discipline || '-' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Catatan {{ action === 'approved' ? '(Opsional)' : '' }}
                            <span v-if="action === 'rejected'" class="text-red-500">*</span>
                        </label>
                        <textarea 
                            v-model="notes"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            rows="4"
                            :placeholder="action === 'approved' ? 'Tambahkan catatan...' : 'Berikan alasan penolakan...'">
                        </textarea>
                        <p v-if="showValidation && !isValid" 
                           class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                           <i class="fas fa-exclamation-circle"></i>
                            Catatan wajib diisi untuk penolakan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button 
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 shadow-sm transition">
                    Batal
                </button>
                <button 
                    @click="submitApproval"
                    :disabled="submitting || !isValid"
                    :class="[ 
                        'px-4 py-2 text-sm font-semibold text-white rounded-md shadow-sm transition',
                        action === 'approved'
                            ? 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:from-blue-300 disabled:to-blue-400'
                            : 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 disabled:from-red-300 disabled:to-red-400',
                        'disabled:opacity-70 disabled:cursor-not-allowed'
                    ]">
                    <span v-if="submitting" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                    <span v-else class="flex items-center gap-1.5">
                        <i :class="[action === 'approved' ? 'fas fa-check' : 'fas fa-times']"></i>
                        {{ action === 'approved' ? 'Setujui' : 'Tolak' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

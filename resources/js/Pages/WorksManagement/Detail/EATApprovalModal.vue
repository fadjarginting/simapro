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
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-md shadow-lg max-w-md w-full mx-4 overflow-hidden">
            <!-- Header -->
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-base font-medium">
                    {{ action === 'approved' ? 'Setujui' : 'Tolak' }} Persetujuan
                </h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="px-5 py-4">
                <div class="space-y-4">
                    <div class="flex gap-1">
                        <span class="text-sm text-gray-500">Disetujui oleh:</span>
                        <span class="text-sm">{{ approval?.approver || '-' }}</span>
                    </div>
                    
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">
                            Catatan {{ action === 'approved' ? '(Opsional)' : '(Wajib)' }}
                        </label>
                        <textarea 
                            v-model="notes"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-400"
                            rows="3"
                            :placeholder="action === 'approved' ? 'Tambahkan catatan...' : 'Berikan alasan penolakan...'">
                        </textarea>
                        <p v-if="action === 'rejected' && !notes.trim() && showValidation" 
                           class="text-xs text-red-500 mt-1">
                            Catatan wajib diisi untuk penolakan
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-5 py-3 bg-gray-50 flex justify-end gap-2">
                <button 
                    @click="$emit('close')"
                    class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800 transition">
                    Batal
                </button>
                <button 
                    @click="submitApproval"
                    :disabled="submitting || (action === 'rejected' && !notes.trim())"
                    :class="[ 
                        'px-3 py-1.5 text-xs font-medium text-white rounded transition',
                        action === 'approved'
                            ? 'bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300'
                            : 'bg-red-500 hover:bg-red-600 disabled:bg-red-300'
                    ]">
                    <span v-if="submitting" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses
                    </span>
                    <span v-else>
                        {{ action === 'approved' ? 'Setujui' : 'Tolak' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

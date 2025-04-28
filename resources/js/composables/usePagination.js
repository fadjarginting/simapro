import { ref, computed, watch } from 'vue';

export function usePagination(items) {
    const currentPage = ref(1);
    const itemsPerPage = ref(10);

    // This is where the calculation happens for which items to display
    const paginatedItems = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage.value;
        const end = start + itemsPerPage.value;
        return items.value.slice(start, end);
    });

    const totalPages = computed(() => {
        return Math.max(1, Math.ceil(items.value.length / itemsPerPage.value));
    });

    // Reset to page 1 when itemsPerPage changes
    watch(itemsPerPage, () => {
        // Simply reset to page 1 when changing items per page
        currentPage.value = 1;
    });

    // Also watch the items array length for changes
    watch(() => items.value.length, () => {
        // If current page exceeds total pages after filtering
        if (currentPage.value > totalPages.value && totalPages.value > 0) {
            currentPage.value = totalPages.value;
        }
    });

    function prevPage() {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    }

    function nextPage() {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
        }
    }

    return {
        currentPage,
        itemsPerPage,
        paginatedItems,
        totalPages,
        prevPage,
        nextPage
    };
}
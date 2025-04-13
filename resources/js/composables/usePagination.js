import { ref, computed } from 'vue';
export function usePagination(items) {
    const currentPage = ref(1);
    const itemsPerPage = ref(10);

    const paginatedItems = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage.value;
        const end = start + itemsPerPage.value;
        return items.value.slice(start, end);
    });

    const totalPages = computed(() => {
        return Math.ceil(items.value.length / itemsPerPage.value);
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
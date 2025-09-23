import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { PaginationData } from '@/types';

export function usePagination(props: { pagination: PaginationData }, routeName: string) {
    const currentPage = ref(props.pagination.current_page);
    const totalItems = props.pagination.total;
    const itemsPerPage = props.pagination.per_page;
    const siblingCount = ref(1);

    const handlePageChange = (page: number, searchQuery: string = '') => {
        router.get(route(routeName), { 
            page,
            search: searchQuery
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return {
        currentPage,
        totalItems,
        itemsPerPage,
        siblingCount,
        handlePageChange
    };
}

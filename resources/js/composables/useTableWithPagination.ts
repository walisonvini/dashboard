import { usePagination } from './usePagination';
import { useSearch } from './useSearch';
import { PaginationData } from '@/types';
import { router } from '@inertiajs/vue3';

interface SearchConfig {
    searchField?: string;
    additionalParams?: Record<string, any>;
}

export function useTableWithPagination(
    props: { pagination: PaginationData }, 
    routeName: string, 
    searchConfig: SearchConfig = {}
) {
    const pagination = usePagination(props, routeName);
    const search = useSearch(routeName, searchConfig);

    const handlePageChange = (page: number) => {
        const searchParam = search.searchQuery.value;
        const allFilters = {
            search: searchParam,
            ...search.filters
        };
        
        router.get(route(routeName), { 
            page,
            ...allFilters
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return {
        // Pagination
        currentPage: pagination.currentPage,
        totalItems: pagination.totalItems,
        itemsPerPage: pagination.itemsPerPage,
        siblingCount: pagination.siblingCount,
        handlePageChange,
        
        // Search
        searchQuery: search.searchQuery,
        filters: search.filters,
        handleSearch: search.handleSearch,
        updateFilter: search.updateFilter,
        clearFilters: search.clearFilters
    };
}

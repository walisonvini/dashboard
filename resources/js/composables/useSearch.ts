import { ref, onMounted, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

interface SearchConfig {
    searchField?: string;
    additionalParams?: Record<string, any>;
}

export function useSearch(routeName: string, config: SearchConfig = {}) {
    const { searchField = 'search', additionalParams = {} } = config;
    
    const searchQuery = ref('');
    const filters = reactive({ ...additionalParams });

    // Sync search input with URL parameters
    onMounted(() => {
        const urlParams = new URLSearchParams(window.location.search);
        searchQuery.value = urlParams.get(searchField) || '';
        
        // Sync additional parameters
        Object.keys(additionalParams).forEach(key => {
            if (urlParams.has(key)) {
                filters[key] = urlParams.get(key);
            }
        });
    });

    const handleSearch = () => {
        const params = {
            page: 1,
            [searchField]: searchQuery.value,
            ...filters
        };
        
        router.get(route(routeName), params, {
            preserveState: false,
            preserveScroll: true,
        });
    };

    const updateFilter = (key: string, value: any) => {
        filters[key] = value;
    };

    const clearFilters = () => {
        searchQuery.value = '';
        Object.keys(filters).forEach(key => {
            filters[key] = additionalParams[key] || '';
        });
    };

    return {
        searchQuery,
        filters,
        handleSearch,
        updateFilter,
        clearFilters
    };
}

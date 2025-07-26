export function useTicketFormatting() {
    const formatStatus = (status: string): string => {
        const statusMap: Record<string, string> = {
            'open': 'Open',
            'in_progress': 'In Progress',
            'waiting_user': 'Waiting For User',
            'waiting_third_party': 'Waiting For Third Party',
            'resolved': 'Resolved',
            'closed': 'Closed',
            'canceled': 'Canceled'
        };
        
        return statusMap[status] || status;
    };

    const formatPriority = (priority: string): string => {
        const priorityMap: Record<string, string> = {
            'low': 'Low',
            'medium': 'Medium',
            'high': 'High'
        };
        
        return priorityMap[priority] || priority;
    };

    const getStatusVariant = (status: string): string => {
        const variantMap: Record<string, string> = {
            'open': 'open',
            'in_progress': 'in_progress',
            'waiting_user': 'outline',
            'waiting_third_party': 'outline',
            'resolved': 'resolved',
            'closed': 'closed',
            'canceled': 'destructive'
        };
        return variantMap[status] || 'default';
    };

    const getPriorityVariant = (priority: string): string => {
        const variantMap: Record<string, string> = {
            'low': 'low',
            'medium': 'medium',
            'high': 'high'
        };
        return variantMap[priority] || 'default';
    };

    return {
        formatStatus,
        formatPriority,
        getStatusVariant,
        getPriorityVariant
    };
} 
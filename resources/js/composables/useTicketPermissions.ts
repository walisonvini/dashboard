import { computed } from 'vue';
import type { authUser } from '@/types/index';

export const useTicketPermissions = (authUser: authUser) => {
  const canEdit = computed(() => authUser.role !== 'observer');
  const canAddUsers = computed(() => authUser.role !== 'observer');
  const canUploadFiles = computed(() => authUser.role !== 'observer');
  const canComment = computed(() => authUser.role !== 'observer');
  const canAssign = computed(() => authUser.role === 'assigned' || authUser.role === null);
  const canUnassign = computed(() => authUser.role === 'assigned');
  const canView = computed(() => true);
  
  return {
    canEdit,
    canAddUsers,
    canUploadFiles,
    canComment,
    canAssign,
    canUnassign,
    canView,
    userRole: computed(() => authUser.role)
  };
};

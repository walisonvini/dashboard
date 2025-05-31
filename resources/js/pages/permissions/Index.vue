<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permissions',
        href: '/permissions',
    },
];

interface Menu {
    id: number;
    name: string;
    permissions: {
        id: number;
        name: string;
        description: string;
    }[];
}

interface Role {
    id: number;
    name: string;
}

const props = defineProps<{
    roleMenus: Menu[];
    roles: Role[];
}>();

const roleMenus = ref<Menu[]>(props.roleMenus);
const selectedRole = ref('');
const selectedPermissions = ref<string[]>([]);

watch(selectedRole, async (roleId) => {
  if (!roleId) {
    selectedPermissions.value = [];
    return;
  }
  const { data } = await axios.get(`/permissions/role/${roleId}`);
  roleMenus.value = data;

  selectedPermissions.value = [];
  data.forEach(menu => {
    menu.permissions.forEach(p => {
      if (p.checked) selectedPermissions.value.push(p.name);
    });
    menu.children.forEach(child => {
      child.permissions.forEach(p => {
        if (p.checked) selectedPermissions.value.push(p.name);
      });
    });
  });
});

function savePermissions() {
  router.post(`/permissions/${selectedRole.value}`, {
    permissions: selectedPermissions.value
  });
}
</script>

<template>
    <Head title="Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Permissions</h1>
            </div>
            <div class="flex items-center justify-center">
                <select 
                    v-model="selectedRole"
                    class="w-[300px] rounded-md border border-input bg-background px-4 py-2.5 text-base ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                >
                    <option value="">Select a role</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">
                        {{ role.name }}
                    </option>
                </select>
            </div>
            <div v-for="menu in roleMenus" :key="menu.id">
                <div>{{ menu.name }}</div>
                <div v-for="perm in menu.permissions" :key="perm.name">
                    <input type="checkbox" :value="perm.name" v-model="selectedPermissions" /> {{ perm.name }}
                </div>
                <div v-for="child in menu.children" :key="child.id">
                    <div>{{ child.name }}</div>
                    <div v-for="perm in child.permissions" :key="perm.name">
                        <input type="checkbox" :value="perm.name" v-model="selectedPermissions" /> {{ perm.name }}
                    </div>
                </div>
            </div>
            <button @click="savePermissions">Salvar Permissões</button>
        </div>
    </AppLayout>
</template>

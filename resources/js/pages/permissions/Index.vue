<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';

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
        checked?: boolean;
    }[];
    children?: {
        id: number;
        name: string;
        permissions: {
            id: number;
            name: string;
            description: string;
            checked?: boolean;
        }[];
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
const expandedMenus = ref<Set<number>>(new Set());

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
    if (menu.children) {
    menu.children.forEach(child => {
      child.permissions.forEach(p => {
        if (p.checked) selectedPermissions.value.push(p.name);
      });
    });
    }
  });
});

function toggleMenu(menuId: number) {
  if (expandedMenus.value.has(menuId)) {
    expandedMenus.value.delete(menuId);
  } else {
    expandedMenus.value.add(menuId);
  }
}

function isMenuExpanded(menuId: number): boolean {
  return expandedMenus.value.has(menuId);
}

function savePermissions() {
  router.post(`/permissions/${selectedRole.value}`, {
    permissions: selectedPermissions.value
  });
}

function selectAllMenuPermissions(menu: Menu) {
  const allPermissions: string[] = [];
  
  // Add main menu permissions
  menu.permissions.forEach(p => allPermissions.push(p.name));
  
  // Add child menu permissions
  if (menu.children) {
    menu.children.forEach(child => {
      child.permissions.forEach(p => allPermissions.push(p.name));
    });
  }
  
  // Add all permissions that aren't already selected
  allPermissions.forEach(perm => {
    if (!selectedPermissions.value.includes(perm)) {
      selectedPermissions.value.push(perm);
    }
  });
}

function deselectAllMenuPermissions(menu: Menu) {
  const allPermissions: string[] = [];
  
  // Add main menu permissions
  menu.permissions.forEach(p => allPermissions.push(p.name));
  
  // Add child menu permissions
  if (menu.children) {
    menu.children.forEach(child => {
      child.permissions.forEach(p => allPermissions.push(p.name));
    });
  }
  
  // Remove all permissions from this menu
  allPermissions.forEach(perm => {
    const index = selectedPermissions.value.indexOf(perm);
    if (index > -1) {
      selectedPermissions.value.splice(index, 1);
    }
  });
}

function selectAllChildPermissions(child: any) {
  child.permissions.forEach((p: any) => {
    if (!selectedPermissions.value.includes(p.name)) {
      selectedPermissions.value.push(p.name);
    }
  });
}

function deselectAllChildPermissions(child: any) {
  child.permissions.forEach((p: any) => {
    const index = selectedPermissions.value.indexOf(p.name);
    if (index > -1) {
      selectedPermissions.value.splice(index, 1);
    }
  });
}

function isMenuAllSelected(menu: Menu): boolean {
  const allPermissions: string[] = [];
  
  // Add main menu permissions
  menu.permissions.forEach(p => allPermissions.push(p.name));
  
  // Add child menu permissions
  if (menu.children) {
    menu.children.forEach(child => {
      child.permissions.forEach(p => allPermissions.push(p.name));
    });
  }
  
  return allPermissions.length > 0 && allPermissions.every(perm => selectedPermissions.value.includes(perm));
}

function isChildAllSelected(child: any): boolean {
  return child.permissions.length > 0 && child.permissions.every((p: any) => selectedPermissions.value.includes(p.name));
}
</script>

<template>
    <Head title="Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Permissions</h1>
            </div>
            
            <!-- Role Selection -->
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

            <!-- Permissions List -->
            <div v-if="selectedRole && roleMenus.length > 0" class="space-y-4">
                <Card v-for="menu in roleMenus" :key="menu.id">
                    <!-- Menu Header -->
                    <CardHeader 
                        @click="toggleMenu(menu.id)"
                        class="cursor-pointer hover:bg-muted/50 transition-colors"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg 
                                    :class="[
                                        'w-5 h-5 text-muted-foreground transition-transform duration-200',
                                        isMenuExpanded(menu.id) ? 'rotate-90' : ''
                                    ]"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <CardTitle class="text-lg">{{ menu.name }}</CardTitle>
                            </div>
                            <span class="text-sm text-muted-foreground">
                                {{ menu.permissions.length + (menu.children?.reduce((acc, child) => acc + child.permissions.length, 0) || 0) }} permissions
                            </span>
                        </div>
                    </CardHeader>

                    <!-- Menu Content -->
                    <CardContent v-show="isMenuExpanded(menu.id)" class="pt-0">
                        <!-- Select All for Menu -->
                        <div v-if="menu.permissions.length > 0 || (menu.children && menu.children.length > 0)" class="mb-4 pb-4 border-b">
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    :id="'select-all-menu-' + menu.id"
                                    :model-value="isMenuAllSelected(menu)"
                                    @update:modelValue="(checked) => {
                                        if (checked) {
                                            selectAllMenuPermissions(menu);
                                        } else {
                                            deselectAllMenuPermissions(menu);
                                        }
                                    }"
                                />
                                <Label :for="'select-all-menu-' + menu.id" class="text-sm font-medium">
                                    Select all permissions for {{ menu.name }}
                                </Label>
                            </div>
                        </div>

                        <!-- Main Menu Permissions -->
                        <div v-if="menu.permissions.length > 0" class="space-y-4">
                            <div>
                                <Label class="text-sm font-medium">Main Permissions</Label>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                                <div 
                                    v-for="perm in menu.permissions" 
                                    :key="perm.name"
                                    class="flex items-center space-x-2"
                                >
                                    <Checkbox
                                        :id="'perm-' + perm.name"
                                        :model-value="selectedPermissions.includes(perm.name)"
                                        @update:modelValue="(checked) => {
                                            if (checked) {
                                                selectedPermissions.push(perm.name);
                                            } else {
                                                const index = selectedPermissions.indexOf(perm.name);
                                                if (index > -1) {
                                                    selectedPermissions.splice(index, 1);
                                                }
                                            }
                                        }"
                                    />
                                    <Label :for="'perm-' + perm.name" class="text-sm font-normal">
                                        <div>{{ perm.name }}</div>
                                        <div v-if="perm.description" class="text-xs text-muted-foreground">{{ perm.description }}</div>
                                    </Label>
                                </div>
                            </div>
                        </div>

                        <!-- Child Menus -->
                        <div v-if="menu.children && menu.children.length > 0" class="space-y-6">
                            <div v-for="child in menu.children" :key="child.id" class="border-t pt-6">
                                <div class="space-y-4">
                                    <!-- Select All for Child -->
                                    <div class="flex items-center space-x-2">
                                        <Checkbox
                                            :id="'select-all-child-' + child.id"
                                            :model-value="isChildAllSelected(child)"
                                            @update:modelValue="(checked) => {
                                                if (checked) {
                                                    selectAllChildPermissions(child);
                                                } else {
                                                    deselectAllChildPermissions(child);
                                                }
                                            }"
                                        />
                                        <Label :for="'select-all-child-' + child.id" class="text-sm font-medium">
                                            Select all {{ child.name }} permissions
                                        </Label>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                                        <div 
                                            v-for="perm in child.permissions" 
                                            :key="perm.name"
                                            class="flex items-center space-x-2"
                                        >
                                            <Checkbox
                                                :id="'child-perm-' + perm.name"
                                                :model-value="selectedPermissions.includes(perm.name)"
                                                @update:modelValue="(checked) => {
                                                    if (checked) {
                                                        selectedPermissions.push(perm.name);
                                                    } else {
                                                        const index = selectedPermissions.indexOf(perm.name);
                                                        if (index > -1) {
                                                            selectedPermissions.splice(index, 1);
                                                        }
                                                    }
                                                }"
                                            />
                                            <Label :for="'child-perm-' + perm.name" class="text-sm font-normal">
                                                <div>{{ perm.name }}</div>
                                                <div v-if="perm.description" class="text-xs text-muted-foreground">{{ perm.description }}</div>
                                            </Label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Save Button -->
                <div class="flex justify-center pt-6">
                    <Button 
                        @click="savePermissions"
                        class="px-6 py-3"
                    >
                        Save Permissions
                    </Button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="selectedRole && roleMenus.length === 0" class="text-center py-12">
                <div class="text-muted-foreground">
                    <svg class="mx-auto h-12 w-12 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium">No permissions found</h3>
                    <p class="mt-1 text-sm">This role doesn't have any permissions configured.</p>
                </div>
                    </div>

            <!-- Select Role Prompt -->
            <div v-else class="text-center py-12">
                <div class="text-muted-foreground">
                    <svg class="mx-auto h-12 w-12 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium">Select a role</h3>
                    <p class="mt-1 text-sm">Choose a role above to manage its permissions.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

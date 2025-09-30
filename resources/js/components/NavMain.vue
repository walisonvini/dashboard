<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubItem, SidebarMenuSubButton, useSidebar } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const { state, setOpen } = useSidebar();
const openMenus = ref<Record<string, boolean>>({});

const isActive = (href: string) => {
    if (!href) return false;
    
    const cleanHref = href.startsWith('/') ? href.slice(1) : href;
    const currentPath = window.location.pathname.slice(1);
    
    return currentPath === cleanHref || currentPath.startsWith(cleanHref + '/');
};

const normalizePath = (path: string) => path.startsWith('/') ? path : `/${path}`;

const toggleSubmenu = (title: string) => {
    if (state.value === 'collapsed') {
        setOpen(true);
    }
    
    openMenus.value[title] = !openMenus.value[title];
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <template v-if="item.children">
                    <SidebarMenuButton 
                        :is-active="isActive(item.href)"
                        :tooltip="item.title"
                        @click="toggleSubmenu(item.title)"
                    >
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-2">
                                <component :is="item.icon" class="size-4" />
                                <span v-if="state !== 'collapsed'">{{ item.title }}</span>
                            </div>
                            <ChevronRight 
                                v-if="state !== 'collapsed'"
                                class="size-3 transition-transform duration-200"
                                :class="{ 'rotate-90': openMenus[item.title] }"
                            />
                        </div>
                    </SidebarMenuButton>
                </template>
                <Link v-else :href="normalizePath(item.href)" class="block">
                    <SidebarMenuButton 
                        :is-active="isActive(item.href)"
                        :tooltip="item.title"
                    >
                        <div class="flex items-center gap-2">
                            <component :is="item.icon" class="size-4" />
                            <span v-if="state !== 'collapsed'">{{ item.title }}</span>
                        </div>
                    </SidebarMenuButton>
                </Link>
                
                <SidebarMenuSub v-if="item.children && openMenus[item.title] && state !== 'collapsed'">
                    <SidebarMenuSubItem v-for="child in item.children" :key="child.title">
                        <SidebarMenuSubButton 
                            as-child 
                            :is-active="isActive(child.href)"
                        >
                            <Link :href="normalizePath(child.href)">
                                <span>{{ child.title }}</span>
                            </Link>
                        </SidebarMenuSubButton>
                    </SidebarMenuSubItem>
                </SidebarMenuSub>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

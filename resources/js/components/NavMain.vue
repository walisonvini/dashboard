<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubItem, SidebarMenuSubButton } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const openMenus = ref<Record<string, boolean>>({});

const toggleSubmenu = (title: string) => {
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
                        :is-active="item.href === page.url"
                        :tooltip="item.title"
                        @click="toggleSubmenu(item.title)"
                    >
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-2">
                                <component :is="item.icon" class="size-4" />
                                <span>{{ item.title }}</span>
                            </div>
                            <ChevronRight 
                                class="size-3 transition-transform duration-200"
                                :class="{ 'rotate-90': openMenus[item.title] }"
                            />
                        </div>
                    </SidebarMenuButton>
                </template>
                <Link v-else :href="item.href" class="block">
                    <SidebarMenuButton 
                        :is-active="item.href === page.url"
                        :tooltip="item.title"
                    >
                        <div class="flex items-center gap-2">
                            <component :is="item.icon" class="size-4" />
                            <span>{{ item.title }}</span>
                        </div>
                    </SidebarMenuButton>
                </Link>
                
                <SidebarMenuSub v-if="item.children && openMenus[item.title]">
                    <SidebarMenuSubItem v-for="child in item.children" :key="child.title">
                        <SidebarMenuSubButton 
                            as-child 
                            :is-active="child.href === page.url"
                        >
                            <Link :href="child.href">
                                <span>{{ child.title }}</span>
                            </Link>
                        </SidebarMenuSubButton>
                    </SidebarMenuSubItem>
                </SidebarMenuSub>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

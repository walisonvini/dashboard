<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3'
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage()

const convertMenuToNavItem = (menu: any): NavItem => {
    console.log('Menu item:', menu);

    const navItem: NavItem = {
        title: menu.name,
        href: menu.children?.length ? '' : menu.route,
        icon: Icons[menu.icon] || Icons.Home,
    };

    if (menu.children?.length) {
        navItem.children = menu.children.map((child: any) => ({
            title: child.name,
            href: child.route,
        }));
    }

    return navItem;
};

const mainNavItems = computed<NavItem[]>(() => {
    const menus = page.props.menus || [];
    console.log('All menus:', menus);
    return menus.map(convertMenuToNavItem);
});

const footerNavItems: NavItem[] = [
    
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('home')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

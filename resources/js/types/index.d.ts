import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    children?: NavItem[];
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface authUser {
    id: number;
    name: string;
    email: string;
    role: string;
}

export interface PaginationData {
    current_page: number;
    per_page: number;
    total: number;
}

export interface Log {
    id: number;
    user: User;
    user_id: number;
    model_id: number;
    model: string;
    action: string;
    data: [];
    ip_address: string;
    created_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

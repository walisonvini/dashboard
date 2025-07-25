import type { User } from './index';

export interface TicketCategory {
    id: number;
    name: string;
}

export interface Ticket {
    id: number;
    title: string;
    description: string;
    status: string;
    priority: string;
    category: TicketCategory;
    comments?: TicketComment[];
    attachments?: TicketAttachment[];
    users?: User[];
}

export interface TicketAttachment {
    id: number;
    ticket_id: number;
    file_path: string;
    original_name: string;
    uploaded_by: number;
    created_at: string;
}

export interface TicketComment {
    id: number;
    user_id: number;
    comment: string;
    created_at: string;
    user?: {
        id: number;
        name: string;
        email: string;
    };
}
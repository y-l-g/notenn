import type { LucideIcon } from 'lucide-vue-next';

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
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    is_pro: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Tune {
    id: number;
    title: string;
    composer?: Composer;
    origin?: Origin;
    rhythm?: string;
    created_at: string;
}

export interface TuneInfos {
    title: string;
    name: string;
    composer_id: number | null;
    origin_id: number | null;
    meter_id: number | null;
    tempo: string;
    parts: string;
    transcription: string;
    keyInfo: string;
    mode: string;
    words: string;
    rhythm_id: number | null;
    tune_body: string;
}

export interface Arrangement {
    tags: Tag[];
    id: number;
    name?: string;
    tune_body_lines?: string;
    notes_lines?: string;
    words_lines?: string;
    likes_count: number;
    rhythm?: Rhythm;
    tune: Tune;
    meter?: Meter;
    tempo?: number;
    parts?: string;
    transcription?: string;
    key?: string;
    user: User;
    created_at: string;
    is_best_arrangement: boolean;
    source?: string;
    source_url?: string;
}

export interface ArrangementFromMeilisearch extends Arrangement {
    title: string;
    composer_name: string;
    origin_name: string;
    rhythm_name: string;
    user_name: string;
    user_id: number;
    _formatted: {
        title: string;
        composer_name: string;
        origin_name: string;
        tags: string[];
        rhythm_name: string;
    };
}
export interface TuneFromMeilisearch extends Tune {
    title: string;
    _formatted: {
        title: string;
    };
}

export interface Meter {
    id: number;
    name: string;
}

export interface Origin {
    id: number;
    name: string;
}

export interface Composer {
    id: number;
    name: string;
}

export interface Tag {
    id: number;
    name: string;
}

export interface Rhythm {
    id: number;
    name: string;
}

export interface Comment {
    id: number;
    content: string;
    user: User;
    created_at: string;
    commentable_type: string;
    commentable: any;
    commentable_id: number;
    is_suggestion: boolean;
    status: string;
}

export interface Tunebook {
    id: number;
    user: User;
    name: string;
    arrangements_count: number;
    created_at: string;
    updated_at: string;
    tags: string[];
    likes_count: number;
}

export interface TunebookFromMeilisearch extends Tunebook {
    user_id: number;
    user_name;
    _formatted: {
        name: string;
        tags: string[];
        user_name;
    };
}

export interface MeilisearchPaginatedResponse<T> {
    current_page: number;
    data: {
        hits: T[];
    };
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}
export interface PaginatedResponse<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface PostFromMeilisearch {
    id: number;
    slug: string;
    title: string;
    content: string;
    published_at: string;
}

export interface BlogPost {
    id: number;
    slug: string;
    title: string;
    content: string;
    published_at: string;
}

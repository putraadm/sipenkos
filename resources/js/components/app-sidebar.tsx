import { Link, usePage } from '@inertiajs/react';
import { BookOpen, ClipboardClock, Folder, LayoutGrid, Search, Users, UserCircle, UserPlus } from 'lucide-react';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import AppLogo from './app-logo';
import { dashboard } from '@/routes';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Kelola User',
        href: '/admin/user',
        icon: UserPlus,
    },
    {
        title: 'Laporan',
        href: '/laporan-mutasi',
        icon: ClipboardClock,
    },
    {
        title: 'Edit Profil',
        href: '/pemilik-kos/profile/edit',
        icon: UserCircle,
    },
    {
        title: 'Tracking Penghuni',
        href: '/admin/tracking-penghuni',
        icon: Search,
    },
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Repository',
    //     href: 'https://github.com/laravel/react-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#react',
    //     icon: BookOpen,
    // },
];

export function AppSidebar() {
    const { auth } = usePage<any>().props;
    const isPemilikKos = auth?.user?.role?.name === 'pemilik_kos';

    const visibleNavItems = mainNavItems.filter(item => {
        if (isPemilikKos) {
            if (['Laporan', 'Tracking Penghuni', 'Kelola User'].includes(item.title)) {
                return false;
            }
        } else {
            if (item.title === 'Edit Profil') {
                return false;
            }
        }
        return true;
    });

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo isSidebar />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={visibleNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}

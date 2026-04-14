import { useState, FormEvent, useEffect } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { User as UserIcon, Trash2, Edit, Plus, Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import { DataTableServer } from '@/components/ui/data-table-server';
import { ColumnDef, Row, SortingState } from '@tanstack/react-table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kelola User',
        href: '/admin/user',
    },
];

export default function KelolaUser({ users, roles, filters }: { users: any, roles: any[], filters: any }) {
    const { data, setData, post, put, reset, errors, clearErrors } = useForm({
        username: '',
        email: '',
        no_wa: '',
        password: '',
        role_id: roles.length > 0 ? roles[0].id : '',
    });

    const [isAddOpen, setIsAddOpen] = useState(false);
    const [isEditOpen, setIsEditOpen] = useState(false);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);
    const [editId, setEditId] = useState<number | null>(null);
    const [deleteData, setDeleteData] = useState<{ id: number; name: string } | null>(null);

    const [searchQuery, setSearchQuery] = useState(filters?.search || '');
    const [isLoading, setIsLoading] = useState(false);
    const [sorting, setSorting] = useState<SortingState>(
        filters?.sort_by ? [{ id: filters.sort_by, desc: filters.sort_direction === 'desc' }] : [{ id: 'id', desc: false }]
    );

    useEffect(() => {
        const unbindStart = router.on('start', () => setIsLoading(true));
        const unbindFinish = router.on('finish', () => setIsLoading(false));
        return () => {
            unbindStart();
            unbindFinish();
        };
    }, []);

    const onSearch = (e: FormEvent) => {
        e.preventDefault();
        const params: any = { search: searchQuery, page: 1, per_page: users.per_page };
        if (sorting.length > 0) {
            params.sort_by = sorting[0].id;
            params.sort_direction = sorting[0].desc ? 'desc' : 'asc';
        }
        router.get('/admin/user', params, { preserveState: true, preserveScroll: true });
    };

    const handleDelete = (id: number, name: string) => {
        setDeleteData({ id, name });
        setIsDeleteOpen(true);
    };

    const confirmDelete = () => {
        if (deleteData) {
            router.delete(`/admin/user/${deleteData.id}`, {
                onSuccess: () => {
                    setIsDeleteOpen(false);
                    setDeleteData(null);
                },
            });
        }
    };

    const openAddModal = () => {
        reset();
        clearErrors();
        if (roles.length > 0) setData('role_id', roles[0].id);
        setIsAddOpen(true);
    };

    const openEditModal = (user: any) => {
        reset();
        clearErrors();
        setEditId(user.id);
        setData({
            username: user.username,
            email: user.email,
            no_wa: user.no_wa || '',
            password: '',
            role_id: user.role_id,
        });
        setIsEditOpen(true);
    };

    const handleAddSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/admin/user', {
            onSuccess: () => {
                setIsAddOpen(false);
                reset();
            },
        });
    };

    const handleEditSubmit = (e: FormEvent) => {
        e.preventDefault();
        if (editId) {
            put(`/admin/user/${editId}`, {
                onSuccess: () => {
                    setIsEditOpen(false);
                    reset();
                },
            });
        }
    };

    const onPaginationChange = (updaterOrValue: any) => {
        const nextState = typeof updaterOrValue === 'function' ? updaterOrValue({ pageIndex: users.current_page - 1, pageSize: users.per_page }) : updaterOrValue;

        const params: any = {
            search: searchQuery,
            page: nextState.pageIndex + 1,
            per_page: nextState.pageSize
        };
        if (sorting.length > 0) {
            params.sort_by = sorting[0].id;
            params.sort_direction = sorting[0].desc ? 'desc' : 'asc';
        }

        router.get('/admin/user', params, { preserveState: true, preserveScroll: true });
    };

    const onSortingChange = (updaterOrValue: any) => {
        const nextState = typeof updaterOrValue === 'function' ? updaterOrValue(sorting) : updaterOrValue;
        setSorting(nextState);

        const params: any = {
            search: searchQuery,
            page: 1,
            per_page: users.per_page,
            sort_by: nextState[0]?.id || 'id',
            sort_direction: nextState[0]?.desc ? 'desc' : 'asc'
        };

        router.get('/admin/user', params, { preserveState: true, replace: true, preserveScroll: true });
    };

    const columns: ColumnDef<any>[] = [
        {
            id: 'row_number',
            header: 'No',
            enableSorting: false,
            cell: ({ row }) => <span className="font-medium text-slate-900 dark:text-white">{(users.current_page - 1) * users.per_page + row.index + 1}</span>
        },
        {
            accessorKey: 'username',
            header: 'Username',
            enableSorting: true,
            cell: ({ row }) => (
                <div className="flex items-center gap-3">
                    <div className="bg-emerald-50 dark:bg-emerald-900/20 p-2 rounded-full text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                        <UserIcon size={18} />
                    </div>
                    <span className="font-semibold text-slate-800 dark:text-slate-200 text-base">{row.original.username}</span>
                </div>
            )
        },
        {
            accessorKey: 'email',
            header: 'Email',
            enableSorting: true,
            cell: ({ row }) => <span className="text-slate-700 dark:text-slate-300">{row.original.email}</span>
        },
        {
            accessorKey: 'role.name',
            header: 'Role',
            enableSorting: false,
            cell: ({ row }) => (
                <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                    {row.original.role?.name || 'User'}
                </span>
            )
        },
        {
            accessorKey: 'no_wa',
            header: 'No WhatsApp',
            enableSorting: true,
            cell: ({ row }) => (
                <span className="font-medium text-slate-700 dark:text-slate-300">
                    {row.original.no_wa || <span className="text-slate-400 italic">Belum diisi</span>}
                </span>
            )
        },
        {
            id: 'actions',
            header: () => <div className="text-right">Aksi</div>,
            cell: ({ row }) => (
                <div className="flex justify-end gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => openEditModal(row.original)}
                        className="h-9 px-3"
                    >
                        <Edit className="w-4 h-4 mr-1.5" />
                        Edit
                    </Button>
                    <Button
                        variant="destructive"
                        size="sm"
                        onClick={() => handleDelete(row.original.id, row.original.username)}
                        className="h-9 px-3"
                    >
                        <Trash2 className="w-4 h-4 mr-1.5" />
                        Hapus
                    </Button>
                </div>
            )
        }
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kelola User" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 w-full">
                    <div className="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-slate-200 dark:border-slate-800 gap-4">
                        <div>
                            <h2 className="text-xl font-bold text-slate-900 dark:text-white uppercase tracking-wide">Daftar Akun User</h2>
                            <p className="text-sm text-slate-500 mt-1">Kelola data user sistem.</p>
                        </div>
                        
                        <div className="flex flex-col sm:flex-row gap-3 w-full sm:w-auto text-slate-600 dark:text-slate-400">
                            <form onSubmit={onSearch} className="relative flex-1 sm:w-64">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                                <Input 
                                    type="text"
                                    placeholder="Cari user, email, WA..."
                                    className="pl-9 h-10 w-full"
                                    value={searchQuery}
                                    onChange={e => setSearchQuery(e.target.value)}
                                />
                            </form>
                            <Button onClick={openAddModal} className="h-10">
                                <Plus className="h-4 w-4 mr-2" />
                                Tambah User
                            </Button>
                        </div>
                    </div>

                    <DataTableServer
                        columns={columns}
                        data={users.data}
                        rowCount={users.total}
                        pagination={{ pageIndex: users.current_page - 1, pageSize: users.per_page }}
                        setPagination={onPaginationChange}
                        sorting={sorting}
                        onSortingChange={onSortingChange}
                        isLoading={isLoading}
                    />
                </div>
            </div>

            {/* Modal Tambah */}
            <Dialog open={isAddOpen} onOpenChange={setIsAddOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Tambah User</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleAddSubmit} className="space-y-4">
                        <div>
                            <Label htmlFor="username">Username</Label>
                            <Input id="username" value={data.username} onChange={e => setData('username', e.target.value)} className="mt-1" />
                            {errors.username && <p className="text-sm text-red-500 mt-1">{errors.username}</p>}
                        </div>
                        <div>
                            <Label htmlFor="email">Email</Label>
                            <Input id="email" type="email" value={data.email} onChange={e => setData('email', e.target.value)} className="mt-1" />
                            {errors.email && <p className="text-sm text-red-500 mt-1">{errors.email}</p>}
                        </div>
                        <div>
                            <Label htmlFor="no_wa">No WhatsApp</Label>
                            <Input id="no_wa" value={data.no_wa} onChange={e => setData('no_wa', e.target.value)} className="mt-1" />
                            {errors.no_wa && <p className="text-sm text-red-500 mt-1">{errors.no_wa}</p>}
                        </div>
                        <div>
                            <Label htmlFor="role_id">Role</Label>
                            <select id="role_id" className="mt-1 block w-full rounded-md border-slate-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 border text-slate-900 dark:text-white" value={data.role_id} onChange={e => setData('role_id', e.target.value)}>
                                {roles.map(role => (
                                    <option key={role.id} value={role.id}>{role.name}</option>
                                ))}
                            </select>
                            {errors.role_id && <p className="text-sm text-red-500 mt-1">{errors.role_id}</p>}
                        </div>
                        <div>
                            <Label htmlFor="password">Password</Label>
                            <Input id="password" type="password" value={data.password} onChange={e => setData('password', e.target.value)} className="mt-1" />
                            {errors.password && <p className="text-sm text-red-500 mt-1">{errors.password}</p>}
                        </div>
                        <div className="flex justify-end pt-4">
                            <Button type="button" variant="outline" className="mr-2" onClick={() => setIsAddOpen(false)}>Batal</Button>
                            <Button type="submit">Simpan</Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Modal Edit */}
            <Dialog open={isEditOpen} onOpenChange={setIsEditOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Edit User</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleEditSubmit} className="space-y-4">
                        <div>
                            <Label htmlFor="edit_username">Username</Label>
                            <Input id="edit_username" value={data.username} onChange={e => setData('username', e.target.value)} className="mt-1" />
                            {errors.username && <p className="text-sm text-red-500 mt-1">{errors.username}</p>}
                        </div>
                        <div>
                            <Label htmlFor="edit_email">Email</Label>
                            <Input id="edit_email" type="email" value={data.email} onChange={e => setData('email', e.target.value)} className="mt-1" />
                            {errors.email && <p className="text-sm text-red-500 mt-1">{errors.email}</p>}
                        </div>
                        <div>
                            <Label htmlFor="edit_no_wa">No WhatsApp</Label>
                            <Input id="edit_no_wa" value={data.no_wa} onChange={e => setData('no_wa', e.target.value)} className="mt-1" />
                            {errors.no_wa && <p className="text-sm text-red-500 mt-1">{errors.no_wa}</p>}
                        </div>
                        <div>
                            <Label htmlFor="edit_role_id">Role</Label>
                            <select id="edit_role_id" className="mt-1 block w-full rounded-md border-slate-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 border text-slate-900 dark:text-white" value={data.role_id} onChange={e => setData('role_id', e.target.value)}>
                                {roles.map(role => (
                                    <option key={role.id} value={role.id}>{role.name}</option>
                                ))}
                            </select>
                            {errors.role_id && <p className="text-sm text-red-500 mt-1">{errors.role_id}</p>}
                        </div>
                        <div>
                            <Label htmlFor="edit_password">Password (Kosongkan jika tidak diubah)</Label>
                            <Input id="edit_password" type="password" value={data.password} onChange={e => setData('password', e.target.value)} className="mt-1" />
                            {errors.password && <p className="text-sm text-red-500 mt-1">{errors.password}</p>}
                        </div>
                        <div className="flex justify-end pt-4">
                            <Button type="button" variant="outline" className="mr-2" onClick={() => setIsEditOpen(false)}>Batal</Button>
                            <Button type="submit">Simpan Perubahan</Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Modal Konfirmasi Hapus */}
            <Dialog open={isDeleteOpen} onOpenChange={setIsDeleteOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Konfirmasi Hapus User</DialogTitle>
                        <DialogDescription className="pt-2">
                            Apakah Anda yakin ingin menghapus akun <span className="font-bold text-slate-900 dark:text-white">{deleteData?.name}</span>? 
                            <br />
                            Tindakan ini tidak dapat dibatalkan dan semua data terkait akan dihapus secara permanen.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter className="mt-4 gap-2">
                        <Button 
                            variant="outline" 
                            onClick={() => setIsDeleteOpen(false)}
                            disabled={isLoading}
                        >
                            Batal
                        </Button>
                        <Button 
                            variant="destructive" 
                            onClick={confirmDelete}
                            disabled={isLoading}
                        >
                            {isLoading ? 'Menghapus...' : 'Ya, Hapus Akun'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

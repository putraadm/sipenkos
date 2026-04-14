import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import { UserCircle } from 'lucide-react';

export default function ProfileEdit({ user }: { user: any }) {
    const { data, setData, put, processing, errors } = useForm({
        username: user.username || '',
        email: user.email || '',
        no_wa: user.no_wa || '',
    });

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard Pemilik', href: '/pemilik-kos/dashboard' },
        { title: 'Edit Profil', href: '/pemilik-kos/profile/edit' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put('/pemilik-kos/profile/update', {
            onSuccess: () => toast.success('Profil dan Nomor WA berhasil diperbarui.'),
            preserveScroll: true
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Profil Pemilik Kos" />
            <div className="p-6 max-w-2xl mx-auto space-y-6 w-full">
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                    <div className="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div className="p-2 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-full">
                            <UserCircle size={24} />
                        </div>
                        <div>
                            <h2 className="text-xl font-bold text-slate-900 dark:text-white">Pengaturan Profil Pemilik</h2>
                            <p className="text-sm text-slate-500">Perbarui identitas dan nomor WhatsApp untuk notifikasi.</p>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="space-y-2">
                            <Label htmlFor="username" className="font-semibold text-slate-700 dark:text-slate-300">Username</Label>
                            <Input
                                id="username"
                                type="text"
                                required
                                value={data.username}
                                onChange={e => setData('username', e.target.value)}
                                className="h-11 rounded-xl"
                            />
                            {errors.username && <p className="text-red-500 text-xs mt-1">{errors.username}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="email" className="font-semibold text-slate-700 dark:text-slate-300">Email Address</Label>
                            <Input
                                id="email"
                                type="email"
                                required
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                className="h-11 rounded-xl"
                            />
                            {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="no_wa" className="font-semibold text-slate-700 dark:text-slate-300">Nomor WhatsApp</Label>
                            <Input
                                id="no_wa"
                                type="text"
                                required
                                value={data.no_wa}
                                onChange={e => setData('no_wa', e.target.value)}
                                placeholder="081234567890"
                                className="h-11 rounded-xl"
                            />
                            <p className="text-xs text-slate-500">Gunakan awalan 08 atau 62.</p>
                            {errors.no_wa && <p className="text-red-500 text-xs mt-1">{errors.no_wa}</p>}
                        </div>

                        <Button type="submit" disabled={processing} className="w-full h-11 bg-blue-600 hover:bg-blue-700 rounded-xl font-bold transition-all shadow-md shadow-blue-500/20 active:scale-[0.98]">
                            {processing ? 'Menyimpan...' : 'Simpan Profil'}
                        </Button>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}

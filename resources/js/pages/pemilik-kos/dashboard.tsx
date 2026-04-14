import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Home, Users } from 'lucide-react';
import { toast } from 'sonner';

export default function Dashboard({ laporans, flash, errors }: { laporans: any[], flash: any, errors: any }) {
    const currentYear = new Date().getFullYear();
    const currentMonth = (new Date().getMonth() + 1).toString();

    const { data, setData, post, processing, reset } = useForm({
        jumlah_kos: '',
        jumlah_penghuni: '',
        periode_bulan: currentMonth,
        periode_tahun: currentYear.toString(),
    });

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard Pemilik', href: '/pemilik-kos/dashboard' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/pemilik-kos/report', {
            onSuccess: () => {
                toast.success('Laporan berhasil disimpan');
                reset('jumlah_kos', 'jumlah_penghuni');
            },
            onError: (err) => {
                toast.error(err.message || 'Gagal menyimpan laporan');
            }
        });
    };

    const months = [
        { val: '1', label: 'Januari' }, { val: '2', label: 'Februari' }, { val: '3', label: 'Maret' },
        { val: '4', label: 'April' }, { val: '5', label: 'Mei' }, { val: '6', label: 'Juni' },
        { val: '7', label: 'Juli' }, { val: '8', label: 'Agustus' }, { val: '9', label: 'September' },
        { val: '10', label: 'Oktober' }, { val: '11', label: 'November' }, { val: '12', label: 'Desember' }
    ];

    // Auto-show flash messages
    if (flash?.success) {
        toast.success(flash.success);
    }
    if (errors?.message) {
        toast.error(errors.message);
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard Pemilik Kos" />
            <div className="p-6 max-w-4xl mx-auto space-y-6 w-full">

                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                    <div className="mb-6">
                        <h2 className="text-xl font-bold text-slate-900 dark:text-white">Lapor Data Kos Bulanan</h2>
                        <p className="text-sm text-slate-500">Laporkan jumlah kos dan penghuni Anda untuk periode bulan ini.</p>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label>Bulan</Label>
                                <Select value={data.periode_bulan} onValueChange={(v) => setData('periode_bulan', v)}>
                                    <SelectTrigger><SelectValue placeholder="Pilih Bulan" /></SelectTrigger>
                                    <SelectContent>
                                        {months.map(m => <SelectItem key={m.val} value={m.val}>{m.label}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>Tahun</Label>
                                <Select value={data.periode_tahun} onValueChange={(v) => setData('periode_tahun', v)}>
                                    <SelectTrigger><SelectValue placeholder="Pilih Tahun" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value={(currentYear - 1).toString()}>{currentYear - 1}</SelectItem>
                                        <SelectItem value={currentYear.toString()}>{currentYear}</SelectItem>
                                        <SelectItem value={(currentYear + 1).toString()}>{currentYear + 1}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label className="flex items-center gap-2"><Home size={16} /> Jumlah Kos Dimiliki</Label>
                                <Input
                                    type="number"
                                    min="0"
                                    required
                                    value={data.jumlah_kos}
                                    onChange={e => setData('jumlah_kos', e.target.value)}
                                    placeholder="Contoh: 1"
                                />
                            </div>
                            <div className="space-y-2">
                                <Label className="flex items-center gap-2"><Users size={16} /> Total Penghuni</Label>
                                <Input
                                    type="number"
                                    min="0"
                                    required
                                    value={data.jumlah_penghuni}
                                    onChange={e => setData('jumlah_penghuni', e.target.value)}
                                    placeholder="Contoh: 5"
                                />
                            </div>
                        </div>

                        <Button type="submit" disabled={processing} className="w-full bg-blue-600 hover:bg-blue-700">
                            {processing ? 'Menyimpan...' : 'Kirim Laporan'}
                        </Button>
                    </form>
                </div>

                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                    <h3 className="text-lg font-bold text-slate-900 dark:text-white mb-4">Riwayat Laporan</h3>

                    {laporans.length === 0 ? (
                        <div className="text-center py-8 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                            <p className="text-sm text-slate-500">Belum ada laporan yang disubmit.</p>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {laporans.map((laporan: any) => (
                                <div key={laporan.id} className="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <div className="flex gap-4">
                                        <div className="flex flex-col justify-center items-center h-12 w-12 bg-blue-100 dark:bg-blue-900/50 rounded-lg text-blue-700 dark:text-blue-400 font-bold">
                                            <span>{laporan.periode_bulan}</span>
                                        </div>
                                        <div>
                                            <p className="font-bold text-slate-900 dark:text-white">Tahun {laporan.periode_tahun}</p>
                                            <p className="text-xs text-slate-500">Disubmit: {new Date(laporan.created_at).toLocaleDateString('id-ID')}</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-6 text-sm">
                                        <div className="text-center">
                                            <p className="text-slate-500 text-xs">Jml Kos</p>
                                            <p className="font-semibold text-slate-900 dark:text-white">{laporan.jumlah_kos}</p>
                                        </div>
                                        <div className="text-center">
                                            <p className="text-slate-500 text-xs">Jml Penghuni</p>
                                            <p className="font-semibold text-slate-900 dark:text-white">{laporan.jumlah_penghuni}</p>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

            </div>
        </AppLayout>
    );
}

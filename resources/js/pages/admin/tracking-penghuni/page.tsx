import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { Search, User, Clock, History } from 'lucide-react';
import { useState } from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tracking Penghuni',
        href: '/admin/tracking-penghuni',
    },
];

interface Kos {
    kos_id: number;
    nama_kos: string;
}

interface RiwayatMutasi {
    id: number;
    kos_id: number;
    jenis_mutasi: 'masuk' | 'keluar';
    tanggal_mutasi: string;
    kos: Kos | null;
}

interface DataPenghuni {
    penghuni_id: string;
    nama: string;
    nik?: string | null;
    no_wa: string | null;
    agama: string | null;
    file_ktp: string | null;
    file_kk: string | null;
    riwayat_mutasi: RiwayatMutasi[];
}

interface PageProps {
    penghuni: DataPenghuni | null;
    searchQuery: string | null;
}

export default function TrackingPenghuni({ penghuni, searchQuery }: PageProps) {
    const [search, setSearch] = useState(searchQuery || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/admin/tracking-penghuni', { search }, { preserveState: true });
    };

    // Calculate current status
    let currentKos = "Tidak sedang ngekos (Tidak rerekam indikasi ngekos)";
    let tanggalMasuk = "-";

    if (penghuni && penghuni.riwayat_mutasi && penghuni.riwayat_mutasi.length > 0) {
        const latestMutasi = penghuni.riwayat_mutasi[0];
        if (latestMutasi.jenis_mutasi === 'masuk') {
            currentKos = `Ngekos di ${latestMutasi.kos?.nama_kos || "Kos Tidak Diketahui"}`;
            tanggalMasuk = latestMutasi.tanggal_mutasi;
        } else {
            currentKos = `Tidak Ngekos (Telah keluar dari ${latestMutasi.kos?.nama_kos || 'Kos'} pada ${latestMutasi.tanggal_mutasi})`;
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tracking Penghuni" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 w-full max-w-4xl mx-auto">
                    <form onSubmit={handleSearch} className="mb-6">
                        <Label htmlFor="search" className="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Cari Data Riwayat Penghuni
                        </Label>
                        <div className="flex items-center gap-4">
                            <div className="relative flex-1">
                                <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <Search className="h-5 w-5 text-slate-400" />
                                </div>
                                <Input
                                    id="search"
                                    type="text"
                                    placeholder="Masukkan nama spesifik atau NIK penghuni..."
                                    className="pl-10 h-11"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                            </div>
                            <button
                                type="submit"
                                className="inline-flex h-11 items-center justify-center rounded-md bg-blue-600 px-6 font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                            >
                                Cari
                            </button>
                        </div>
                    </form>

                    <div className="space-y-6">
                        {penghuni === null && !searchQuery ? (
                            <div className="flex flex-col h-[200px] items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50">
                                <Search className="mb-2 h-8 w-8 text-slate-400" />
                                <p className="text-sm text-slate-500">Ketikkan nama persis atau NIK untuk melihat tracking mendalam.</p>
                            </div>
                        ) : penghuni === null && searchQuery ? (
                            <div className="flex flex-col h-[200px] items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50">
                                <User className="mb-2 h-8 w-8 text-slate-400" />
                                <p className="text-sm text-slate-500">Data penghuni tidak ditemukan untuk nama/NIK tersebut.</p>
                            </div>
                        ) : penghuni && (
                            <div className="grid gap-8">
                                {/* Form-like Details View */}
                                <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                                    <div className="border-b border-slate-200 dark:border-slate-800 pb-4 mb-6 flex justify-between items-center">
                                        <h3 className="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wide">Data Diri Penghuni</h3>
                                        <span className="text-sm text-slate-500 font-mono">ID: {penghuni.penghuni_id}</span>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-sm md:text-base">
                                        <div className="grid grid-cols-[100px_10px_1fr] md:grid-cols-[120px_10px_1fr] items-start">
                                            <span className="font-semibold text-slate-700 dark:text-slate-300">Nama</span>
                                            <span className="text-slate-500">:</span>
                                            <span className="text-slate-900 dark:text-white capitalize font-medium">{penghuni.nama}</span>
                                        </div>

                                        <div className="grid grid-cols-[100px_10px_1fr] md:grid-cols-[120px_10px_1fr] items-start">
                                            <span className="font-semibold text-slate-700 dark:text-slate-300">NIK</span>
                                            <span className="text-slate-500">:</span>
                                            <span className="text-slate-900 dark:text-white font-mono text-sm">{penghuni.nik || '-'}</span>
                                        </div>

                                        <div className="grid grid-cols-[100px_10px_1fr] md:grid-cols-[120px_10px_1fr] items-start">
                                            <span className="font-semibold text-slate-700 dark:text-slate-300">Agama</span>
                                            <span className="text-slate-500">:</span>
                                            <span className="text-slate-900 dark:text-white capitalize">{penghuni.agama || '-'}</span>
                                        </div>

                                        <div className="grid grid-cols-[100px_10px_1fr] md:grid-cols-[120px_10px_1fr] items-start">
                                            <span className="font-semibold text-slate-700 dark:text-slate-300">No. WA</span>
                                            <span className="text-slate-500">:</span>
                                            <span className="text-slate-900 dark:text-white">{penghuni.no_wa || '-'}</span>
                                        </div>

                                        <div className="grid grid-cols-[100px_10px_1fr] md:grid-cols-[120px_10px_1fr] items-center">
                                            <span className="font-semibold text-slate-700 dark:text-slate-300">Status</span>
                                            <span className="text-slate-500">:</span>
                                            <Badge
                                                variant={currentKos.includes('Tidak Ngekos') ? 'destructive' : 'default'}
                                                className={!currentKos.includes('Tidak Ngekos') ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : ''}
                                            >
                                                {currentKos}
                                            </Badge>
                                        </div>

                                        <div className="md:col-span-2 mt-2 pt-6 border-t border-slate-100 dark:border-slate-800">
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                                <div className="space-y-3">
                                                    <span className="font-semibold text-slate-700 dark:text-slate-300 block">File KTP</span>
                                                    <div className="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 flex items-center justify-center w-full h-[180px] shadow-inner group relative">
                                                        {penghuni.file_ktp ? (
                                                            <>
                                                                <img src={`/storage/${penghuni.file_ktp}`} alt="Preview KTP" className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110 cursor-pointer" onClick={() => window.open(`/storage/${penghuni.file_ktp}`, '_blank')} />
                                                                <div className="absolute inset-0 bg-black/5 group-hover:bg-black/0 transition-colors pointer-events-none" />
                                                            </>
                                                        ) : (
                                                            <div className="text-center">
                                                                <User className="mx-auto h-8 w-8 text-slate-300 mb-1" />
                                                                <span className="text-xs text-slate-400">KTP Tidak terlampir</span>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>

                                                <div className="space-y-3">
                                                    <span className="font-semibold text-slate-700 dark:text-slate-300 block">File KK</span>
                                                    <div className="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 flex items-center justify-center w-full h-[180px] shadow-inner group relative">
                                                        {penghuni.file_kk ? (
                                                            <>
                                                                <img src={`/storage/${penghuni.file_kk}`} alt="Preview KK" className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110 cursor-pointer" onClick={() => window.open(`/storage/${penghuni.file_kk}`, '_blank')} />
                                                                <div className="absolute inset-0 bg-black/5 group-hover:bg-black/0 transition-colors pointer-events-none" />
                                                            </>
                                                        ) : (
                                                            <div className="text-center">
                                                                <History className="mx-auto h-8 w-8 text-slate-300 mb-1" />
                                                                <span className="text-xs text-slate-400">KK Tidak terlampir</span>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Riwayat Pindah Kos */}
                                <div>
                                    <div className="flex items-center gap-2 mb-4 px-1">
                                        <History size={20} className="text-blue-500" />
                                        <h4 className="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wide">Riwayat Mutasi & Pindah Kos</h4>
                                    </div>

                                    {penghuni.riwayat_mutasi && penghuni.riwayat_mutasi.length > 0 ? (
                                        <div className="border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-sm">
                                            <ul className="divide-y divide-slate-100 dark:divide-slate-800">
                                                {penghuni.riwayat_mutasi.map((mutasi) => (
                                                    <li key={mutasi.id} className="p-5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                        <div className="flex items-start justify-between">
                                                            <div className="flex gap-4">
                                                                <div className="mt-0.5 shrink-0">
                                                                    {mutasi.jenis_mutasi === 'masuk' ? (
                                                                        <span className="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30">
                                                                            <span className="text-sm font-bold">+</span>
                                                                        </span>
                                                                    ) : (
                                                                        <span className="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-900/30">
                                                                            <span className="text-sm font-bold">-</span>
                                                                        </span>
                                                                    )}
                                                                </div>
                                                                <div>
                                                                    <p className="text-base font-bold text-slate-900 dark:text-white">
                                                                        {mutasi.kos?.nama_kos || 'Kos Tidak Diketahui'}
                                                                    </p>
                                                                    <p className="text-sm text-slate-600 dark:text-slate-400 mt-0.5">
                                                                        Status: <span className={`font-semibold ${mutasi.jenis_mutasi === 'masuk' ? 'text-emerald-600' : 'text-rose-600'}`}>{mutasi.jenis_mutasi === 'masuk' ? 'Masuk' : 'Keluar'}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div className="text-right">
                                                                <div className="inline-flex items-center gap-1.5 rounded-md bg-slate-100 dark:bg-slate-800 px-3 py-1.5 text-sm font-medium text-slate-600 dark:text-slate-300">
                                                                    <Clock size={14} />
                                                                    {mutasi.tanggal_mutasi}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    ) : (
                                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-xl text-center shadow-sm">
                                            <p className="text-sm text-slate-500 font-medium">Tidak ada riwayat mutasi / belum pernah terekam.</p>
                                        </div>
                                    )}
                                </div>

                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

import { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import laporan from '@/routes/laporan';
import AppLayout from '@/layouts/app-layout';
import { DataTableServer } from '@/components/ui/data-table-server';
import { ColumnDef, Row, SortingState } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { FileText, FileDown, FileSpreadsheet, Search, Users, LogIn, LogOut } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Penghuni {
    nama: string;
    no_wa: string | null;
    file_ktp: string | null;
    file_kk: string | null;
}

interface Kos {
    nama_kos: string;
    nama_pemilik: string;
}

interface MutasiItem {
    id: number;
    tanggal_mutasi: string;
    jenis_mutasi: 'masuk' | 'keluar';
    penghuni: Penghuni | null;
    kos: Kos | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedData<T> {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    per_page: number;
}

interface LaporanProps {
    mutasi: PaginatedData<MutasiItem>;
    filters: {
        search?: string;
        month?: string;
        year?: string;
        jenis_mutasi?: string;
        sort_by?: string;
        sort_direction?: string;
    };
    stats: {
        total_penghuni_aktif: number;
        masuk_bulan_ini: number;
        keluar_bulan_ini: number;
        current_month: string;
        current_year: number;
    };
}

interface BreadcrumbItem {
    title: string;
    href: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Laporan Mutasi',
        href: '/laporan-mutasi',
    },
];

export default function LaporanMutasi({ mutasi, filters, stats }: LaporanProps) {
    const [search, setSearch] = useState<string>(filters.search || '');
    const [month, setMonth] = useState<string>(filters.month || '');
    const [year, setYear] = useState<string>(filters.year || '');
    const [jenisMutasi, setJenisMutasi] = useState<string>(filters.jenis_mutasi || '');
    const [isLoading, setIsLoading] = useState(false);
    const [sorting, setSorting] = useState<SortingState>(
        filters.sort_by ? [{ id: filters.sort_by, desc: filters.sort_direction === 'desc' }] : [{ id: 'tanggal_mutasi', desc: false }]
    );

    useEffect(() => {
        const unbindStart = router.on('start', () => setIsLoading(true));
        const unbindFinish = router.on('finish', () => setIsLoading(false));
        return () => {
            unbindStart();
            unbindFinish();
        };
    }, []);

    const columns: ColumnDef<MutasiItem>[] = [
        {
            accessorKey: 'tanggal_mutasi',
            header: 'Tanggal',
            cell: ({ row }: { row: Row<MutasiItem> }) => <span className="text-sm font-medium text-slate-600">{row.original.tanggal_mutasi}</span>,
        },
        {
            accessorKey: 'penghuni.nama',
            header: 'Penghuni',
            enableSorting: true,
            cell: ({ row }: { row: Row<MutasiItem> }) => (
                <div className="flex flex-col">
                    <span className="text-sm font-bold text-slate-900 dark:text-slate-400">{row.original.penghuni?.nama}</span>
                    <span className="text-xs text-slate-500">WA: {row.original.penghuni?.no_wa || '-'}</span>
                </div>
            ),
        },
        {
            accessorKey: 'kos.nama_kos',
            header: 'Nama Kos',
            enableSorting: true,
            cell: ({ row }: { row: Row<MutasiItem> }) => (
                <div className="flex flex-col">
                    <span className="text-sm font-medium text-slate-800 dark:text-slate-400">{row.original.kos?.nama_kos}</span>
                    <span className="text-xs text-slate-500">Pemilik: {row.original.kos?.nama_pemilik}</span>
                </div>
            ),
        },
        {
            accessorKey: 'jenis_mutasi',
            header: 'Jenis Mutasi',
            enableSorting: true,
            cell: ({ row }: { row: Row<MutasiItem> }) => (
                <Badge variant={row.original.jenis_mutasi === 'masuk' ? 'default' : 'destructive'} className="capitalize">
                    {row.original.jenis_mutasi}
                </Badge>
            ),
        },
        {
            id: 'actions',
            header: 'Dokumen',
            cell: ({ row }: { row: Row<MutasiItem> }) => {
                const ktp = row.original.penghuni?.file_ktp;
                const kk = row.original.penghuni?.file_kk;
                return (
                    <div className="flex gap-2">
                        {ktp ? (
                            <Button variant="outline" size="sm" asChild className="h-8 gap-2 border-blue-200 text-blue-600 hover:bg-blue-50 hover:text-blue-700">
                                <a href={`/storage/${ktp}`} target="_blank" rel="noreferrer">
                                    <FileText size={14} />
                                    KTP
                                </a>
                            </Button>
                        ) : null}
                        {kk ? (
                            <Button variant="outline" size="sm" asChild className="h-8 gap-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700">
                                <a href={`/storage/${kk}`} target="_blank" rel="noreferrer">
                                    <FileText size={14} />
                                    KK
                                </a>
                            </Button>
                        ) : null}
                        {!ktp && !kk && <span className="text-xs text-slate-400 italic">Tidak ada file</span>}
                    </div>
                );
            },
        },
    ];

    useEffect(() => {
        setSearch(filters.search || '');
        setMonth(filters.month || '');
        setYear(filters.year || '');
        setJenisMutasi(filters.jenis_mutasi || '');
        setSorting(
            filters.sort_by ? [{ id: filters.sort_by, desc: filters.sort_direction === 'desc' }] : [{ id: 'tanggal_mutasi', desc: false }]
        );
    }, [filters.search, filters.month, filters.year, filters.jenis_mutasi, filters.sort_by, filters.sort_direction]);

    const onSearch = (value: string) => {
        setSearch(value);

        const params: any = { search: value };
        if (month) params.month = month;
        if (year) params.year = year;
        if (jenisMutasi) params.jenis_mutasi = jenisMutasi;
        if (sorting.length > 0) {
            params.sort_by = sorting[0].id;
            params.sort_direction = sorting[0].desc ? 'desc' : 'asc';
        }

        const url = laporan?.index?.url
            ? laporan.index.url({ query: params })
            : `/laporan-mutasi?${new URLSearchParams(params).toString()}`;

        router.get(url, {}, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    };

    const onFilterChange = (type: 'month' | 'year' | 'jenis_mutasi', value: string) => {
        const val = value === 'all' ? '' : value;
        const newMonth = type === 'month' ? val : month;
        const newYear = type === 'year' ? val : year;
        const newJenis = type === 'jenis_mutasi' ? val : jenisMutasi;

        if (type === 'month') setMonth(val);
        if (type === 'year') setYear(val);
        if (type === 'jenis_mutasi') setJenisMutasi(val);

        const params: any = { search };
        if (newMonth) params.month = newMonth;
        if (newYear) params.year = newYear;
        if (newJenis) params.jenis_mutasi = newJenis;
        if (sorting.length > 0) {
            params.sort_by = sorting[0].id;
            params.sort_direction = sorting[0].desc ? 'desc' : 'asc';
        }

        const url = laporan?.index?.url
            ? laporan.index.url({ query: params })
            : `/laporan-mutasi?${new URLSearchParams(params).toString()}`;

        router.get(url, {}, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    };

    const onPaginationChange = (updaterOrValue: any) => {
        const nextState = typeof updaterOrValue === 'function' ? updaterOrValue({ pageIndex: mutasi.current_page - 1, pageSize: mutasi.per_page }) : updaterOrValue;

        const params: any = {
            search: search,
            page: nextState.pageIndex + 1,
            per_page: nextState.pageSize
        };
        if (month) params.month = month;
        if (year) params.year = year;
        if (jenisMutasi) params.jenis_mutasi = jenisMutasi;
        if (sorting.length > 0) {
            params.sort_by = sorting[0].id;
            params.sort_direction = sorting[0].desc ? 'desc' : 'asc';
        }

        const url = laporan?.index?.url
            ? laporan.index.url({ query: params })
            : `/laporan-mutasi?${new URLSearchParams(params).toString()}`;

        router.get(url, {}, {
            preserveState: true,
            preserveScroll: true
        });
    };

    const onSortingChange = (updaterOrValue: any) => {
        const nextState = typeof updaterOrValue === 'function' ? updaterOrValue(sorting) : updaterOrValue;
        setSorting(nextState);

        const params: any = {
            search,
            month,
            year,
            jenis_mutasi: jenisMutasi,
            page: 1, // Reset to first page on sort change
            per_page: mutasi.per_page,
            sort_by: nextState[0]?.id || 'tanggal_mutasi',
            sort_direction: nextState[0]?.desc ? 'desc' : 'asc'
        };

        const url = laporan?.index?.url
            ? laporan.index.url({ query: params })
            : `/laporan-mutasi?${new URLSearchParams(params).toString()}`;

        router.get(url, {}, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    };

    const onExport = (format: 'pdf' | 'excel') => {
        const params: any = { search };
        if (month) params.month = month;
        if (year) params.year = year;
        if (jenisMutasi) params.jenis_mutasi = jenisMutasi;
        if (sorting.length > 0) {
            params.sort_by = sorting[0].id;
            params.sort_direction = sorting[0].desc ? 'desc' : 'asc';
        }

        const queryString = new URLSearchParams(params).toString();
        window.open(`/laporan-mutasi/${format}?${queryString}`, '_blank');
    };

    return (
        <>
            <Head title="Laporan Mutasi" />

            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="flex flex-col gap-2">
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Laporan Mutasi Penghuni</h1>
                    <p className="text-sm text-slate-500 dark:text-slate-400">Kelola dan tinjau riwayat perpindahan penghuni kos di wilayah Anda.</p>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div className="flex items-center gap-4 rounded-2xl border border-blue-100 bg-blue-50/50 p-4 dark:border-blue-900/50 dark:bg-blue-900/20">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 text-white shadow-lg shadow-blue-200 dark:shadow-none">
                            <Users size={24} />
                        </div>
                        <div className="flex flex-col">
                            <span className="text-sm font-medium text-slate-500 dark:text-slate-400">Total Penghuni Aktif</span>
                            <span className="text-2xl font-bold text-slate-900 dark:text-white">{stats.total_penghuni_aktif.toLocaleString('id')}</span>
                        </div>
                    </div>
                    <div className="flex items-center gap-4 rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-900/50 dark:bg-emerald-900/20">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-200 dark:shadow-none">
                            <LogIn size={24} />
                        </div>
                        <div className="flex flex-col">
                            <span className="text-sm font-medium text-slate-500 dark:text-slate-400">Masuk ({stats.current_month})</span>
                            <span className="text-2xl font-bold text-slate-900 dark:text-white">{stats.masuk_bulan_ini.toLocaleString('id')}</span>
                        </div>
                    </div>
                    <div className="flex items-center gap-4 rounded-2xl border border-rose-100 bg-rose-50/50 p-4 dark:border-rose-900/50 dark:bg-rose-900/20">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-500 text-white shadow-lg shadow-rose-200 dark:shadow-none">
                            <LogOut size={24} />
                        </div>
                        <div className="flex flex-col">
                            <span className="text-sm font-medium text-slate-500 dark:text-slate-400">Keluar ({stats.current_month})</span>
                            <span className="text-2xl font-bold text-slate-900 dark:text-white">{stats.keluar_bulan_ini.toLocaleString('id')}</span>
                        </div>
                    </div>
                </div>

                <div className="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div className="flex flex-col items-end gap-4 md:flex-row md:items-center md:justify-between">
                        {/* Left: Filters */}
                        <div className="flex flex-wrap items-center gap-3">
                            <div className="grid w-full max-w-sm items-center gap-1.5 sm:w-auto">
                                <Label className="text-xs font-semibold text-slate-500">Bulan</Label>
                                <Select value={month || 'all'} onValueChange={(v) => onFilterChange('month', v)}>
                                    <SelectTrigger className="h-9 w-full sm:w-[130px]">
                                        <SelectValue placeholder="Semua Bulan" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Semua Bulan</SelectItem>
                                        {['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'].map((m, i) => (
                                            <SelectItem key={i} value={(i + 1).toString()}>{m}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid w-full max-w-sm items-center gap-1.5 sm:w-auto">
                                <Label className="text-xs font-semibold text-slate-500">Tahun</Label>
                                <Select value={year || 'all'} onValueChange={(v) => onFilterChange('year', v)}>
                                    <SelectTrigger className="h-9 w-full sm:w-[100px]">
                                        <SelectValue placeholder="Tahun" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Semua</SelectItem>
                                        {[2024, 2025, 2026].map((y) => (
                                            <SelectItem key={y} value={y.toString()}>{y}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid w-full max-w-sm items-center gap-1.5 sm:w-auto">
                                <Label className="text-xs font-semibold text-slate-500">Jenis</Label>
                                <Select value={jenisMutasi || 'all'} onValueChange={(v) => onFilterChange('jenis_mutasi', v)}>
                                    <SelectTrigger className="h-9 w-full sm:w-[120px]">
                                        <SelectValue placeholder="Semua Jenis" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Semua Jenis</SelectItem>
                                        <SelectItem value="masuk">Masuk</SelectItem>
                                        <SelectItem value="keluar">Keluar</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        {/* Right: Search & Exports */}
                        <div className="flex w-full flex-wrap items-center justify-end gap-2 md:w-auto">
                            <div className="relative w-full sm:w-64">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                <Input
                                    placeholder="Cari penghuni atau kos..."
                                    value={search}
                                    onChange={(e) => onSearch(e.target.value)}
                                    className="h-9 pl-9"
                                />
                            </div>
                            <div className="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className="h-9 gap-2 border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700"
                                    onClick={() => onExport('pdf')}
                                >
                                    <FileDown size={16} />
                                    PDF
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className="h-9 gap-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700"
                                    onClick={() => onExport('excel')}
                                >
                                    <FileSpreadsheet size={16} />
                                    Excel
                                </Button>
                            </div>
                        </div>
                    </div>

                    <DataTableServer
                        columns={columns}
                        data={mutasi.data}
                        rowCount={mutasi.total}
                        pagination={{ pageIndex: mutasi.current_page - 1, pageSize: mutasi.per_page }}
                        setPagination={onPaginationChange}
                        sorting={sorting}
                        onSortingChange={onSortingChange}
                        isLoading={isLoading}
                    />
                </div>
            </div>
        </>
    );
}

LaporanMutasi.layout = (page: React.ReactNode) => (
    <AppLayout breadcrumbs={breadcrumbs}>{page}</AppLayout>
);

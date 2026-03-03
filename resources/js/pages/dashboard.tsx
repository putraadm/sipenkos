import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { Users, Home, ArrowLeftRight, TrendingUp } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
    Cell,
} from 'recharts';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface DashboardProps {
    stats: {
        total_penghuni: number;
        total_kos: number;
        mutasi_bulan_ini: number;
    };
    chartData: {
        name: string;
        masuk: number;
        keluar: number;
    }[];
    recentActivities: any[];
}

export default function Dashboard({ stats, chartData, recentActivities }: DashboardProps) {
    const statCards = [
        { label: 'Total Penghuni', value: stats.total_penghuni.toString(), icon: Users, color: 'text-blue-600', bg: 'bg-blue-100 dark:bg-blue-900/30' },
        { label: 'Total Kos', value: stats.total_kos.toString(), icon: Home, color: 'text-indigo-600', bg: 'bg-indigo-100 dark:bg-indigo-900/30' },
        { label: 'Mutasi Bulan Ini', value: stats.mutasi_bulan_ini.toString(), icon: ArrowLeftRight, color: 'text-emerald-600', bg: 'bg-emerald-100 dark:bg-emerald-900/30' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {statCards.map((stat, i) => (
                        <div key={i} className="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 transition-all duration-300 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                            <div className="flex items-center gap-4">
                                <div className={`flex h-12 w-12 items-center justify-center rounded-xl ${stat.bg} ${stat.color}`}>
                                    <stat.icon size={24} />
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-slate-500 dark:text-slate-400">{stat.label}</p>
                                    <p className="text-2xl font-bold text-slate-900 dark:text-white">{stat.value}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    {/* Mutation Trend Chart */}
                    <div className="relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
                        <div className="mb-6 flex items-center justify-between">
                            <div>
                                <h2 className="text-lg font-bold text-slate-900 dark:text-white">Rekap Mutasi</h2>
                                <p className="text-sm text-slate-500 dark:text-slate-400">Tren penghuni masuk & keluar 6 bulan terakhir.</p>
                            </div>
                            <div className="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1.5 dark:bg-slate-800/50">
                                <TrendingUp size={16} className="text-blue-500" />
                                <span className="text-xs font-semibold text-slate-600 dark:text-slate-300">Statistik Live</span>
                            </div>
                        </div>

                        <div className="h-[300px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart
                                    data={chartData}
                                    margin={{ top: 10, right: 10, left: -20, bottom: 0 }}
                                    barGap={8}
                                >
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#E2E8F0" />
                                    <XAxis
                                        dataKey="name"
                                        axisLine={false}
                                        tickLine={false}
                                        tick={{ fill: '#64748B', fontSize: 12 }}
                                        dy={10}
                                    />
                                    <YAxis
                                        axisLine={false}
                                        tickLine={false}
                                        tick={{ fill: '#64748B', fontSize: 12 }}
                                    />
                                    <Tooltip
                                        cursor={{ fill: '#F1F5F9', radius: 8 }}
                                        contentStyle={{
                                            borderRadius: '12px',
                                            border: 'none',
                                            boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                                        }}
                                    />
                                    <Legend
                                        verticalAlign="top"
                                        align="right"
                                        iconType="circle"
                                        formatter={(value) => <span className="text-xs font-medium text-slate-600 dark:text-slate-300 capitalize">{value}</span>}
                                        wrapperStyle={{ paddingBottom: '20px' }}
                                    />
                                    <Bar
                                        dataKey="masuk"
                                        name="Masuk"
                                        fill="#10B981"
                                        radius={[4, 4, 0, 0]}
                                        barSize={16}
                                    />
                                    <Bar
                                        dataKey="keluar"
                                        name="Keluar"
                                        fill="#F43F5E"
                                        radius={[4, 4, 0, 0]}
                                        barSize={16}
                                    />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* Recent Activities */}
                    <div className="relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div className="mb-6">
                            <h2 className="text-lg font-bold text-slate-900 dark:text-white">Aktivitas Terbaru</h2>
                            <p className="text-sm text-slate-500 dark:text-slate-400">Mutasi penghuni kos terbaru.</p>
                        </div>

                        <div className="flex-1 space-y-4">
                            {recentActivities.length > 0 ? (
                                recentActivities.map((activity) => (
                                    <div key={activity.id} className="flex items-start gap-4 rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50">
                                        <div className={`mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full ${activity.jenis_mutasi === 'masuk' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'}`}>
                                            <ArrowLeftRight size={14} />
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-bold text-slate-900 dark:text-white">
                                                {activity.penghuni?.nama}
                                            </p>
                                            <p className="mt-0.5 text-xs text-slate-500">
                                                {activity.jenis_mutasi === 'masuk' ? 'Masuk ke' : 'Keluar dari'} {activity.kos?.nama_kos}
                                            </p>
                                            <p className="mt-1 text-[10px] text-slate-400 font-medium">{activity.tanggal_mutasi}</p>
                                        </div>
                                        <Badge variant={activity.jenis_mutasi === 'masuk' ? 'default' : 'destructive'} className="h-5 px-1.5 text-[10px] capitalize">
                                            {activity.jenis_mutasi}
                                        </Badge>
                                    </div>
                                ))
                            ) : (
                                <div className="flex h-[200px] items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50">
                                    <p className="text-sm text-slate-400">Belum ada aktivitas</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

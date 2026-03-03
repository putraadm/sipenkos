import { Link } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';
import type { AuthLayoutProps } from '@/types';
import { login } from '@/routes';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden bg-slate-50 p-6 md:p-10 dark:bg-slate-950">
            {/* Background Decorations */}
            <div className="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div className="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-blue-500/20 blur-[120px] dark:bg-blue-500/15" />
                <div className="absolute top-[60%] -right-[10%] h-[40%] w-[40%] rounded-full bg-indigo-500/20 blur-[120px] dark:bg-indigo-500/15" />
                <div className="absolute inset-0 bg-grid-slate dark:bg-grid-white" />
            </div>

            <div className="glass relative z-10 w-full max-w-md rounded-2xl p-8 transition-all duration-300 hover:shadow-2xl md:p-10">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link
                            href={login().url}
                            className="flex flex-col items-center gap-2 font-medium transition-transform duration-300 hover:scale-105"
                        >
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-500/30">
                                <AppLogoIcon className="size-8 fill-current text-white" />
                            </div>
                            <span className="sr-only">{title}</span>
                        </Link>

                        <div className="space-y-1.5 text-center">
                            <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{title}</h1>
                            <p className="text-center text-sm font-medium text-slate-500 dark:text-slate-400">
                                {description}
                            </p>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}

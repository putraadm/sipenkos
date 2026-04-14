import AppLogoIcon from './app-logo-icon';

export default function AppLogo({ isSidebar }: { isSidebar?: boolean }) {
    return (
        <>
            <div className="flex aspect-square size-10 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-500/20">
                <AppLogoIcon className="size-10 text-white" isSidebar={isSidebar} />
            </div>
            <div className="grid flex-1 text-left text-lg">
                <span className="mb-0.5 truncate leading-tight font-bold">
                    SIPENKOS
                </span>
            </div>
        </>
    );
}

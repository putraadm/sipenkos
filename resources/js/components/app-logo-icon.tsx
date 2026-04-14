import { cn } from '@/lib/utils';
import type { ImgHTMLAttributes } from 'react';

interface AppLogoIconProps extends ImgHTMLAttributes<HTMLImageElement> {
    isSidebar?: boolean;
}

export default function AppLogoIcon({ isSidebar, className, ...props }: AppLogoIconProps) {
    if (isSidebar) {
        return (
            <>
                <img
                    {...props}
                    src="/favicon-96x96-light.png"
                    alt="SIPENKOS Logo"
                    className={cn(className, 'dark:hidden')}
                />
                <img
                    {...props}
                    src="/favicon-96x96.png"
                    alt="SIPENKOS Logo"
                    className={cn(className, 'hidden dark:block')}
                />
            </>
        );
    }

    return <img {...props} src="/favicon-96x96.png" alt="SIPENKOS Logo" className={className} />;
}

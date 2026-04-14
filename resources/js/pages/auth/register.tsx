import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import { store } from '@/routes/register';

export default function Register() {
    return (
        <AuthLayout
            title="Create an account"
            description="Enter your details below to create your account"
        >
            <Head title="Register" />
            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-5">
                            <div className="grid gap-2">
                                <Label htmlFor="username" className="text-sm font-semibold text-slate-700 dark:text-slate-300">Username</Label>
                                <Input
                                    id="username"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="username"
                                    name="username"
                                    placeholder="johndoe"
                                    className="h-11 rounded-xl border-slate-200 bg-white/50 focus:border-blue-500 focus:ring-blue-500/20 dark:border-slate-800 dark:bg-slate-900/50"
                                />
                                <InputError
                                    message={errors.username}
                                    className="mt-1"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email" className="text-sm font-semibold text-slate-700 dark:text-slate-300">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="john@example.com"
                                    className="h-11 rounded-xl border-slate-200 bg-white/50 focus:border-blue-500 focus:ring-blue-500/20 dark:border-slate-800 dark:bg-slate-900/50"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="no_wa" className="text-sm font-semibold text-slate-700 dark:text-slate-300">Nomor WhatsApp</Label>
                                <Input
                                    id="no_wa"
                                    type="text"
                                    required
                                    tabIndex={3}
                                    name="no_wa"
                                    placeholder="081234567890"
                                    className="h-11 rounded-xl border-slate-200 bg-white/50 focus:border-blue-500 focus:ring-blue-500/20 dark:border-slate-800 dark:bg-slate-900/50"
                                />
                                <InputError message={errors.no_wa as string} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password" className="text-sm font-semibold text-slate-700 dark:text-slate-300">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabIndex={4}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="••••••••"
                                    className="h-11 rounded-xl border-slate-200 bg-white/50 focus:border-blue-500 focus:ring-blue-500/20 dark:border-slate-800 dark:bg-slate-900/50"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation" className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Confirm password
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabIndex={4}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="••••••••"
                                    className="h-11 rounded-xl border-slate-200 bg-white/50 focus:border-blue-500 focus:ring-blue-500/20 dark:border-slate-800 dark:bg-slate-900/50"
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <Button
                                type="submit"
                                className="h-11 w-full rounded-xl bg-blue-600 font-bold text-white shadow-lg shadow-blue-500/25 transition-all hover:bg-blue-700 hover:shadow-blue-500/35 active:scale-[0.98] disabled:opacity-70 mt-2"
                                tabIndex={5}
                                data-test="register-user-button"
                                disabled={processing}
                            >
                                {processing ? <Spinner className="mr-2 h-4 w-4" /> : null}
                                Create Account
                            </Button>
                        </div>

                        <div className="text-center text-sm font-medium text-slate-500 dark:text-slate-400">
                            Already have an account?{' '}
                            <TextLink href={login()} tabIndex={6} className="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                Log in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}

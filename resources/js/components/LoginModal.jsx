import React, {useEffect, useState} from 'react';

export default function LoginModal() {
    const [open, setOpen] = useState(false);

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);


    /*
     * Open the modal automatically when PHP redirects
     * the visitor to the homepage with ?login=1.
     *
     * Links/buttons with data-login-trigger can also
     * open the same modal from anywhere on the website.
     */
    useEffect(() => {
        const params = new URLSearchParams(
            window.location.search
        );

        if (params.get('login') === '1') {
            setOpen(true);
        }

        const handleLoginTrigger = event => {
            const trigger = event.target.closest(
                '[data-login-trigger]'
            );

            if (!trigger) {
                return;
            }

            event.preventDefault();

            setError('');
            setOpen(true);
        };

        document.addEventListener(
            'click',
            handleLoginTrigger
        );

        return () => {
            document.removeEventListener(
                'click',
                handleLoginTrigger
            );
        };
    }, []);


    /*
     * Prevent the page underneath the modal from scrolling.
     */
    useEffect(() => {
        if (!open) {
            return;
        }

        const previousOverflow =
            document.body.style.overflow;

        document.body.style.overflow = 'hidden';

        return () => {
            document.body.style.overflow =
                previousOverflow;
        };
    }, [open]);


    /*
     * Allow Escape to close the modal.
     */
    useEffect(() => {
        const handleKeyDown = event => {
            if (
                event.key === 'Escape'
                && !loading
            ) {
                setOpen(false);
            }
        };

        document.addEventListener(
            'keydown',
            handleKeyDown
        );

        return () => {
            document.removeEventListener(
                'keydown',
                handleKeyDown
            );
        };
    }, [loading]);


    const authenticate = async event => {
        event.preventDefault();

        setLoading(true);
        setError('');

        const formData = new FormData();

        formData.append('email', email);
        formData.append('password', password);

        try {
            const response = await fetch(
                '/public/index.php?route=/login',
                {
                    method: 'POST',
                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.error || 'Impossible de se connecter.'
                );
            }

            window.location.href = data.redirect;

        } catch (error) {
            setError(error.message);

        } finally {
            setLoading(false);
        }
    };


    if (!open) {
        return null;
    }


    return (
        <div
            className="
                fixed inset-0 z-[100]
                flex items-center justify-center
                bg-black/50
                p-4
                backdrop-blur-xl
            "
            onMouseDown={event => {
                if (
                    event.target === event.currentTarget
                    && !loading
                ) {
                    setOpen(false);
                }
            }}
        >
            <div
                className="
                    relative
                    w-full max-w-md
                    overflow-hidden
                    rounded-[32px]
                    border border-white/50
                    bg-white/75
                    p-8
                    shadow-[0_30px_100px_rgba(0,0,0,0.35)]
                    backdrop-blur-3xl
                "
            >
                {/* Liquid decorative shapes */}
                <div
                    className="
                        pointer-events-none
                        absolute -right-20 -top-20
                        h-52 w-52
                        rounded-full
                        bg-white/60
                        blur-3xl
                    "
                />

                <div
                    className="
                        pointer-events-none
                        absolute -bottom-24 -left-20
                        h-56 w-56
                        rounded-full
                        bg-black/5
                        blur-3xl
                    "
                />


                <button
                    type="button"
                    disabled={loading}
                    onClick={() => setOpen(false)}
                    className="
                        absolute right-5 top-5
                        flex h-9 w-9
                        items-center justify-center
                        rounded-full
                        bg-black/5
                        text-xl
                        transition
                        hover:bg-black hover:text-white
                    "
                    aria-label="Fermer"
                >
                    ×
                </button>


                <div className="relative">
                    <p
                        className="
                            text-xs font-black
                            uppercase
                            tracking-[0.2em]
                            text-neutral-500
                        "
                    >
                        CafThé
                    </p>

                    <h2
                        className="
                            mt-3
                            text-4xl font-black
                            tracking-[-0.05em]
                        "
                    >
                        Bon retour.
                    </h2>

                    <p className="mt-2 text-neutral-500">
                        Connectez-vous à votre espace.
                    </p>


                    <form
                        onSubmit={authenticate}
                        className="mt-8 space-y-5"
                    >
                        <div>
                            <label
                                htmlFor="login-email"
                                className="
                                    mb-2 block
                                    text-sm font-bold
                                "
                            >
                                Email
                            </label>

                            <input
                                id="login-email"
                                type="email"
                                required
                                autoComplete="email"
                                value={email}
                                onChange={event => (
                                    setEmail(
                                        event.target.value
                                    )
                                )}
                                className="
                                    w-full
                                    rounded-2xl
                                    border border-black/10
                                    bg-white/70
                                    px-4 py-3
                                    outline-none
                                    transition
                                    focus:border-black/40
                                    focus:bg-white
                                "
                            />
                        </div>


                        <div>
                            <label
                                htmlFor="login-password"
                                className="
                                    mb-2 block
                                    text-sm font-bold
                                "
                            >
                                Mot de passe
                            </label>

                            <input
                                id="login-password"
                                type="password"
                                required
                                autoComplete="current-password"
                                value={password}
                                onChange={event => (
                                    setPassword(
                                        event.target.value
                                    )
                                )}
                                className="
                                    w-full
                                    rounded-2xl
                                    border border-black/10
                                    bg-white/70
                                    px-4 py-3
                                    outline-none
                                    transition
                                    focus:border-black/40
                                    focus:bg-white
                                "
                            />
                        </div>


                        {error && (
                            <div
                                className="
                                    rounded-2xl
                                    bg-red-50
                                    px-4 py-3
                                    text-sm font-semibold
                                    text-red-700
                                "
                            >
                                {error}
                            </div>
                        )}


                        <button
                            type="submit"
                            disabled={loading}
                            className="
                                w-full
                                rounded-full
                                bg-black
                                px-5 py-4
                                font-black text-white
                                transition
                                hover:-translate-y-0.5
                                disabled:opacity-40
                            "
                        >
                            {loading
                                ? 'Connexion...'
                                : 'Se connecter'
                            }
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
}
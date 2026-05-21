@props([
    'action' => 'submit',
    'target' => 'recaptchaToken',
])

@php
    $siteKey = config('services.recaptcha.site_key');
    $enabled = config('services.recaptcha.enabled') && filled($siteKey);
@endphp

@if ($enabled)
    @once
        <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}" async defer></script>
    @endonce

    <div wire:ignore
         x-data="recaptchaWidget($wire, @js($siteKey), @js($action), @js($target))"
         x-init="start()"
         style="display:none;"></div>

    {{-- Disclaimer requerido por ToS de Google reCAPTCHA --}}
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
        Protegido por reCAPTCHA — aplican la
        <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="underline hover:text-gray-700 dark:hover:text-gray-200">Política de privacidad</a>
        y los
        <a href="https://policies.google.com/terms" target="_blank" rel="noopener" class="underline hover:text-gray-700 dark:hover:text-gray-200">Términos del servicio</a>
        de Google.
    </p>

    @once
        <script>
            window.recaptchaWidget = function (wire, siteKey, action, target) {
                return {
                    intervalId: null,
                    fetchToken() {
                        if (!window.grecaptcha || !window.grecaptcha.execute) {
                            setTimeout(() => this.fetchToken(), 300);
                            return;
                        }
                        try {
                            window.grecaptcha.execute(siteKey, { action }).then(token => {
                                // Tercer arg = defer: no disparar render, solo
                                // actualizar la prop. Se va a mandar con el
                                // proximo request (el wire:click="save").
                                wire.set(target, token, true);
                            }).catch(err => console.warn('reCAPTCHA execute error', err));
                        } catch (e) {
                            console.warn('reCAPTCHA execute exception', e);
                        }
                    },
                    start() {
                        const init = () => {
                            this.fetchToken();
                            // Tokens v3 caducan en ~120s — refrescar antes.
                            this.intervalId = setInterval(() => this.fetchToken(), 90000);
                        };
                        if (window.grecaptcha && window.grecaptcha.ready) {
                            window.grecaptcha.ready(init);
                        } else {
                            const wait = setInterval(() => {
                                if (window.grecaptcha && window.grecaptcha.ready) {
                                    clearInterval(wait);
                                    window.grecaptcha.ready(init);
                                }
                            }, 200);
                        }
                    },
                };
            };
        </script>
    @endonce
@endif

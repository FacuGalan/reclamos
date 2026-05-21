@props([
    'action' => 'submit',
    'target' => 'recaptchaToken',
])

@php
    $siteKey = config('services.recaptcha.site_key');
    $enabled = config('services.recaptcha.enabled') && filled($siteKey);
@endphp

@if ($enabled)
    {{-- El <script src> de Google reCAPTCHA se inyecta via @@assets desde el
         componente Livewire padre (alta-reclamo / alta-reporte). @@assets no
         funciona en sub-componentes blade. --}}

    {{-- x-data inline (sin funcion global) para que Alpine pueda evaluarlo
         apenas el nodo aparece, sin depender de un <script> que tal vez no
         haya corrido todavia (Livewire morph no ejecuta <script> inline). --}}
    <div wire:ignore
         x-data="{
             intervalId: null,
             siteKey: @js($siteKey),
             action: @js($action),
             target: @js($target),
             fetchToken() {
                 if (!window.grecaptcha || !window.grecaptcha.execute) {
                     setTimeout(() => this.fetchToken(), 300);
                     return;
                 }
                 try {
                     window.grecaptcha.execute(this.siteKey, { action: this.action }).then(token => {
                         $wire.set(this.target, token, true);
                     }).catch(err => console.warn('reCAPTCHA execute error', err));
                 } catch (e) {
                     console.warn('reCAPTCHA execute exception', e);
                 }
             },
             start() {
                 const init = () => {
                     this.fetchToken();
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
             }
         }"
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
@endif

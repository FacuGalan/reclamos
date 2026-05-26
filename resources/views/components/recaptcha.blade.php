@props([
    'action' => 'submit',
    'target' => 'recaptchaToken',
    // widget   = solo el div oculto que fetchea el token (montar UNA vez al
    //            inicio del wizard, fuera de @if($step==X), para evitar que
    //            morphs intermedios pierdan el x-init).
    // disclaimer = solo el texto legal de Google (mostrar cerca del submit).
    // both     = ambos (compat con usos viejos).
    'mode' => 'both',
])

@php
    $siteKey = config('services.recaptcha.site_key');
    $enabled = config('services.recaptcha.enabled') && filled($siteKey);
    $renderWidget = $enabled && in_array($mode, ['widget', 'both'], true);
    $renderDisclaimer = $enabled && in_array($mode, ['disclaimer', 'both'], true);
@endphp

@if ($renderWidget)
    {{-- El <script src> de Google reCAPTCHA se inyecta via @@assets desde el
         componente Livewire padre (alta-reclamo / alta-reporte). @@assets no
         funciona en sub-componentes blade. --}}

    {{-- x-data inline (sin funcion global) para que Alpine pueda evaluarlo
         apenas el nodo aparece, sin depender de un <script> que tal vez no
         haya corrido todavia (Livewire morph no ejecuta <script> inline). --}}
    <div wire:ignore
         x-data="{
             intervalId: null,
             retryTimeoutId: null,
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
                     }).catch(err => {
                         console.warn('reCAPTCHA execute error, reintentando en 5s', err);
                         clearTimeout(this.retryTimeoutId);
                         this.retryTimeoutId = setTimeout(() => this.fetchToken(), 5000);
                     });
                 } catch (e) {
                     console.warn('reCAPTCHA execute exception, reintentando en 5s', e);
                     clearTimeout(this.retryTimeoutId);
                     this.retryTimeoutId = setTimeout(() => this.fetchToken(), 5000);
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
@endif

@if ($renderDisclaimer)
    {{-- Disclaimer requerido por ToS de Google reCAPTCHA --}}
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
        Protegido por reCAPTCHA — aplican la
        <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="underline hover:text-gray-700 dark:hover:text-gray-200">Política de privacidad</a>
        y los
        <a href="https://policies.google.com/terms" target="_blank" rel="noopener" class="underline hover:text-gray-700 dark:hover:text-gray-200">Términos del servicio</a>
        de Google.
    </p>
@endif

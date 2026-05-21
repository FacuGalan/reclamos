<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaVerifier
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public static function enabled(): bool
    {
        return (bool) config('services.recaptcha.enabled')
            && filled(config('services.recaptcha.secret_key'));
    }

    // Devuelve true si el token es valido para la action esperada y supera
    // el score minimo. Si reCAPTCHA esta deshabilitado, devuelve true (bypass
    // para dev local sin keys).
    public static function verify(?string $token, string $expectedAction, ?string $remoteIp = null): bool
    {
        if (!self::enabled()) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->connectTimeout(3)
                ->post(self::VERIFY_URL, array_filter([
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]));

            if (!$response->successful()) {
                Log::warning('reCAPTCHA siteverify HTTP error', [
                    'status' => $response->status(),
                ]);
                // Fail-open ante errores HTTP de Google.
                return true;
            }

            $body = $response->json();

            if (!($body['success'] ?? false)) {
                Log::info('reCAPTCHA rechazado', [
                    'error-codes' => $body['error-codes'] ?? [],
                    'action' => $body['action'] ?? null,
                    'ip' => $remoteIp,
                ]);
                return false;
            }

            // v3 obliga a verificar la action server-side. Si no coincide,
            // alguien esta intentando reutilizar un token de otra pagina.
            $actionDevuelta = $body['action'] ?? '';
            if ($actionDevuelta !== $expectedAction) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $expectedAction,
                    'got' => $actionDevuelta,
                    'ip' => $remoteIp,
                ]);
                return false;
            }

            $score = (float) ($body['score'] ?? 0);
            $minScore = (float) config('services.recaptcha.min_score', 0.5);

            if ($score < $minScore) {
                Log::info('reCAPTCHA score bajo', [
                    'score' => $score,
                    'min_score' => $minScore,
                    'action' => $expectedAction,
                    'ip' => $remoteIp,
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA siteverify exception', [
                'message' => $e->getMessage(),
            ]);
            // Fail-open ante excepciones de red para no bloquear vecinos
            // legitimos por una falla transitoria de Google.
            return true;
        }
    }
}

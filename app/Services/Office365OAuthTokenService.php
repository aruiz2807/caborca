<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Office365OAuthTokenService
{
    public function getAccessToken(array $config): string
    {
        $tenantId = trim((string) ($config['oauth_tenant_id'] ?? ''));
        $clientId = trim((string) ($config['oauth_client_id'] ?? ''));
        $clientSecret = (string) ($config['oauth_client_secret'] ?? '');
        $mailbox = trim((string) ($config['oauth_mailbox'] ?? $config['username'] ?? ''));

        if ($tenantId === '' || $clientId === '' || $clientSecret === '' || $mailbox === '') {
            throw new RuntimeException('Faltan credenciales OAuth2 de Office 365 para autenticar el envio SMTP.');
        }

        $cacheKey = 'office365.smtp.token.' . hash('sha256', implode('|', [
            $tenantId,
            $clientId,
            $mailbox,
            $clientSecret,
        ]));

        $cachedToken = Cache::get($cacheKey);
        if (is_string($cachedToken) && $cachedToken !== '') {
            return $cachedToken;
        }

        $response = Http::asForm()
            ->timeout(15)
            ->post($this->tokenEndpoint($tenantId), [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://outlook.office365.com/.default',
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            $message = $response->json('error_description')
                ?: $response->json('error')
                ?: $response->body();

            throw new RuntimeException('No fue posible obtener el token OAuth2 de Office 365: ' . trim((string) $message));
        }

        $accessToken = (string) $response->json('access_token', '');
        if ($accessToken === '') {
            throw new RuntimeException('Office 365 no devolvio un access token valido para SMTP OAuth2.');
        }

        $expiresIn = (int) $response->json('expires_in', 3600);
        $ttl = max(60, $expiresIn - 120);

        Cache::put($cacheKey, $accessToken, now()->addSeconds($ttl));

        return $accessToken;
    }

    protected function tokenEndpoint(string $tenantId): string
    {
        return sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', rawurlencode($tenantId));
    }
}

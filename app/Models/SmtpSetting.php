<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

class SmtpSetting extends Model
{
    use HasFactory;

    public const PROVIDER_SMTP = 'smtp';
    public const PROVIDER_OFFICE365_OAUTH2 = 'office365_oauth2';

    protected $fillable = [
        'provider',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'oauth_tenant_id',
        'oauth_client_id',
        'oauth_client_secret',
        'oauth_mailbox',
        'from_name',
        'from_email',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'port' => 'integer',
    ];

    protected $attributes = [
        'provider' => self::PROVIDER_SMTP,
    ];

    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getDecryptedPasswordAttribute(): ?string
    {
        $value = $this->attributes['password'] ?? null;
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function setOauthClientSecretAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->attributes['oauth_client_secret'] = Crypt::encryptString($value);
    }

    public function getDecryptedOauthClientSecretAttribute(): ?string
    {
        $value = $this->attributes['oauth_client_secret'] ?? null;
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function activeSetting(): ?self
    {
        if (! Schema::hasTable('smtp_settings')) {
            return null;
        }

        return Cache::remember('smtp.setting.active', 60, function () {
            return static::where('active', true)->first() ?? static::first();
        });
    }

    public static function isConfigured(): bool
    {
        $setting = static::activeSetting();

        return $setting?->isConfiguredForProvider() ?? false;
    }

    public function providerKey(): string
    {
        return (string) (($this->attributes['provider'] ?? null) ?: self::PROVIDER_SMTP);
    }

    public function usesOffice365OAuth2(): bool
    {
        return $this->providerKey() === self::PROVIDER_OFFICE365_OAUTH2;
    }

    public function smtpHost(): string
    {
        if ($this->usesOffice365OAuth2()) {
            return $this->host ?: 'smtp.office365.com';
        }

        return (string) ($this->host ?? '');
    }

    public function smtpPort(): int
    {
        if ($this->usesOffice365OAuth2()) {
            return (int) ($this->port ?: 587);
        }

        return (int) ($this->port ?: 587);
    }

    public function smtpEncryption(): ?string
    {
        if ($this->usesOffice365OAuth2()) {
            return $this->encryption ?: 'tls';
        }

        return $this->encryption;
    }

    public function smtpUsername(): ?string
    {
        if ($this->usesOffice365OAuth2()) {
            return $this->oauth_mailbox ?: $this->from_email;
        }

        return $this->username;
    }

    public function isConfiguredForProvider(): bool
    {
        if (! $this->from_email) {
            return false;
        }

        if ($this->usesOffice365OAuth2()) {
            return (bool) (
                $this->smtpHost()
                && $this->smtpPort() > 0
                && $this->smtpUsername()
                && $this->oauth_tenant_id
                && $this->oauth_client_id
                && $this->decrypted_oauth_client_secret
            );
        }

        return (bool) ($this->smtpHost());
    }

    public function mailerTransportConfiguration(): array
    {
        if ($this->usesOffice365OAuth2()) {
            return [
                'transport' => 'office365-oauth2',
                'host' => $this->smtpHost(),
                'port' => $this->smtpPort(),
                'encryption' => $this->smtpEncryption(),
                'username' => $this->smtpUsername(),
                'oauth_tenant_id' => $this->oauth_tenant_id,
                'oauth_client_id' => $this->oauth_client_id,
                'oauth_client_secret' => $this->decrypted_oauth_client_secret,
                'oauth_mailbox' => $this->smtpUsername(),
                'timeout' => null,
            ];
        }

        return [
            'transport' => 'smtp',
            'host' => $this->smtpHost(),
            'port' => $this->smtpPort(),
            'encryption' => $this->smtpEncryption(),
            'username' => $this->username,
            'password' => $this->decrypted_password,
            'timeout' => null,
        ];
    }
}

<?php

namespace Tests\Unit;

use App\Models\SmtpSetting;
use Tests\TestCase;

class SmtpSettingTest extends TestCase
{
    public function test_it_builds_office365_mailer_configuration(): void
    {
        $setting = new SmtpSetting([
            'provider' => SmtpSetting::PROVIDER_OFFICE365_OAUTH2,
            'host' => 'smtp.office365.com',
            'port' => 587,
            'encryption' => 'tls',
            'oauth_tenant_id' => 'tenant-id',
            'oauth_client_id' => 'client-id',
            'oauth_mailbox' => 'notificaciones@empresa.com',
            'from_email' => 'notificaciones@empresa.com',
            'from_name' => 'Caborca',
        ]);

        $setting->oauth_client_secret = 'top-secret';

        $config = $setting->mailerTransportConfiguration();

        $this->assertSame('office365-oauth2', $config['transport']);
        $this->assertSame('smtp.office365.com', $config['host']);
        $this->assertSame(587, $config['port']);
        $this->assertSame('notificaciones@empresa.com', $config['oauth_mailbox']);
        $this->assertSame('top-secret', $config['oauth_client_secret']);
        $this->assertTrue($setting->isConfiguredForProvider());
    }

    public function test_it_builds_classic_smtp_configuration(): void
    {
        $setting = new SmtpSetting([
            'provider' => SmtpSetting::PROVIDER_SMTP,
            'host' => 'smtp.sendgrid.net',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'apikey',
            'from_email' => 'notificaciones@empresa.com',
            'from_name' => 'Caborca',
        ]);

        $setting->password = 'smtp-password';

        $config = $setting->mailerTransportConfiguration();

        $this->assertSame('smtp', $config['transport']);
        $this->assertSame('apikey', $config['username']);
        $this->assertSame('smtp-password', $config['password']);
        $this->assertTrue($setting->isConfiguredForProvider());
    }
}

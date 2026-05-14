<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SmtpSettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Smtp/Index', [
            'setting' => SmtpSetting::first(),
        ]);
    }

    public function update(Request $request)
    {
        $setting = SmtpSetting::firstOrCreate([]);

        $validator = Validator::make($request->all(), [
            'provider' => ['required', Rule::in([
                SmtpSetting::PROVIDER_SMTP,
                SmtpSetting::PROVIDER_OFFICE365_OAUTH2,
            ])],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'encryption' => ['nullable', 'in:tls,ssl,none'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'oauth_tenant_id' => ['nullable', 'string', 'max:255'],
            'oauth_client_id' => ['nullable', 'string', 'max:255'],
            'oauth_client_secret' => ['nullable', 'string', 'max:255'],
            'oauth_mailbox' => ['nullable', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
            'from_email' => ['required', 'email', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($request, $setting) {
            if ($request->input('provider') !== SmtpSetting::PROVIDER_OFFICE365_OAUTH2) {
                return;
            }

            if (! $request->filled('oauth_tenant_id')) {
                $validator->errors()->add('oauth_tenant_id', 'El Tenant ID de Office 365 es obligatorio.');
            }

            if (! $request->filled('oauth_client_id')) {
                $validator->errors()->add('oauth_client_id', 'El Client ID de Office 365 es obligatorio.');
            }

            if (! $request->filled('oauth_mailbox') && ! $request->filled('from_email')) {
                $validator->errors()->add('oauth_mailbox', 'El buzon remitente de Office 365 es obligatorio.');
            }

            $hasExistingSecret = ! empty($setting->decrypted_oauth_client_secret);
            if (! $request->filled('oauth_client_secret') && ! $hasExistingSecret) {
                $validator->errors()->add('oauth_client_secret', 'El Client Secret de Office 365 es obligatorio.');
            }
        });

        $data = $validator->validate();

        $setting->host = $data['host'];
        $setting->port = $data['port'];
        $setting->encryption = $data['encryption'] === 'none' ? null : $data['encryption'];
        $setting->provider = $data['provider'];
        $setting->username = $data['username'];
        $setting->from_name = $data['from_name'];
        $setting->from_email = $data['from_email'];
        $setting->active = (bool) ($data['active'] ?? false);
        $setting->oauth_tenant_id = $data['oauth_tenant_id'] ?? null;
        $setting->oauth_client_id = $data['oauth_client_id'] ?? null;
        $setting->oauth_mailbox = $data['oauth_mailbox'] ?? $data['from_email'];

        if (! empty($data['password'])) {
            $setting->password = $data['password'];
        }

        if (! empty($data['oauth_client_secret'])) {
            $setting->oauth_client_secret = $data['oauth_client_secret'];
        }

        $setting->save();

        cache()->forget('smtp.setting.active');

        return to_route('smtp.index')->with('message', 'smtp-stored');
    }

    public function test(Request $request)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $setting = SmtpSetting::activeSetting();

        if (! $setting || ! $setting->active || ! $setting->isConfiguredForProvider()) {
            return to_route('smtp.index')->with('error', 'smtp-not-configured');
        }

        $defaultMessage = 'Este es un correo de prueba desde ' . config('app.name', 'Caborca') . '.';

        try {
            Mail::mailer('smtp')->raw($defaultMessage, function ($message) use ($data) {
                $message->to($data['test_email'])->subject('Prueba de correo saliente');
            });
        } catch (\Throwable $th) {
            return to_route('smtp.index')->with('error', 'smtp-test-failed');
        }

        return to_route('smtp.index')->with('message', 'smtp-test-sent');
    }
}

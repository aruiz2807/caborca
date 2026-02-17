@component('mail::message')

# {{ __('app.hello') }} {{ $user->name }}!

{{ __('emails.reset_password_message') }}

@component('mail::button', ['url' => $url])
{{ __('emails.reset_password_button') }}
@endcomponent

@component('mail::panel')
{{ __('emails.reset_password_expire') }}

{{ __('emails.reset_password_warning') }}
@endcomponent

{{ __('app.regards') }}

@slot('subcopy')
{{ __('emails.reset_password_trouble') }}
[{{ $url }}]({{ $url }})
@endslot

@endcomponent

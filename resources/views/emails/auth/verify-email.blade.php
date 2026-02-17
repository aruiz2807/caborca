@component('mail::message')

# {{ __('app.hello') }} {{ $user->name }}!

{{ __('emails.verify_email_message') }}

@component('mail::button', ['url' => $url])
{{ __('emails.verify_email_button') }}
@endcomponent

@component('mail::panel')
{{ __('emails.verify_email_warning') }}
@endcomponent

{{ __('app.regards') }}

@slot('subcopy')
{{ __('emails.verify_email_trouble') }}
[{{ $url }}]({{ $url }})
@endslot

@endcomponent

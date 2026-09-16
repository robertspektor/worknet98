<x-mail::message>
# {{ __('mail.login_link.heading') }}

{{ __('mail.login_link.intro') }}

<x-mail::button :url="$url">
{{ __('mail.login_link.button') }}
</x-mail::button>

{{ __('mail.login_link.expiry', ['minutes' => $lifetimeMinutes]) }}

{{ __('mail.login_link.ignore') }}

{{ __('mail.login_link.signature') }}
</x-mail::message>

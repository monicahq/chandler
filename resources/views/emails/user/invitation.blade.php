@component('mail::message')
# {{ $userName }} invites you to join Monica.

@component('mail::button', ['url' => $url])
Join
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

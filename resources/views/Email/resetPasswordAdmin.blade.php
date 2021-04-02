@component('mail::message')
# Your new password

Check admin login again - {{ config('app.url') }}
@component('mail::panel')
    {{$password}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
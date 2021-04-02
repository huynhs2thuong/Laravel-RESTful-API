@component('mail::message')
# Your verification code

The code will last for 5 minutes.

@component('mail::panel')
    {{$token}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

@component('mail::message')
# Email para recuperação de senha

Utilize o Token abaixo :

{{$token}}
@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

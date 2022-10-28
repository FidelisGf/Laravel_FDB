@component('mail::message')
# Olá {{ $user->NAME }}!

# Obrigado pela preferência !
Estamos trabalhando duro para que você desfrute do melhor software possivel...
<p>Bem Vindo ao Empresarial !</p>

@component('mail::button', ['url' => "http://localhost:3000/#/"])
Acesse o link para confirmar seu cadastro
@endcomponent

Obrigado,<br>
Empresarial...
@endcomponent

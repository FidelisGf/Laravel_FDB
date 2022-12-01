
@component('mail::message')
# Olá Sr(a) {{ $user->NOME }}...

<p>Seja Bem Vindo a nossa empresa</p>
# Obrigado pela preferência !
O numero do seu pedido é # {{$pedido->ID}}, você pode conferir as informações do mesmo abaixo :

@component('mail::table')
| PRODUTO 	| VALOR 	| QUANTIDADE 	|
| :---------	| :-------	| :------------	|
@foreach ($PRODUTOS as $prod)
| {{$prod->NOME}}   | R$ {{$prod->VALOR}}   | {{$prod->QUANTIDADE}} {{$prod->MEDIDA}}  |
@endforeach
@endcomponent


<p>Valor Total : R$ {{$pedido->VALOR_TOTAL}}</p>
<p>Metodo de Pagamento : {{$pedido->METODO_PAGAMENTO}}</p>
Obrigado,<br>
Tenha um otimo dia...<br>

@endcomponent

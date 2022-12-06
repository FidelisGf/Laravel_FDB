
@component('mail::message')

#Olá Sr(a) Cliente

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

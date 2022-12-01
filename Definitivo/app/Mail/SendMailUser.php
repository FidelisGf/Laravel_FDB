<?php

namespace App\Mail;

use App\Cliente;
use App\Empresa;
use App\Http\Resources\FakeProduct;
use App\Pedidos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailUser extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $pedidos;
    private $cliente;
    private $PRODUTOS;
    public function __construct($pedidos, $PRODUTOS, $cliente)
    {
        $this->PRODUTOS = $PRODUTOS;
        $this->pedidos = $pedidos;
        $this->cliente = $cliente;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('to@email.com')
                ->markdown('emails.test-markdown')
                ->with([
                    'user'  => $this->cliente,
                    'pedido' => $this->pedidos,
                    'PRODUTOS' => $this->PRODUTOS

                ]);
    }
}

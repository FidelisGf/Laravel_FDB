<?php

namespace App\Mail;

use App\Cliente;
use App\Empresa;
use App\Pedidos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailUser extends Mailable
{
    use  SerializesModels;
    public $usuario;
    public $pedidos;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Cliente $usuario, Pedidos $pedidos)
    {
        $this->usuario = $usuario;
        $this->pedidos = $pedidos;
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
                    'user'  => $this->usuario,
                    'pedido' => $this->pedidos,
                    'empresa' => auth()->user()->empresa
                ]);
    }
}

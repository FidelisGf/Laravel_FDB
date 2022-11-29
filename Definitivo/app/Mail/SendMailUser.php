<?php

namespace App\Mail;

use App\Cliente;
use App\Pedidos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailUser extends Mailable
{
    use SerializesModels;
    private $usuario;
    private $url = "https://laravel.com/docs/5.6/queues";
    private $pedidos;
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
                    'rota' => $this->url,
                    'empresa' => auth()->user()->empresa
                ]);
    }
}

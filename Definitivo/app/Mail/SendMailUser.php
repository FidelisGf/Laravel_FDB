<?php

namespace App\Mail;

use App\Cliente;
use App\Empresa;
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

    public function __construct(Pedidos $pedidos)
    {
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
                    'user'  => $this->pedidos->cliente,
                    'pedido' => $this->pedidos,

                ]);
    }
}

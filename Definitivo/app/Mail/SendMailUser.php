<?php

namespace App\Mail;

use App\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailUser extends Mailable
{
    use Queueable, SerializesModels;
    private $usuario;
    private $cod;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Cliente $usuario, $cod)
    {
        $this->usuario = $usuario;
        $this->cod = strval($cod);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('to@email.com')
                ->attach(storage_path("pedido.pdf"), ['mime' => 'application/pdf'])
                ->markdown('emails.test-markdown')
                ->with([
                    'user'  => $this->usuario
                ]);
    }
}

<?php

namespace App\Listeners;

use App\Cliente;
use App\Events\VendaGerada;
use App\Mail\SendMailUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMailVendaGerada
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  VendaGerada  $event
     * @return void
     */
    public function handle(VendaGerada $event)
    {
        $cliente = Cliente::FindOrFail($event->idClient);
        Mail::to($cliente->EMAIL)->send(new SendMailUser($cliente, $event->venda));
    }
}

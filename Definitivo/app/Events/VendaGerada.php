<?php

namespace App\Events;

use App\Pedidos;
use App\Venda;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendaGerada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $venda ;
    public $idClient;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Pedidos $venda, $id)
    {
        $this->venda = $venda;
        $this->idClient = $id;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

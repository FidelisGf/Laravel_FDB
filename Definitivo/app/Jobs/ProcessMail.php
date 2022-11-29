<?php

namespace App\Jobs;

use App\Cliente;
use App\Mail\SendMailUser;
use Facade\FlareClient\Http\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $venda;
    public $cliente;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cliente $cliente, $venda)
    {
        $this->cliente = $cliente;
        $this->venda = $venda;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() // preciso utilizar o queue:work para que isso funcione
    {
      return Mail::to($this->cliente->EMAIL)->send(new SendMailUser($this->cliente, $this->venda));
    }
}

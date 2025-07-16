<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reclamo;

class MovimientoReclamoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reclamo;
    public $observaciones;

    public function __construct(Reclamo $reclamo, $observaciones = null)
    {
        $this->reclamo = $reclamo;
        $this->observaciones = $observaciones;
    }

    public function build()
    {
        return $this->subject('Actualización de su reclamo #' . $this->reclamo->id)
                    ->view('emails.movimiento-reclamo')
                    ->with([
                        'reclamo' => $this->reclamo,
                        'observaciones' => $this->observaciones,
                    ]);
    }
}
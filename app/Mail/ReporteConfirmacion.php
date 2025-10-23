<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reporte;

class ReporteConfirmacion extends Mailable
{
    use Queueable, SerializesModels;

    public $reporte;
    public $persona;
    public $categoria;
    public $domicilio;

    /**
     * Create a new message instance.
     */
    public function __construct(Reporte $reporte)
    {
        $this->reporte = $reporte;
        $this->persona = $reporte->persona;
        $this->categoria = $reporte->categoria;
        $this->domicilio = $reporte->domicilio;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Reporte #' . $this->reporte->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reporte-confirmacion',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
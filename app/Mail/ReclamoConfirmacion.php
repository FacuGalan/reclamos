<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reclamo;

class ReclamoConfirmacion extends Mailable
{
    use Queueable, SerializesModels;

    public $reclamo;
    public $persona;
    public $categoria;

    /**
     * Create a new message instance.
     */
    public function __construct(Reclamo $reclamo)
    {
        $this->reclamo = $reclamo;
        $this->persona = $reclamo->persona;
        $this->categoria = $reclamo->categoria;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Reclamo #' . $this->reclamo->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reclamo-confirmacion',
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
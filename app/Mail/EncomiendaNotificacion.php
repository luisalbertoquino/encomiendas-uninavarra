<?php

namespace App\Mail;

use App\Models\Encomienda;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EncomiendaNotificacion extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Encomienda $encomienda,
        public string $estacion,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Llegó tu encomienda · '.$this->encomienda->codigo,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.encomienda-notificacion',
        );
    }
}

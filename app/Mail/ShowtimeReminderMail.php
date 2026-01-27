<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class ShowtimeReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $hoursUntilShow;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, int $hoursUntilShow = 2)
    {
        $this->booking = $booking;
        $this->hoursUntilShow = $hoursUntilShow;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $movieTitle = $this->booking->showtime->movie->title;
        return new Envelope(
            subject: "Reminder: {$movieTitle} starts in {$this->hoursUntilShow} hour(s)!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.showtime_reminder',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $employee,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        $platform = config('tripolizoo.platform_name');

        return new Envelope(
            subject: "مرحباً بك في {$platform} — بيانات الدخول",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.employee-welcome',
        );
    }
}

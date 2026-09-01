<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceRequestResolved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MaintenanceRequest $maintenanceRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "CampusCare request resolved: {$this->maintenanceRequest->reference}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.maintenance-request-resolved');
    }
}

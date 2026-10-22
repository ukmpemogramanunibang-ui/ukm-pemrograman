<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact; // ← Properti ini WAJIB public agar bisa diakses di view

    public function __construct(Contact $contact)
    {
        $this->contact = $contact; // isi properti
    }

    public function build()
    {
        return $this->from($this->contact->email, $this->contact->name)
                    ->subject('Pesan Baru: ' . $this->contact->subject)
                    ->view('emails.contact')      // ← ganti markdown() jadi view() agar lebih sederhana
                    ->with('contact', $this->contact);
    }
}
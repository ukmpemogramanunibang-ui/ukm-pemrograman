<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function index()
    {
        return view('kontak');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:100',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        // Simpan ke database
        $contact = Contact::create($validated);

        // Kirim email ke admin (ganti dengan email Anda)
        $adminEmail = 'ukmpemogramanunibang@gmail.com'; // GANTI dengan email Anda
        Mail::to($adminEmail)->send(new ContactFormMail($contact));

        return redirect()->route('kontak')->with('success', 'Pesan Anda telah terkirim! Kami akan menghubungi Anda segera.');
    }
}
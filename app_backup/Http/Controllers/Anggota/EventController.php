<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('owner.events.index');
    }

    public function create()
    {
        return view('owner.events.create');
    }

    public function store(Request $request)
    {
        // Logic penyimpanan event
        return redirect()->route('owner.events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function show($id)
    {
        return view('owner.events.show', compact('id'));
    }

    public function edit($id)
    {
        return view('owner.events.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Logic update event
        return redirect()->route('owner.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Logic hapus event
        return redirect()->route('owner.events.index')->with('success', 'Event berhasil dihapus.');
    }
}
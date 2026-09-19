<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('secretary.archives.index');
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('secretary.archives.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'file'  => 'required|file|max:20480', // 20MB
        'visibility' => 'required|in:private,internal,public',
        'shared_users' => 'nullable|array',
        'shared_users.*' => 'exists:users,id'
    ]);

    $user = auth()->user();
    if (!$user->google_drive_token) {
        return redirect()->route('google.redirect')->with('error', 'Harap hubungkan akun Google Drive terlebih dahulu.');
    }

    // 1. Upload ke Google Drive
    $client = $this->getGoogleClient($user);
    $drive = new Drive($client);
    $file = $request->file('file');
    $fileName = $file->getClientOriginalName();

    $fileMetadata = new Drive\DriveFile([
        'name' => $fileName,
        'parents' => [config('google.drive_folder_id')]
    ]);
    $content = file_get_contents($file->getRealPath());
    $uploadedFile = $drive->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $file->getMimeType(),
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    // 2. Simpan metadata ke database
    $archive = Archive::create([
        'title' => $request->title,
        'description' => $request->description,
        'file_name' => $fileName,
        'file_id' => $uploadedFile->id,
        'mime_type' => $file->getMimeType(),
        'uploaded_by' => $user->id,
        'visibility' => $request->visibility,
    ]);

    // 3. Atur permission di Google Drive (opsional, bisa via Laravel)
    if ($request->filled('shared_users')) {
        foreach ($request->shared_users as $userId) {
            ArchivePermission::create([
                'archive_id' => $archive->id,
                'user_id' => $userId,
                'permission' => 'view',
                'shared_by' => $user->id
            ]);
        }
    }

 return redirect()->route('owner.officials.secretary.archives.index')
        ->with('info', 'Fitur upload arsip sedang dalam pengembangan.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

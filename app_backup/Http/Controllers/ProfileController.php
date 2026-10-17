<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio'   => 'nullable|string|max:1000',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'semester' => 'nullable|string|max:10',
            'portfolio_url' => 'nullable|url|max:255',
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'required|in:instagram,facebook,telegram,x',
            'social_media.*.url' => 'required|url|max:255',
            'skills' => 'nullable|string|max:500',
            'pin' => 'nullable|string|max:10|min:4',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
        ];

        $validated = $request->validate($rules);

        // Update field dasar
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;
        $user->birth_date = $validated['birth_date'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->gender = $validated['gender'] ?? null;
        $user->country = $validated['country'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->semester = $validated['semester'] ?? null;
        $user->portfolio_url = $validated['portfolio_url'] ?? null;

        // Sosial media (JSON)
        if ($request->has('social_media')) {
            $user->social_media = $validated['social_media'];
        }

        // Skills (array dari string yang dipisah koma)
        if (!empty($validated['skills'])) {
            $skillsArray = array_map('trim', explode(',', $validated['skills']));
            $user->skills = $skillsArray;
        } else {
            $user->skills = [];
        }

        // PIN
        if (!empty($validated['pin'])) {
            $user->pin = $validated['pin'];
        }

        // Password
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Upload avatar
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            // Simpan file baru
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('avatars', $fileName, 'public');
            $user->avatar_url = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function showPublic(User $user)
    {
        return view('profile.public', compact('user'));
    }
// ProfileController.php
public function toggleLove(User $user) {
    $currentUser = auth()->user();
    if ($currentUser->id == $user->id) {
        return response()->json(['success' => false, 'message' => 'Cannot love yourself']);
    }
    $loved = $currentUser->lovedUsers()->toggle($user->id);
    $count = $user->lovers()->count();
    return response()->json([
        'success' => true,
        'count' => $count,
        'isLoved' => in_array($user->id, $loved['attached'])
    ]);
}
public function explore()
{
    $users = User::with('level')
                ->where('is_active', true)
                ->where('level_id', '!=', 6) // kecuali pengunjung
                ->orderBy('name')
                ->paginate(12);
    return view('profile.explore', compact('users'));
}
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /**
     * Update profil user (single source of truth).
     * Support: data diri, avatar, skills, social media, portfolio links,
     *          preferences (JSON), password, PIN.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'nim'           => 'nullable|string|max:50',
            'phone'         => 'nullable|string|max:20',
            'birth_date'    => 'nullable|date',
            'gender'        => 'nullable|in:male,female',
            'country'       => 'nullable|string|max:100',
            'semester'      => 'nullable|string|max:10',
            'address'       => 'nullable|string|max:500',
            'bio'           => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'skills'        => 'nullable|string|max:500',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pin'           => 'nullable|string|min:4|max:10',
            'password'      => 'nullable|string|min:8|confirmed',

            'social_media'             => 'nullable|array',
            'social_media.*.platform'  => 'nullable|string|max:50',
            'social_media.*.url'       => 'nullable|url|max:255',

            'portfolio_links'          => 'nullable|array',
            'portfolio_links.*.name'   => 'nullable|string|max:100',
            'portfolio_links.*.url'    => 'nullable|url|max:500',

            'preferences'              => 'nullable|array',
        ]);

        /* ── Skills ── */
        $validated['skills'] = $request->filled('skills')
            ? array_values(array_filter(array_map('trim', explode(',', $request->skills))))
            : [];

        /* ── Social Media — filter hanya yang punya URL ── */
        $validated['social_media'] = collect($request->input('social_media', []))
            ->filter(fn ($s) => !empty($s['url']))
            ->map(fn ($s) => [
                'platform' => $s['platform'] ?? 'link',
                'url'      => $s['url'],
            ])
            ->values()->all();

        /* ── Portfolio Links — filter hanya yang lengkap ── */
        $validated['portfolio_links'] = collect($request->input('portfolio_links', []))
            ->filter(fn ($p) => !empty(trim($p['name'] ?? '')) && !empty(trim($p['url'] ?? '')))
            ->map(fn ($p) => ['name' => trim($p['name']), 'url' => trim($p['url'])])
            ->values()->all();

        /* ── Preferences — merge dengan existing ── */
        if ($request->has('preferences')) {
            $validated['preferences'] = array_merge(
                $user->preferences ?? [],
                $request->input('preferences', [])
            );
        }

        /* ── PIN ── */
        if (!empty($validated['pin'])) {
            $validated['pin'] = Hash::make($validated['pin']);
        } else {
            unset($validated['pin']);
        }

        /* ── Password ── */
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        /* ── Avatar ── */
        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $validated['avatar_url'] = $request->file('avatar')->store('avatars', 'public');
        }
        unset($validated['avatar']);

        $user->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Profil diperbarui']);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function showPublic(User $user)
    {
        return view('profile.public', compact('user'));
    }

    public function explore()
    {
        $users = User::with('level')
            ->where('is_active', true)
            ->where('level_id', '!=', 6)
            ->orderBy('name')
            ->paginate(12);

        return view('profile.explore', compact('users'));
    }

    public function toggleLove(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->id == $user->id) {
            return response()->json(['success' => false, 'message' => 'Cannot love yourself']);
        }

        $loved = $currentUser->lovedUsers()->toggle($user->id);
        $count = $user->lovers()->count();

        return response()->json([
            'success' => true,
            'count'   => $count,
            'isLoved' => in_array($user->id, $loved['attached']),
        ]);
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

        return redirect('/');
    }
}
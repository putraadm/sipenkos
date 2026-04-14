<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('pemilik-kos/profile', [
            'user' => $request->user()
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
            'no_wa' => ['required', 'string', 'max:25'],
        ]);

        $request->user()->update($validated);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}

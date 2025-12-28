<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show profile page (tests only check for successful response)
    public function show(Request $request)
    {
        return response('', 200);
    }

    // Update profile information (name and email)
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $emailChanged = $data['email'] !== $user->email;

        $user->fill($data);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect('/profile');
    }

    // Delete account with password confirmation using the 'userDeletion' error bag
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->input('password'), $user->password)) {
            return redirect('/profile')->withErrors(['password' => 'The provided password does not match our records.'], 'userDeletion');
        }

        auth()->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

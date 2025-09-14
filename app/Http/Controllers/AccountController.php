<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AccountController extends Controller
{
    public function createAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'score' => 'required|numeric|min:90'
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'message' => 'Account already exists',
                'email' => $existingUser->email
            ]);
        }

        // Generate random password
        $password = Str::random(8);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'score' => $request->score,
            'account_type' => 'student'
        ]);

        // Send welcome email (optional)
        try {
            $this->sendWelcomeEmail($user, $password);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Account created successfully',
            'email' => $user->email,
            'password' => $password,
            'academy_url' => 'https://academy.example.com', // رابط الموقع الرسمي للأكاديمية
            'user_id' => $user->id
        ]);
    }

    private function sendWelcomeEmail($user, $password)
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'score' => $user->score
        ];

        // Simple email sending (you can customize this)
        Mail::send('emails.welcome', $data, function($message) use ($user) {
            $message->to($user->email, $user->name)
                   ->subject('Welcome to Academic Platform - Account Created');
        });
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|email',
            'number_phone' => 'required|string',
            'address' => 'required|string',
            'otp' => 'required|string'
        ]);

        // Cek apakah OTP valid
        $otpRecord = DB::table('otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP tidak valid atau kadaluarsa'], 400);
        }

        // Cek apakah user sudah ada
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email sudah terdaftar'], 400);
        }

        // Simpan user
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'number_phone' => $request->number_phone,
            'address' => $request->address,
            'role' => 'user' // default
        ]);

        // Hapus OTP
        DB::table('otps')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Registrasi berhasil']);
    }


    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->identifier)
                    ->orWhere('number_phone', $request->identifier)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email/No. HP atau password salah'
            ], 401);
        }

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user->only(['id', 'username', 'role', 'email', 'number_phone', 'address'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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

    
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $otp = rand(100000, 999999); // OTP 6 digit

        // Simpan OTP ke database
        DB::table('otps')->updateOrInsert(
            ['email' => $request->email],
            ['otp' => $otp, 'created_at' => now()]
        );

        // Kirim email
        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP berhasil dikirim']);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string'
        ]);

        // Cek apakah OTP valid
        $otpRecord = DB::table('otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP tidak valid atau kadaluarsa'], 400);
        }

        // Hapus OTP
        DB::table('otps')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Kode OTP valid. Silahkan buat password baru.']);
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah. Silahkan login.']);
    }


}

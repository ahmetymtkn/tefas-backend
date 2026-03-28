<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Yeni kullanıcı kaydı oluştur
     * 
     * POST /api/register
     * @param Request $request - name, email, password, password_confirmation
     * @return JSON - başarı durumu, kullanıcı bilgisi
     */
    public function register(Request $request)
    {
        // Gelen verileri doğrula
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Validasyon başarısız ise hata döndür
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Yeni kullanıcı oluştur (şifre otomatik hash'lenecek)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kayıt başarılı!',
            'user' => $user
        ], 201);
    }

    /**
     * Kullanıcı girişi - API token oluştur
     * 
     * POST /api/login
     * @param Request $request - email, password
     * @return JSON - başarı durumu, kullanıcı bilgisi, API token
     */
    public function login(Request $request)
    {
        // Email ve şifre doğrula
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Validasyon başarısız ise hata döndür
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verilen email ile kullanıcı ara
        $user = User::where('email', $request->email)->first();

        // Kullanıcı yoksa veya şifre yanlışsa hata döndür
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz email veya şifre'
            ], 401);
        }

        // Sanctum ile API token oluştur (API istekleri için)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Başarılı giriş döndür
        return response()->json([
            'success' => true,
            'message' => 'Giriş başarılı',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Kullanıcı çıkışı - API token'ı sil
     * 
     * POST /api/logout
     * @param Request $request - Authenticated user
     * @return JSON - başarı durumu
     */
    public function logout(Request $request)
    {
        // Kullanıcının geçerli API token'ını sil (logout)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Çıkış başarılı'
        ], 200);
    }
}

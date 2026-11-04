<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @return array{message: string, token: string, data: User, code: int}
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Autenticación exitosa',
            'token' => $token,
            'data' => $user,
            'code' => 200,
        ], 200);
    }

    /**
     * @return array{message: string, code: int}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
            'code' => 200,
        ], 200);
    }

    /**
     * @return array{data: User, code: int}
     */
    public function me(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
            'code' => 200,
        ], 200);
    }
}

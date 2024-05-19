<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Optionmenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     *  Authenticate user and generate access token
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 example="username"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="123456"
     *             )
     *         )
     *     ),
     *

     *      @OA\Response(
     *          response=401,
     *          description="User not authenticated",
     *           @OA\JsonContent(
     *               @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Unauthorized."
     *              )
     *           )
     *      ),
     *       @OA\Response(
     *           response=400,
     *           description="Credentials are invalid",
     *            @OA\JsonContent(
     *                @OA\Property(
     *                    property="message",
     *                    type="string",
     *                    example="Invalid credentials."
     *               )
     *            )
     *       )
     * )
     */
    public function login(Request $request)
    {
        // Validar las credenciales del usuario
        $credentials = $request->only('username', 'password');
        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required',
        ]);

        // Verificar si las credenciales son válidas
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        $user = User::where("username", $request->username)->first();

        if (!$user) {
            return response()->json([
                "error" => "User not Registered",
            ], 422);
        }

        if (Hash::check($request->password, $user->password)) {
            // Autenticar al usuario

            Auth::loginUsingId($user->id);

            $token = $user->createToken('auth_token', ['expires' => now()->addHours(2)])->plainTextToken;

            // -------------------------------------------------
            return response()->json([
                'access_token' => $token,
                'user' => User::with(['typeUser', 'worker', 'typeUser.access'])->find($user->id),
                'permissions' => Optionmenu::pluck('id'),

            ]);
        } else {
            return response()->json([
                "error" => "Password Not Correct",
            ], 422);

        }

    }

    /**
     * @OA\Get(
     *     path="/transporte/public/api/authenticate",
     *     summary="Get Profile user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     description="Get user",
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="user",
     *             type="object",
     *             description="User",
     *             ref="#/components/schemas/User"
     *              ),
     *          @OA\Property(
     *          property="menu",
     *          type="array",
     *              @OA\Items(
     *              type="object",
     *               description="Menú"
     *              )
     *),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *        @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */

    public function authenticate(Request $request)
    {
        try {

            $user = auth('sanctum')->user();
            $token = $request->bearerToken();

            return response()->json([
                'access_token' => $token,
                'user' => User::with(['typeUser', 'worker', 'typeUser.access'])->find($user->id),
                'permissions' => Optionmenu::pluck('id'),

            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error interno del servidor: " . $e,
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/transporte/public/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     description="Log out user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="An error occurred while trying to log out. Please try again later.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        if (auth('sanctum')->user()) {
            auth('sanctum')->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }
}

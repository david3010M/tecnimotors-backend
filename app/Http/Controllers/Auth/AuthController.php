<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

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
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             @OA\Property( property="access_token", type="string", example="1|1a2b3c4d5e6f7g8h9i0j" ),
     *             @OA\Property( property="user", type="object", ref="#/components/schemas/User" ),
     *             @OA\Property( property="group_menus", type="array", @OA\Items( type="object", ref="#/components/schemas/GroupMenu" ) ),
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
        $credentials = $request->only('username', 'password');
        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required',
        ]);

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
            Auth::loginUsingId($user->id);

            // 🔹 Aquí guardamos el objeto completo
            $tokenResult = $user->createToken('auth_token');

            // 🔹 Este es el string que vas a devolver al cliente
            $plainTextToken = $tokenResult->plainTextToken;

            // 🔹 Este es el registro en la tabla personal_access_tokens
            $accessToken = $tokenResult->accessToken;
            $accessToken->expires_at = now()->addHour(8); // Para pruebas de 8 horas
            $accessToken->save();

            $user = User::with(['typeUser', 'worker'])->find($user->id);

            $typeUser = $user->typeUser;
            $groupMenu = GroupMenu::getFilteredGroupMenus($typeUser->id);

            $tipo = 'OTRS';
            $resultado = DB::select(
                'SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum 
             FROM attentions a WHERE SUBSTRING(number, 1, 4) = ?',
                [$tipo]
            )[0]->siguienteNum;

            $siguienteNum = (int) $resultado;

            return response()->json([
                'access_token' => $plainTextToken, 
                'expires_at' => $accessToken->expires_at->toDateTimeString(),
                'user' => $user,
                'correlativo' => $siguienteNum,
                'groupMenu' => $groupMenu,
            ]);
        } else {
            return response()->json([
                "error" => "Password Not Correct",
            ], 422);
        }
    }


    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/authenticate",
     *     summary="Get Profile user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     description="Get user",
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="User",
     *                 ref="#/components/schemas/User"
     *             ),
     *             @OA\Property(
     *                 property="menu",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     description="Menú"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
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
            $tokenValue = $request->bearerToken();

            if (!$tokenValue) {
                return response()->json(["message" => "Token no proporcionado"], 401);
            }

            $accessToken = PersonalAccessToken::findToken($tokenValue);

            if (!$accessToken) {
                return response()->json(["message" => "Token inválido"], 401);
            }

            // 🔎 Verificar si está vencido o revocado
            if (
                ($accessToken->expires_at && $accessToken->expires_at->isPast())
                || $accessToken->isRevoked()
            ) {
                $accessToken->revoked_at = now();
                $accessToken->save();

                return response()->json(["message" => "Token expirado o revocado, sesión cerrada"], 401);
            }

            // Usuario relacionado
            $user = $accessToken->tokenable()->with(['typeUser', 'worker'])->first();

            $groupMenu = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

            $tipo = 'OTRS';
            $resultado = DB::select(
                'SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum 
             FROM attentions a WHERE SUBSTRING(number, 1, 4) = ?',
                [$tipo]
            )[0]->siguienteNum;

            return response()->json([
                'access_token' => $tokenValue,
                'expires_at' => $accessToken->expires_at?->toDateTimeString(),
                'user' => $user,
                'correlativo' => (int) $resultado,
                'groupMenu' => $groupMenu,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error interno del servidor: " . $e->getMessage(),
            ], 500);
        }
    }



    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/logout",
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

    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile)) {
            $logs = File::get($logFile);
            $logLines = explode("\n", $logs);
            $errorLogs = array_filter($logLines, function ($line) {
                return strpos($line, 'ERROR') !== false;
            });
            $errorLogs = array_reverse($errorLogs);
            $errorObjects = array_map(function ($line) {
                preg_match('/^\[(.*?)\] (.*?)\.(.*?): (.*?)$/', $line, $matches);

                return [
                    'date' => $matches[1] ?? null,
                    'environment' => $matches[2] ?? null,
                    'error_type' => $matches[3] ?? null,
                    'message' => $matches[4] ?? $line,
                ];
            }, $errorLogs);

            return response()->json([
                'errors' => array_values($errorObjects)
            ]);
        }

        return response()->json([
            'message' => 'No logs found.'
        ], 404);
    }
}

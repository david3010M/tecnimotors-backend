<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/user/{id}",
     *     summary="Update user by ID",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of user",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"names","password"},
     *
     *              @OA\Property(property="username", type="string", example="admin", description="Usrname of the user"),
     *              @OA\Property(property="password", type="string", example="12345678", description="Password of the user"),

     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Typeuser or User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),

     * )
     *
     */
    public function update(Request $request, string $id)
    {
//        Find a user by ID
        $user = User::find($id);

//        If the user is not found, return a 404 response
        if (!$user) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }

//        Validate data
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cifrar la contraseña
        $hashedPassword = Hash::make($request->password);

//        Update with password hashed
        $user->update([
            'username' => $request->username,
            'password' => $hashedPassword,
        ]);

//        Return the user
        return $user;
    }

}

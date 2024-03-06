<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;

use Laravel\Sanctum\PersonalAccessToken;


class UserController extends Controller
{
    public function info($id)
    {
        return new UserCollection(User::where('id', auth()->user()->id)->get());
    }

    public function updateName(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'name' => $request->name
        ]);
        return response()->json([
            'message' => translate('Profile information has been updated successfully')
        ]);
    }

    public function getUserInfoByAccessToken(Request $request)
    {

        $false_response = [
            'result' => false,
            'id' => 0,
            'name' => "",
            'email' => "",
            'avatar' => "",
            'avatar_original' => "",
            'phone' => ""
        ];
        


        $token = PersonalAccessToken::findToken($request->access_token);
        if (!$token) {
            return response()->json($false_response);
        }
        
        $user = $token->tokenable;

        

        if ($user == null) {
            return response()->json($false_response);

        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged in'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => uploaded_asset($user->avatar_original),
                'phone' => $user->phone
            ]
        ]);

    }
}

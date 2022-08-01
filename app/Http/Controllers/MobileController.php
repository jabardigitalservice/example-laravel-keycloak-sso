<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class MobileController extends Controller
{
    public function getCurrentUserData(Request $request)
    {
        $token = $request->bearerToken();
        $decodedToken = parseJWTToken($token);

        $data_siap = getCurrentUserProfileFromSIAP($token);
        $data_web = User::where('nik', $decodedToken->nik)->first();

        return response()->json(compact('data_siap', 'data_web'));
    }
}

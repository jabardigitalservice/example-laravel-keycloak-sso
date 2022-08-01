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

        // Idealnya di sini ada validasi tambahan untuk access token yang masuk,
        // misalnya memeriksa nilai field `iss`, `aud`, dst.
        // referensi lebih dalam bisa cek: https://datatracker.ietf.org/doc/html/rfc8725#section-3

        $data_siap = getCurrentUserProfileFromSIAP($token);
        $data_web = User::where('nik', $decodedToken->nik)->first();

        return response()->json(compact('data_siap', 'data_web'));
    }
}

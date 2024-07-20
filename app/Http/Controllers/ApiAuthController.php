<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tokens;

use DateTimeImmutable;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;


class ApiAuthController extends Controller
{
    private function GenerateToken($user)
    {
        $secretKey  = env('JWT_KEY');
        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+32 day')->getTimestamp();      // Add 32 days
        $serverName = "localhost";
        $userID   = $user->id;                                           // Retrieved from filtered POST data

        // Create the token as an array
        $data = [
            'iat'  => $issuedAt->getTimestamp(),    // Issued at: time when the token was generated
            'jti'  => $tokenId,                     // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,                  // Issuer
            'nbf'  => $issuedAt->getTimestamp(),    // Not before
            'exp'  => $expire,                      // Expire
            'data' => [                             // Data related to the signer user
                'userID' => $userID,            // User name
            ]
        ];

        // Encode the array to a JWT string.
        $token = JWT::encode(
            $data,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );
        $rtoken =  $var = Str::random(45);

        $data=Tokens::create([
            'token' => $tokenId,
            'user_id' => $userID,
            'rememberToken' => $rtoken
        ]);
        return $token;
    }
    public function userCreate(Request $request){
        try {

            if($request->name && $request->email && $request->password){
                $user=User::where('email', $request->email)->first();
     
                if(!$user){
                    $data=User::create([
                        'name' => $request->name,
                        'password' => Hash::make($request->password),
                        'email' => $request->email
                    ]);
                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ], 400);
                }else{
                    return response()->json([
                        'success' => false
                    ], 409);
                }
            }else{
                return response()->json([
                    'success' => false
                ], 400);
            }

          } catch (Exception $e) {
                  return $e;
          }


        /*$data = HotspotStaffUsers::create([
            'TCorPass' => $request->TCorPass,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'userName' => $request->userName,
            'dateOfBirth' => $dateOfBirth,
            'companyId' => $request->CID,
            'speedProfile' => $request->speedProfile,
            'connectionLimit' => $request->totalConnection,
        ]);*/


    }
    public function userLogin(Request $request){
        try {

            if($request->email && $request->password){
                $user=User::where('email', $request->email)->first();
                if($user){
                    if(Hash::check($request->password , $user->password)){
                        $data=$this->GenerateToken($user);
                        return response()->json([
                            'success' => false,
                            'data' => $data,
                            'dataReq' => $request->All(),
                            'user' => $user
                        ], 200);
                    }else{
                        return response()->json([
                            'success' => false,
                            'data' => $user,
                            'dataReq' => $request->All()
                        ], 200);
                    }
                    $data=$this->GenerateToken($user);
                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ], 200);
                }else{
                    return response()->json([
                        'success' => false
                    ], 409);
                }
            }else{
                return response()->json([
                    'success' => false
                ], 400);
            }

          } catch (Exception $e) {
                  return $e;
          }


    }

    public function userMe(Request $request){

        return response()->json([
            'success' => true,
            'data' =>  $request->helpers['user']
        ], 200);
    }


}

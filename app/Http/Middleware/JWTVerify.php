<?php
namespace App\Http\Middleware;

use Closure;
use App\Helpers\PublicHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeImmutable;
use Exception;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use App\Models\Tokens;
use Carbon\Carbon;
use App\Models\User;


class JWTVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $publicHelper = new PublicHelper();

        try {
            $token = $publicHelper->GetAndDecodeJWT();
            $userID = $token->data->userID;
            $tokendata = Tokens::where('token', $token->jti)->where('user_id',$userID)->first();
            
            if($tokendata){
               Tokens::where('id', $tokendata->id)->update([
                    'last_activity' => Carbon::now()
               ]);
               $helper['user'] = User::find($userID);
               $helper['token'] = $token;

               $request->merge(['helpers' => $helper]);
            }else{
                return response()->json(['error' => 'unauthorized'], 401);
            }
            
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}

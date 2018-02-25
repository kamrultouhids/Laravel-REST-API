<?php

namespace App\Http\Middleware;

use Closure;

use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;



class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            $user = JWTAuth::toUser($request->input('token'));
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['Authentication Expired'], $e->getStatusCode());
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['Invalid Authentication'], $e->getStatusCode());
            }else{
                return response()->json(['error'=>'Authentication is required']);
            }
        }

        return $next($request);
    }
}

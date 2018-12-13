<?php

namespace App\Http\Middleware;

use Closure;

use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


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
            $token = JWTAuth::getToken();
            if ($token) {
                if (! $user = JWTAuth::parseToken()->authenticate() ) {
                    return response()->json(['user_not_found'], 200);
                }
            }else{
                return response()->json([ 'error' => 'token not provided', ]);
            }
        } catch (TokenExpiredException $e) {
            // If the token is expired, then it will be refreshed and added to the headers
            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($refreshed)->toUser();
                header('Authorization: Bearer ' . $refreshed);
            } catch (JWTException $e) {
                return response()->json(['status' => 'error', 'msg' => 'something went wrong', ]);
            }catch (TokenInvalidException $e) {
                return response()->json(['status' => 'error', 'msg' => 'token invalid', ]);
            }
        }catch (JWTException $e) {
            return response()->json(['status' => 'error', 'msg' => 'something went wrong', ]);
        }catch (TokenInvalidException $e) {
            return response()->json(['status' => 'error', 'msg' => 'token invalid', ]);
        }


        return $next($request);
    }
}

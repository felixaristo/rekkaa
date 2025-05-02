<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class AccessApiOperational
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
        $exclude_url = ['login'];
        if(in_array($request->segment(3), $exclude_url)) {
            return $next($request);   
        }
        try {
            $token = JWTAuth::getToken();
            $decode = JWTAuth::getPayload($token)->toArray();
            $request->attributes->set('data', $decode['data']);
            return $next($request);
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Token Expired.',
            ], 500);
    
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
    
            return response()->json([
                'success' => false,
                'message' => 'Token Invalid.',
            ], 500);
    
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
    
            return response()->json([
                'success' => false,
                'message' => 'Token Absent.',
            ], 500);
    
        }
    }
}

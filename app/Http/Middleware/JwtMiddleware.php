<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Http\Request;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Token is Invalid";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Token is Expired";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 401);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Authorization Token not found";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 401);
            }
        }
        return $next($request);
    }
}

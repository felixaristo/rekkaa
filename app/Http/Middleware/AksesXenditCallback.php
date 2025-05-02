<?php

namespace App\Http\Middleware;

use Closure;

class AksesXenditCallback
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
        $xendiclbktoken = $request->header('X-CALLBACK-TOKEN');
        // dd($xendiclbktoken);
        if($xendiclbktoken !== env('XENDIT_CALLBACK_TOKEN')) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden!'
            ], 400);
        }
        return $next($request);
    }
}
